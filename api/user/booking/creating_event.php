<?php
$method = "POST";
$cache  = "no-cache";
include "../../head.php";

if (
    isset(
        $_POST['title'],
        $_POST['event_date'],
        $_POST['event_time'],
        $_POST['total_seats'],
        $_POST['price']
    )
) {

    $title       = cleanme(trim($_POST['title']));
    $description = isset($_POST['description']) ? cleanme(trim($_POST['description'])) : null;
    $location    = isset($_POST['location']) ? cleanme(trim($_POST['location'])) : null;
    $event_date  = cleanme(trim($_POST['event_date']));
    $event_time  = cleanme(trim($_POST['event_time']));
    $total_seats = cleanme(trim($_POST['total_seats']));
    $price       = cleanme(trim($_POST['price']));

    // ======================
    // VALIDATION SECTION
    // ======================

    if (
        input_is_invalid($title) ||
        input_is_invalid($event_date) ||
        input_is_invalid($event_time) ||
        input_is_invalid($total_seats) ||
        input_is_invalid($price)
    ) {

        respondBadRequest("All required fields must be filled.");

    } else if (!is_numeric($total_seats) || $total_seats <= 0) {

        respondBadRequest("Total seats must be a positive number.");

    } else if (!is_numeric($price) || $price <= 0) {

        respondBadRequest("Price must be a valid positive number.");

    } else {

        // ======================
        // CHECK DUPLICATE EVENT
        // ======================

        $checkEvent = $connect->prepare("
            SELECT id FROM events 
            WHERE title = ? 
            AND event_date = ? 
            AND event_time = ?
        ");

        $checkEvent->bind_param(
            "sss",
            $title,
            $event_date,
            $event_time
        );

        $checkEvent->execute();
        $eventResult = $checkEvent->get_result();

        if ($eventResult->num_rows > 0) {

            respondBadRequest("This event already exists.");

        } else {

            // ======================
            // INSERT EVENT
            // ======================

            $available_seats = $total_seats;

            $insertEvent = $connect->prepare("
                INSERT INTO events
                (title, description, location, event_date, event_time, total_seats, available_seats, price)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");

            $insertEvent->bind_param(
                "sssss iid",
                $title,
                $description,
                $location,
                $event_date,
                $event_time,
                $total_seats,
                $available_seats,
                $price
            );

            $insertEvent->execute();

            if ($insertEvent->affected_rows > 0) {

                $event_id = $connect->insert_id;

                $getEvent = $connect->prepare("
                    SELECT * FROM events WHERE id = ?
                ");
                $getEvent->bind_param("i", $event_id);
                $getEvent->execute();
                $eventDetails = $getEvent->get_result()->fetch_assoc();

                respondOK($eventDetails, "Event created successfully");

            } else {
                respondBadRequest("Event creation failed.");
            }
        }
    }

} else {
    respondBadRequest("Invalid request. Required fields missing.");
}