<?php

$method="POST";
$cache="no-cache";
include "../../head.php";

if (isset($_POST['fullname'], $_POST['email'], $_POST['password'])) {

    $fullname = cleanme(trim($_POST['fullname']));
    $email    = cleanme(trim($_POST['email']));
    $phone    = isset($_POST['phone']) ? cleanme(trim($_POST['phone'])) : null;
    $password = cleanme(trim($_POST['password']));

    // ======================
    // VALIDATION SECTION
    // ======================

    if (input_is_invalid($fullname) || input_is_invalid($email) || input_is_invalid($password)) {
        respondBadRequest("Fullname, Email and Password are required.");

    } else if (strlen($fullname) < 3) {
        respondBadRequest("Fullname must be at least 3 characters.");

    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        respondBadRequest("Invalid email format.");

    } else if (!is_null($phone) && !is_numeric($phone)) {
        respondBadRequest("Phone number must contain only numbers.");

    } else if (strlen($password) < 8) {
        respondBadRequest("Password must be at least 8 characters.");

    } else if (!preg_match("/[A-Z]/", $password)) {
        respondBadRequest("Password must contain at least one uppercase letter.");

    } else if (!preg_match("/[\W]/", $password)) {
        respondBadRequest("Password must contain at least one special character.");

    } else {

        // ======================
        // CHECK IF EMAIL EXISTS
        // ======================

        $checkUser = $connect->prepare("SELECT id FROM users WHERE email = ?");
        $checkUser->bind_param("s", $email);
        $checkUser->execute();
        $result = $checkUser->get_result();

        if ($result->num_rows > 0) {

            respondBadRequest("User with this email already exists.");

        } else {

            // ======================
            // INSERT NEW USER
            // ======================

            $insertUser = $connect->prepare("
                INSERT INTO users (fullname, email, phone, password)
                VALUES (?, ?, ?, ?)
            ");

            $insertUser->bind_param("ssss", $fullname, $email, $phone, $password);
            $insertUser->execute();

            if ($insertUser->affected_rows > 0) {

                $user_id = $connect->insert_id;

                // Fetch inserted user
                $getUser = $connect->prepare("
                    SELECT id, fullname, email, phone, created_at
                    FROM users
                    WHERE id = ?
                ");

                $getUser->bind_param("i", $user_id);
                $getUser->execute();
                $userDetails = $getUser->get_result()->fetch_assoc();

                respondOK($userDetails, "User registered successfully");

            } else {
                respondBadRequest("User registration failed.");
            }
        }
    }

} else {
    respondBadRequest("Invalid request. Required fields missing.");
}

?>