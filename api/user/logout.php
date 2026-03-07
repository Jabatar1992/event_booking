<?php
$method="POST";
$cache="no-cache";
include "../../head.php";

// check access token
if(isset($_POST['access_token'])){

    $access_token = cleanme($_POST['access_token']);

    // validation
    if(input_is_invalid($access_token)){
        respondBadRequest("Access token is required");
    }else{

        // check if token exists
        $checktoken = $connect->prepare("SELECT * FROM user_tokens WHERE access_token=?");
        $checktoken->bind_param("s",$access_token);
        $checktoken->execute();
        $result = $checktoken->get_result();

        if($result->num_rows > 0){

            // delete token (logout)
            $deletetoken = $connect->prepare("DELETE FROM user_tokens WHERE access_token=?");
            $deletetoken->bind_param("s",$access_token);
            $deletetoken->execute();

            respondOK([],"Logout successful");

        }else{
            respondBadRequest("Invalid access token");
        }
    }

}else{
    respondBadRequest("Invalid request. Access token is required.");
}

?>