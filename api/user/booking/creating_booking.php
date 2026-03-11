<?php
$method = "POST";
$cache  = "no-cache";
include "../../head.php";

// if (isset($_POST['event_id'], $_POST['number_of_tickets'], $_POST['total_amount'])) {

//     $event_id          = cleanme(trim($_POST['event_id']));
//     $number_of_tickets = cleanme(trim($_POST['number_of_tickets']));
//     $total_amount      = cleanme(trim($_POST['total_amount']));

//     // Default booking status
//     $booking_status = "pending";

//     $datasentin = ValidateAPITokenSentIN();
//     $user_id = $datasentin->usertoken;





//     // ======================
//     // VALIDATION SECTION
//     // ======================

//     if (input_is_invalid($user_id) || input_is_invalid($event_id) || 
//         input_is_invalid($number_of_tickets) || input_is_invalid($total_amount)) {

//         respondBadRequest("All fields are required.");

//     } else if (!is_numeric($user_id) || !is_numeric($event_id)) {

//         respondBadRequest("User ID and Event ID must be numeric.");

//     } else if (!is_numeric($number_of_tickets) || $number_of_tickets <= 0) {

//         respondBadRequest("Number of tickets must be a positive number.");

//     } else if (!is_numeric($total_amount) || $total_amount <= 0) {

//         respondBadRequest("Total amount must be a valid positive number.");

//     } else if (!in_array($booking_status, ['pending','confirmed','cancelled'])) {

//         respondBadRequest("Invalid booking status.");

//     } else {

//         // ======================
//         // OPTIONAL: CHECK USER EXISTS
//         // ======================

//         $checkUser = $connect->prepare("SELECT id FROM users WHERE id = ?");
//         $checkUser->bind_param("i", $user_id);
//         $checkUser->execute();
//         $userResult = $checkUser->get_result();

//         if ($userResult->num_rows == 0) {

//             respondBadRequest("User does not exist.");

//         } else {

//             // ======================
//             // OPTIONAL: CHECK EVENT EXISTS
//             // ======================

//             $checkEvent = $connect->prepare("SELECT id FROM events WHERE id = ?");
//             $checkEvent->bind_param("i", $event_id);
//             $checkEvent->execute();
//             $eventResult = $checkEvent->get_result();

//             if ($eventResult->num_rows == 0) {

//                 respondBadRequest("Event does not exist.");

//             } else {

//                 // ======================
//                 // INSERT BOOKING
//                 // ======================

//                 $insertBooking = $connect->prepare("
//                     INSERT INTO bookings 
//                     (user_id, event_id, number_of_tickets, total_amount, booking_status)
//                     VALUES (?, ?, ?, ?, ?)
//                 ");

//                 $insertBooking->bind_param(
//                     "iiids",
//                     $user_id,
//                     $event_id,
//                     $number_of_tickets,
//                     $total_amount,
//                     $booking_status
//                 );

//                 $insertBooking->execute();

//                 if ($insertBooking->affected_rows > 0) {

//                     $booking_id = $connect->insert_id;

//                     $getBooking = $connect->prepare("
//                         SELECT * FROM bookings WHERE id = ?
//                     ");
//                     $getBooking->bind_param("i", $booking_id);
//                     $getBooking->execute();
//                     $bookingDetails = $getBooking->get_result()->fetch_assoc();

//                     respondOK([], "Booking created successfully");

//                 } else {
//                     respondBadRequest("Booking failed.");
//                 }
//             }
//         }
//     }

// } else {
//     respondBadRequest("Invalid request. Required fields missing.");
// }



// if (isset($_POST['event_id'], $_POST['number_of_tickets'], $_POST['total_amount'])) {

//     $event_id          = cleanme(trim($_POST['event_id']));
//     $number_of_tickets = cleanme(trim($_POST['number_of_tickets']));
//     $total_amount      = cleanme(trim($_POST['total_amount']));

//     // Default booking status
//     $booking_status = "pending";

//     $datasentin = ValidateAPITokenSentIN();
//     $user_id = $datasentin->usertoken;

//     // ======================
//     // VALIDATION SECTION
//     // ======================
//     if (input_is_invalid($user_id) || input_is_invalid($event_id) || 
//         input_is_invalid($number_of_tickets) || input_is_invalid($total_amount)) {

//         respondBadRequest("All fields are required.");

//     } else if (!is_numeric($user_id) || !is_numeric($event_id)) {

//         respondBadRequest("User ID and Event ID must be numeric.");

//     } else if (!is_numeric($number_of_tickets) || $number_of_tickets <= 0) {

//         respondBadRequest("Number of tickets must be a positive number.");

//     } else if (!is_numeric($total_amount) || $total_amount <= 0) {

//         respondBadRequest("Total amount must be a valid positive number.");

//     } else if (!in_array($booking_status, ['pending','confirmed','cancelled'])) {

//         respondBadRequest("Invalid booking status.");

//     } else {

//         // ======================
//         // CHECK USER EXISTS
//         // ======================
//         $checkUser = $connect->prepare("SELECT id FROM users WHERE id = ?");
//         $checkUser->bind_param("i", $user_id);
//         $checkUser->execute();
//         $userResult = $checkUser->get_result();

//         if ($userResult->num_rows == 0) {
//             respondBadRequest("User does not exist.");
//         }

//         // ======================
//         // CHECK EVENT EXISTS AND AVAILABLE SEATS
//         // ======================
//         $checkEvent = $connect->prepare("SELECT id, total_seats FROM events WHERE id = ?");
//         $checkEvent->bind_param("i", $event_id);
//         $checkEvent->execute();
//         $eventResult = $checkEvent->get_result();

//         if ($eventResult->num_rows == 0) {
//             respondBadRequest("Event does not exist.");
//         }

//         $event = $eventResult->fetch_assoc();
//         $totalSeats = $event['total_seats'];

//         // Get already booked seats
//         $checkSeats = $connect->prepare("SELECT SUM(number_of_tickets) as booked_seats FROM bookings WHERE event_id = ?");
//         $checkSeats->bind_param("i", $event_id);
//         $checkSeats->execute();
//         $seatResult = $checkSeats->get_result()->fetch_assoc();
//         $bookedSeats = $seatResult['booked_seats'] ?? 0;

//         $availableSeats = $totalSeats - $bookedSeats;

//         if ($availableSeats <= 0) {
//             respondBadRequest("No seats available for this event.");
//         }

//         if ($number_of_tickets > $availableSeats) {
//             respondBadRequest("Only $availableSeats seats are available.");
//         }

//         // ======================
//         // PREVENT MULTIPLE BOOKINGS FOR SAME USER
//         // ======================
//         // $checkUserBooking = $connect->prepare("SELECT id FROM bookings WHERE user_id = ? AND event_id = ?");
//         // $checkUserBooking->bind_param("ii", $user_id, $event_id);
//         // $checkUserBooking->execute();
//         // $userBookingResult = $checkUserBooking->get_result();

//         // if ($userBookingResult->num_rows > 0) {
//         //     respondBadRequest("You have already booked for this event.");
//         // }

//         // ======================
//         // ALLOCATE SEAT NUMBERS
//         // ======================
//         $seatNumbers = [];
//         $lastSeatQuery = $connect->prepare("SELECT MAX(seat_number) as last_seat FROM bookings WHERE event_id = ?");
//         $lastSeatQuery->bind_param("i", $event_id);
//         $lastSeatQuery->execute();
//         $lastSeat = $lastSeatQuery->get_result()->fetch_assoc()['last_seat'] ?? 0;

//         for ($i = 1; $i <= $number_of_tickets; $i++) {
//             $seatNumbers[] = $lastSeat + $i;
//         }
//         $seatNumbersStr = implode(',', $seatNumbers); // store as comma-separated

//         // ======================
//         // INSERT BOOKING
//         // ======================
//         $insertBooking = $connect->prepare("
//             INSERT INTO bookings 
//             (user_id, event_id, number_of_tickets, total_amount, booking_status, seat_number)
//             VALUES (?, ?, ?, ?, ?, ?)
//         ");
//         $insertBooking->bind_param(
//             "iiidss",
//             $user_id,
//             $event_id,
//             $number_of_tickets,
//             $total_amount,
//             $booking_status,
//             $seatNumbersStr
//         );

//         $insertBooking->execute();

//         if ($insertBooking->affected_rows > 0) {
//             respondOK(['seat_numbers' => $seatNumbers], "Booking created successfully");
//         } else {
//             respondBadRequest("Booking failed.");
//         }
//     }

// } else {
//     respondBadRequest("Invalid request. Required fields missing.");
// }



if (isset($_POST['event_id'])) {

    $event_id = cleanme(trim($_POST['event_id']));

    $booking_status = "pending";

    $datasentin = ValidateAPITokenSentIN();
    $user_id = $datasentin->usertoken;

    // ======================
    // VALIDATION SECTION
    // ======================
    if (input_is_invalid($user_id) || input_is_invalid($event_id)) {

        respondBadRequest("User ID and Event ID are required.");

    } else if (!is_numeric($user_id) || !is_numeric($event_id)) {

        respondBadRequest("User ID and Event ID must be numeric.");

    } else {

        // ======================
        // CHECK USER EXISTS
        // ======================
        $checkUser = $connect->prepare("SELECT id FROM users WHERE id = ?");
        $checkUser->bind_param("i", $user_id);
        $checkUser->execute();
        $userResult = $checkUser->get_result();

        if ($userResult->num_rows == 0) {
            respondBadRequest("User does not exist.");
        }

        // ======================
        // CHECK EVENT EXISTS
        // ======================
        $checkEvent = $connect->prepare("
            SELECT id, total_seats, available_seats 
            FROM events 
            WHERE id = ?
        ");

        $checkEvent->bind_param("i", $event_id);
        $checkEvent->execute();
        $eventResult = $checkEvent->get_result();

        if ($eventResult->num_rows == 0) {
            respondBadRequest("Event does not exist.");
        }

        $event = $eventResult->fetch_assoc();
        $availableSeats = $event['available_seats'];

        if ($availableSeats <= 0) {
            respondBadRequest("No seats available for this event.");
        }

        // ======================
        // PREVENT DUPLICATE BOOKING
        // ======================
        $checkBooking = $connect->prepare("
            SELECT id 
            FROM bookings 
            WHERE user_id = ? AND event_id = ?
        ");

        $checkBooking->bind_param("ii", $user_id, $event_id);
        $checkBooking->execute();
        $bookingResult = $checkBooking->get_result();

        if ($bookingResult->num_rows > 0) {
            respondBadRequest("You have already booked this event.");
        }

        // ======================
        // GENERATE NEXT SEAT NUMBER
        // ======================
        $seatQuery = $connect->prepare("
            SELECT MAX(seat_number) as last_seat 
            FROM bookings 
            WHERE event_id = ?
        ");

        $seatQuery->bind_param("i", $event_id);
        $seatQuery->execute();

        $seatResult = $seatQuery->get_result()->fetch_assoc();
        $seat_number = ($seatResult['last_seat'] ?? 0) + 1;

        // ======================
        // INSERT BOOKING
        // ======================
        $insertBooking = $connect->prepare("
            INSERT INTO bookings 
            (user_id, event_id, seat_number, booking_status)
            VALUES (?, ?, ?, ?)
        ");

        $insertBooking->bind_param(
            "iiis",
            $user_id,
            $event_id,
            $seat_number,
            $booking_status
        );

        $insertBooking->execute();

        if ($insertBooking->affected_rows > 0) {

            // ======================
            // UPDATE AVAILABLE SEATS
            // ======================
            $updateSeats = $connect->prepare("
                UPDATE events 
                SET available_seats = available_seats - 1 
                WHERE id = ?
            ");

            $updateSeats->bind_param("i", $event_id);
            $updateSeats->execute();

            respondOK([
                'seat_number' => $seat_number
            ], "Booking created successfully");

        } else {

            respondBadRequest("Booking failed.");

        }

    }

} else {

    respondBadRequest("Invalid request. Event ID is required.");

}

 ?>