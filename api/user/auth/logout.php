
<?php
$method="POST";
$cache="no-cache";
include "../../head.php";

if(isset($_POST['users_id'])){

    $users_id = cleanme(trim($_POST['users_id']));

    // validation
    if(input_is_invalid($users_id)){
        respondBadRequest("users ID is required");
    }else if(!is_numeric($users_id)){ 
        respondBadRequest("users ID must be numeric");
    }else{

        // check if users exists
        $checkusers = $connect->prepare("SELECT * FROM users WHERE id=?");
        $checkusers->bind_param("i", $users_id);
        $checkusers->execute();
        $result = $checkusers->get_result();

        if($result->num_rows > 0){

           
    $accesstoken=getTokenToSendAPI($users_id);

    // Login successful
    respondOK( ['access_token'=>$accesstoken],"Logout successful.");

} else {
    respondBadRequest("Invalid request. users ID and password are required.");
}

    }

}
//
?>