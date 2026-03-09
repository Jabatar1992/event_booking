<?php
$method = "POST";
$cache  = "no-cache";
include "../../head.php";

if (isset($_POST['event_id'], $_POST['number_of_tickets'], $_POST['total_amount'])) {

    $event_id          = cleanme(trim($_POST['event_id']));
    $number_of_tickets = cleanme(trim($_POST['number_of_tickets']));
    $total_amount      = cleanme(trim($_POST['total_amount']));

    // Default booking status
    $booking_status = "pending";

    $datasentin = ValidateAPITokenSentIN();
    $user_id = $datasentin->usertoken;





    // ======================
    // VALIDATION SECTION
    // ======================

    if (input_is_invalid($user_id) || input_is_invalid($event_id) || 
        input_is_invalid($number_of_tickets) || input_is_invalid($total_amount)) {

        respondBadRequest("All fields are required.");

    } else if (!is_numeric($user_id) || !is_numeric($event_id)) {

        respondBadRequest("User ID and Event ID must be numeric.");

    } else if (!is_numeric($number_of_tickets) || $number_of_tickets <= 0) {

        respondBadRequest("Number of tickets must be a positive number.");

    } else if (!is_numeric($total_amount) || $total_amount <= 0) {

        respondBadRequest("Total amount must be a valid positive number.");

    } else if (!in_array($booking_status, ['pending','confirmed','cancelled'])) {

        respondBadRequest("Invalid booking status.");

    } else {

        // ======================
        // OPTIONAL: CHECK USER EXISTS
        // ======================

        $checkUser = $connect->prepare("SELECT id FROM users WHERE id = ?");
        $checkUser->bind_param("i", $user_id);
        $checkUser->execute();
        $userResult = $checkUser->get_result();

        if ($userResult->num_rows == 0) {

            respondBadRequest("User does not exist.");

        } else {

            // ======================
            // OPTIONAL: CHECK EVENT EXISTS
            // ======================

            $checkEvent = $connect->prepare("SELECT id FROM events WHERE id = ?");
            $checkEvent->bind_param("i", $event_id);
            $checkEvent->execute();
            $eventResult = $checkEvent->get_result();

            if ($eventResult->num_rows == 0) {

                respondBadRequest("Event does not exist.");

            } else {

                // ======================
                // INSERT BOOKING
                // ======================

                $insertBooking = $connect->prepare("
                    INSERT INTO bookings 
                    (user_id, event_id, number_of_tickets, total_amount, booking_status)
                    VALUES (?, ?, ?, ?, ?)
                ");

                $insertBooking->bind_param(
                    "iiids",
                    $user_id,
                    $event_id,
                    $number_of_tickets,
                    $total_amount,
                    $booking_status
                );

                $insertBooking->execute();

                if ($insertBooking->affected_rows > 0) {

                    $booking_id = $connect->insert_id;

                    $getBooking = $connect->prepare("
                        SELECT * FROM bookings WHERE id = ?
                    ");
                    $getBooking->bind_param("i", $booking_id);
                    $getBooking->execute();
                    $bookingDetails = $getBooking->get_result()->fetch_assoc();

                    respondOK([], "Booking created successfully");

                } else {
                    respondBadRequest("Booking failed.");
                }
            }
        }
    }

} else {
    respondBadRequest("Invalid request. Required fields missing.");
}