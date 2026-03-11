<?php

$method = "GET";
$cache  = "no-cache";
include "../../head.php";


// CHECK BOOKING STATUS


if (isset($_GET['user_id']) && isset($_GET['event_id'])) {

    $user_id  = cleanme(trim($_GET['user_id']));
    $event_id = cleanme(trim($_GET['event_id']));

    
    // VALIDATION
    

    if (input_is_invalid($user_id) || input_is_invalid($event_id)) {

        respondBadRequest("User ID and Event ID are required.");

    } else if (!is_numeric($user_id) || !is_numeric($event_id)) {

        respondBadRequest("User ID and Event ID must be numeric.");

    }

    
    // FETCH BOOKING STATUS
    

    $query = $connect->prepare("
        SELECT id, user_id, event_id, number_of_tickets, total_amount, booking_status, booked_at
        FROM bookings
        WHERE user_id = ? AND event_id = ?
        LIMIT 1
    ");

    $query->bind_param("ii", $user_id, $event_id);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows > 0) {

        $booking = $result->fetch_assoc();

        respondOK($booking, "Booking status fetched successfully.");

    } else {

        respondNotFound([], "No booking found for this user and event.");

    }

} else {

    respondBadRequest("Missing user_id or event_id parameter.");

}

?>