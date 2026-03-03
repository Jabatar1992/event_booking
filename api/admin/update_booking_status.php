<?php
$method="POST";
$cache="no-cache";
include "../head.php";

if (isset($_POST['booking_id'], $_POST['booking_status'])) {

    $booking_id     = cleanme(trim($_POST['booking_id']));
    $booking_status = cleanme(trim($_POST['booking_status']));

    // Validation using else-if structure
    if (input_is_invalid($booking_id) || input_is_invalid($booking_status)) {
        respondBadRequest("Booking ID and booking status are required.");
        exit;

    } else if (!is_numeric($booking_id) || $booking_id <= 0) {
        respondBadRequest("Invalid booking ID.");
        exit;

    } else if (
        $booking_status !== 'pending' &&
        $booking_status !== 'confirmed' &&
        $booking_status !== 'cancelled'
    ) {
        respondBadRequest("Invalid booking status value.");
        exit;
    }

    // Check if booking exists
    $checkBooking = $connect->prepare("
        SELECT id, user_id, event_id, number_of_tickets, total_amount, booking_status, booked_at
        FROM bookings
        WHERE id = ?
    ");
    $checkBooking->bind_param("i", $booking_id);
    $checkBooking->execute();
    $result = $checkBooking->get_result();

    if ($result->num_rows == 0) {
        respondBadRequest("Booking not found.");
        exit;
    }

    // Update booking status
    $updateBooking = $connect->prepare("
        UPDATE bookings
        SET booking_status = ?
        WHERE id = ?
    ");
    $updateBooking->bind_param("si", $booking_status, $booking_id);
    $updateBooking->execute();

    if ($updateBooking->affected_rows > 0) {

        // Fetch updated booking
        $getBooking = $connect->prepare("
            SELECT id, user_id, event_id, number_of_tickets, total_amount, booking_status, booked_at
            FROM bookings
            WHERE id = ?
        ");
        $getBooking->bind_param("i", $booking_id);
        $getBooking->execute();
        $bookingDetails = $getBooking->get_result()->fetch_assoc();

        respondOK($bookingDetails, "Booking status updated successfully");

    } else {
        respondBadRequest("No changes made or failed to update booking status.");
    }

} else {
    respondBadRequest("Invalid request. Booking ID and booking status are required.");
}
?>