<?php
$method="POST";
$cache="no-cache";
include "../head.php";

if (isset($_POST['fullname'], $_POST['email'], $_POST['password'])) {

    $fullname = cleanme(trim($_POST['fullname']));
    $email    = cleanme(trim($_POST['email']));
    $phone    = isset($_POST['phone']) ? cleanme(trim($_POST['phone'])) : NULL;
    $password = cleanme(trim($_POST['password']));

    // Validation using else-if structure
    if (input_is_invalid($fullname) || input_is_invalid($email) || input_is_invalid($password)) {
        respondBadRequest("Full name, email and password are required.");
        exit;

    } else if (strlen($fullname) < 3) {
        respondBadRequest("Full name must be at least 3 characters.");
        exit;

    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        respondBadRequest("Invalid email format.");
        exit;

    } else if (strlen($password) < 8) {
        respondBadRequest("Password must be at least 8 characters.");
        exit;

    } else if (!preg_match("/[A-Z]/", $password)) {
        respondBadRequest("Password must contain at least one uppercase letter.");
        exit;

    } else if (!preg_match("/[\W]/", $password)) {
        respondBadRequest("Password must contain at least one special character.");
        exit;
    }

    // Check if email already exists
    $checkadmin = $connect->prepare("
        SELECT id, fullname, email, phone, created_at
        FROM admin
        WHERE email = ?
    ");
    $checkadmin->bind_param("s", $email);
    $checkadmin->execute();
    $result = $checkadmin->get_result();

    if ($result->num_rows > 0) {
        $existingadmin = $result->fetch_assoc();
        respondBadRequest("Admin with this email already exists.", $existingadmin);
        exit;
    }

    // Hash password (Recommended for security)
    //$hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert new admin
    $insertadmin = $connect->prepare("
        INSERT INTO admin (fullname, email, phone, password)
        VALUES (?, ?, ?, ?)
    ");
    $insertadmin->bind_param("ssss", $fullname, $email, $phone, $password);
    $insertadmin->execute();

    if ($insertadmin->affected_rows > 0) {

        $admin_id = $connect->insert_id;

        // Fetch newly added admin details (excluding password)
        $getadmin = $connect->prepare("
            SELECT id, fullname, email, phone, created_at
            FROM admin
            WHERE id = ?
        ");
        $getadmin->bind_param("i", $admin_id);
        $getadmin->execute();
        $adminDetails = $getadmin->get_result()->fetch_assoc();

        respondOK($adminDetails, "Admin created successfully");

    } else {
        respondBadRequest("Failed to create admin.");
    }

} else {
    respondBadRequest("Invalid request. Required fields are missing.");
}
?>