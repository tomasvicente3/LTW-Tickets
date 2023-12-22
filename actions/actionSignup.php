<?php

declare(strict_types=1);

require_once("../utils/session.php");
$session = new Session();

require_once("../database/client.class.php");
require_once("../utils/inputVerification.php");

$signupEmail = $_POST['email'];
$signupFirstName = $_POST['firstName'];
$signupLastName = $_POST['lastName'];
$signupUsername = $_POST['username'];
$signupPassword = $_POST['password'];
$signupConfirmedPassword = $_POST['password_verification'];

if (empty($signupEmail) || empty($signupFirstName) || empty($signupLastName) || empty($signupUsername) || empty($signupPassword) || empty($signupConfirmedPassword)) {
    $session->addMessage("error", "Enter a value for all fields!");
    die(header("Location: ../pages/signup.php"));
} else if ($signupPassword !== $signupConfirmedPassword) {
    $session->addMessage("error", "Passwords don't match!");
    die(header("Location: ../pages/signup.php"));
} else if (!isPasswordValid($signupPassword) || !isUsernameValid($signupUsername) || !isEmailValid($signupEmail)) {
    die(header("Location: ../pages/signup.php"));
} else {
    Client::createClient(getDatabaseConnection(), $signupEmail, $signupFirstName, $signupLastName, $signupUsername, password_hash($signupPassword, PASSWORD_DEFAULT));
    $session->addMessage("sucess", "Signup successful!");
    die(header('Location: ../pages/login.php'));
}
