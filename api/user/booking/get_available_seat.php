<?php
$method = "GET";
$cache  = "no-cache";
include "../../head.php";

if (isset($_GET['event_id'])) {

    $event_id = cleanme(trim($_GET['event_id']));


    // VALIDATION SECTION
    
    if (input_is_invalid($event_id)) {

        respondBadRequest("Event ID is required.");

    } else if (!is_numeric($event_id)) {

        respondBadRequest("Event ID must be numeric.");

    } else {

        
        // CHECK EVENT EXISTS
        
        $checkEvent = $connect->prepare("
            SELECT id, title, total_seats, available_seats
            FROM events
            WHERE id = ?
        ");

        $checkEvent->bind_param("i", $event_id);
        $checkEvent->execute();
        $eventResult = $checkEvent->get_result();

        if ($eventResult->num_rows == 0) {

            respondBadRequest("Event does not exist.");

        } else {

            $event = $eventResult->fetch_assoc();

            respondOK([
                "event_id" => $event['id'],
                "event_title" => $event['title'],
                "total_seats" => $event['total_seats'],
                "available_seats" => $event['available_seats']
            ], "Available seats fetched successfully");

        }

    }

} else {

    respondBadRequest("Event ID parameter is required.");

}
?>