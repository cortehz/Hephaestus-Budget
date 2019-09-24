<?php

$conn = mysqli_connect('localhost', 'root', '', 'altair');

function clean_data($data)
{
    return stripslashes(strip_tags(trim($_POST[$data])));
}

if (isset($_POST['signup'])) {
    $username = clean_data('username');
    $email = clean_data('email');
    $pass = clean_data('pass');
    $rpass = clean_data('rpass');
    $validated = true;
    if (!empty($username) && !empty($email) && !empty($pass) && !empty($rpass)) {

        //check if username is already taken

        $user_exists = mysqli_query($conn, "SELECT  `username` FROM registered_user WHERE `username` = '{$username}'");
        $count = mysqli_num_rows($user_exists);
        if ($count == "1") {
            $validated = false;
            $error = "This username is already taken";
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $validated = false;
            $error = "Enter a valid email address";
        }

        if (strlen($pass) < 4 || strlen($rpass) < 4) {
            $validated = false;
            $error = "Password must be minimum 4 characters";
        }

        if ($pass !== $rpass) {
            $validated = false;
            $error = "Passwords do not match";
        }

        if ($validated == true) {
            $pass = md5($rpass);
            $new_user_query =  mysqli_query($conn, "INSERT INTO registered_user(`username`, `email`, `password`) VALUES('$username', '$email',  '$pass')");
            if ($new_user_query == TRUE) {
                $username = $email = "";
                $success = "Your registration was successful, you can login to your account";
            } else {
                $error = "An error was encountered on registration";

                // mysqli_error($conn);
            }
        }
    } else {
        $validated = false;
        $error = "All fields are required";
    }
}


if (isset($_POST['login'])) {
    $username = clean_data('username');
    $pass = md5(clean_data('pass'));

    if (!empty($username) && !empty($pass)) {
        $login_query = mysqli_query($conn, "SELECT `username`, `password` FROM registered_user WHERE `username` = '{$username}' AND `password` = '{$pass}' ");
        // $fetch_user = mysqli_fetch_assoc($login_query);
        $count = mysqli_num_rows($login_query);
        if ($count == "0") {
            $error = "Incorrect username or password";
        } else {
            header("location: ./");
         }
    } else {
        $error = "Enter username and password to login";
    }
}
