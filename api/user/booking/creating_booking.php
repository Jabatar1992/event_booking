<?php
$method = "POST";
$cache  = "no-cache";
include "../../head.php";



if (isset($_POST['event_id'])) {

    $event_id = cleanme(trim($_POST['event_id']));

    $booking_status = "pending";

    $datasentin = ValidateAPITokenSentIN();
    $user_id = $datasentin->usertoken;

    
    // VALIDATION SECTION
    
    if (input_is_invalid($user_id) || input_is_invalid($event_id)) {

        respondBadRequest("User ID and Event ID are required.");

    } else if (!is_numeric($user_id) || !is_numeric($event_id)) {

        respondBadRequest("User ID and Event ID must be numeric.");

    } else {

        
        // CHECK USER EXISTS
        
        $checkUser = $connect->prepare("SELECT id FROM users WHERE id = ?");
        $checkUser->bind_param("i", $user_id);
        $checkUser->execute();
        $userResult = $checkUser->get_result();

        if ($userResult->num_rows == 0) {
            respondBadRequest("User does not exist.");
        }

        
        // CHECK EVENT EXISTS
        
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

        
        // PREVENT DUPLICATE BOOKING
        
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

        
        // GENERATE NEXT SEAT NUMBER
        
        $seatQuery = $connect->prepare("
            SELECT MAX(seat_number) as last_seat 
            FROM bookings 
            WHERE event_id = ?
        ");

        $seatQuery->bind_param("i", $event_id);
        $seatQuery->execute();

        $seatResult = $seatQuery->get_result()->fetch_assoc();
        $seat_number = ($seatResult['last_seat'] ?? 0) + 1;

    
        // INSERT BOOKING
        
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

            
            // UPDATE AVAILABLE SEATS
            
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