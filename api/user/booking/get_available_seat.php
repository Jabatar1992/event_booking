<?php
$method = "GET";
$cache  = "no-cache";
include "../../head.php";

// if (isset($_GET['event_id'])) {

//     $event_id = cleanme(trim($_GET['event_id']));

//     if (input_is_invalid($event_id) || !is_numeric($event_id)) {
//         respondBadRequest("Invalid event ID.");
//     }

//     // ======================
//     // CHECK EVENT EXISTS
//     // ======================
//     $checkEvent = $connect->prepare("SELECT id, total_seats FROM events WHERE id = ?");
//     $checkEvent->bind_param("i", $event_id);
//     $checkEvent->execute();
//     $eventResult = $checkEvent->get_result();

//     if ($eventResult->num_rows == 0) {
//         respondBadRequest("Event does not exist.");
//     }

//     $event = $eventResult->fetch_assoc();
//     $totalSeats = $event['total_seats'];

//     // ======================
//     // CALCULATE ALREADY BOOKED SEATS
//     // ======================
//     $checkSeats = $connect->prepare("SELECT SUM(number_of_tickets) as booked_seats FROM bookings WHERE event_id = ?");
//     $checkSeats->bind_param("i", $event_id);
//     $checkSeats->execute();
//     $seatResult = $checkSeats->get_result()->fetch_assoc();

//     $bookedSeats = $seatResult['booked_seats'] ?? 0;
//     $availableSeats = $totalSeats - $bookedSeats;

//     respondOK([
//         'event_id' => $event_id,
//         'total_seats' => $totalSeats,
//         'booked_seats' => $bookedSeats,
//         'available_seats' => $availableSeats
//     ], "Available seats fetched successfully.");

// } else {
//     respondBadRequest("Missing event_id parameter.");
// }


// if (!isset($_GET['event_id'])) {
//     respondBadRequest("Missing event_id parameter.");
// }

// $event_id = cleanme(trim($_GET['event_id']));

// if (input_is_invalid($event_id) || !is_numeric($event_id)) {
//     respondBadRequest("Invalid event ID.");
// }

// // ======================
// // CHECK IF EVENT EXISTS
// // ======================
// $stmtEvent = $connect->prepare("SELECT id, total_seats FROM events WHERE id = ?");
// $stmtEvent->bind_param("i", $event_id);
// $stmtEvent->execute();
// $resultEvent = $stmtEvent->get_result();

// if ($resultEvent->num_rows === 0) {
//     respondBadRequest("Event does not exist.");
// }

// $event = $resultEvent->fetch_assoc();
// $totalSeats = (int)$event['total_seats'];

// // ======================
// // CALCULATE AVAILABLE SEATS
// // ======================
// $stmtBooked = $connect->prepare("SELECT SUM(number_of_tickets) as booked_seats FROM bookings WHERE event_id = ?");
// $stmtBooked->bind_param("i", $event_id);
// $stmtBooked->execute();
// $resultBooked = $stmtBooked->get_result()->fetch_assoc();

// $bookedSeats = (int)($resultBooked['booked_seats'] ?? 0);

// // Total seats minus already booked seats
// $availableSeats = $totalSeats - $bookedSeats;

// // ======================
// // RESPOND WITH DATA
// // ======================
// respondOK([
//     'event_id' => $event_id,
//     'total_seats' => $totalSeats,
//     'booked_seats' => $bookedSeats,
//     'available_seats' => $availableSeats
// ], "Available seats fetched successfully.");






if (!isset($_GET['event_id'])) {
    respondBadRequest("Missing event_id parameter.");
}

$event_id = cleanme(trim($_GET['event_id']));

if (input_is_invalid($event_id) || !is_numeric($event_id)) {
    respondBadRequest("Invalid event ID.");
}

// ======================
// CHECK IF EVENT EXISTS
// ======================
$stmtEvent = $connect->prepare("SELECT id, total_seats FROM events WHERE id = ?");
$stmtEvent->bind_param("i", $event_id);
$stmtEvent->execute();
$resultEvent = $stmtEvent->get_result();

if ($resultEvent->num_rows === 0) {
    respondBadRequest("Event does not exist.");
}

$event = $resultEvent->fetch_assoc();
$totalSeats = (int)$event['total_seats'];

// ======================
// CALCULATE AVAILABLE SEATS
// ======================
$stmtBooked = $connect->prepare("SELECT SUM(number_of_tickets) as booked_seats FROM bookings WHERE event_id = ?");
$stmtBooked->bind_param("i", $event_id);
$stmtBooked->execute();
$resultBooked = $stmtBooked->get_result()->fetch_assoc();

$bookedSeats = (int)($resultBooked['booked_seats'] ?? 0);

// Available seats = total seats - booked seats
$availableSeats = $totalSeats - $bookedSeats;

// ======================
// RESPOND WITH AVAILABLE SEATS ONLY
// ======================
respondOK([
    'event_id' => $event_id,
    'available_seats' => $availableSeats
], "Available seats fetched successfully.");
?>