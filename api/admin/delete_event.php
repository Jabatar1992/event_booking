<?php
$method = "POST";
$cache  = "no-cache";
include "../head.php";

if (isset($_POST['event_id'])) {

    $event_id = cleanme(trim($_POST['event_id']));

    // ======================
    // VALIDATION SECTION
    // ======================

    if (input_is_invalid($event_id)) {

        respondBadRequest("Event ID is required.");

    } else if (!is_numeric($event_id) || $event_id <= 0) {

        respondBadRequest("Invalid Event ID.");

    } else {

        // ======================
        // CHECK IF EVENT EXISTS
        // ======================

        $checkEvent = $connect->prepare("
            SELECT id FROM events WHERE id = ?
        ");

        $checkEvent->bind_param("i", $event_id);
        $checkEvent->execute();
        $result = $checkEvent->get_result();

        if ($result->num_rows === 0) {

            respondBadRequest("Event not found.");

        } else {

            // ======================
            // DELETE EVENT
            // ======================

            $deleteEvent = $connect->prepare("
                DELETE FROM events WHERE id = ?
            ");

            $deleteEvent->bind_param("i", $event_id);
            $deleteEvent->execute();

            if ($deleteEvent->affected_rows > 0) {

                respondOK([], "Event deleted successfully.");

            } else {

                respondBadRequest("Event deletion failed.");

            }
        }
    }

} else {

    respondBadRequest("Invalid request. Event ID missing.");

}