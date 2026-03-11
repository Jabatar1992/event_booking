<?php
$method = "POST";
$cache  = "no-cache";
include "../../head.php";

if (isset($_POST['user_id'])) {

    $user_id = cleanme(trim($_POST['user_id']));


    
    // VALIDATION SECTION
    

    if (input_is_invalid($user_id)) {

        respondBadRequest("User ID is required.");

    } else if (!is_numeric($user_id)) {

        respondBadRequest("User ID must be numeric.");

    } else {

        
        // CHECK USER EXISTS
        

        $checkUser = $connect->prepare("SELECT id FROM users WHERE id = ?");
        $checkUser->bind_param("i", $user_id);
        $checkUser->execute();
        $userResult = $checkUser->get_result();

        if ($userResult->num_rows == 0) {

            respondBadRequest("User does not exist.");

        } else {

            
            // GET USER BOOKINGS
            

            $getBookings = $connect->prepare("
                SELECT * FROM bookings 
                WHERE user_id = ?
                ORDER BY id DESC
            ");

            $getBookings->bind_param("i", $user_id);
            $getBookings->execute();
            $result = $getBookings->get_result();

            if ($result->num_rows > 0) {

                $bookings = [];

                while ($row = $result->fetch_assoc()) {
                    $bookings[] = $row;
                }

                respondOK($bookings, "User bookings retrieved successfully");

            } else {

                respondOK([], "No bookings found for this user.");
            }
        }
    }

} else {

    respondBadRequest("Invalid request. User ID is required.");
}