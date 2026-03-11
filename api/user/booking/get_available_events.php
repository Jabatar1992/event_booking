<?php

$method = "GET";
$cache  = "no-cache";
include "../../head.php";


// FETCH AVAILABLE EVENTS


$query = $connect->prepare("
    SELECT 
        id,
        title,
        description,
        location,
        event_date,
        event_time,
        total_seats,
        available_seats,
        price
    FROM events
    WHERE available_seats > 0
    ORDER BY event_date ASC
");

$query->execute();
$result = $query->get_result();

$events = [];


// LOOP THROUGH RESULTS


while ($row = $result->fetch_assoc()) {
    $events[] = $row;
}


// RESPONSE


if (count($events) > 0) {

    respondOK($events, "Available events fetched successfully.");

} else {

    respondOK([], "No available events found.");

}