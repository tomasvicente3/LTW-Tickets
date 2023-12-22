<?php

declare(strict_types=1);

require_once("../utils/session.php");
$session = new Session();

require_once("../database/connection.db.php");
require_once("../database/client.class.php");


function isPasswordValid(string $password): bool
{
    global $session;
    $password_validation_regex = "/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-_]).{8,}$/";

    if (preg_match($password_validation_regex, $password))
        return true;
    else {
        $session->addMessage("error", "Password needs to be 8 characters long, contain a lowercase and uppercase letter, a number and a special char!");
        return false;
    }
}

function isUsernameValid(string $username): bool
{
    global $session;
    $username_validation_regex = "/^[A-Za-z][A-Za-z0-9]{4,24}$/";
    $userDB = getDatabaseConnection();

    if (preg_match($username_validation_regex, $username)) {
        if (!Client::isUsernameUsed($userDB, $username))
            return true;
        else {
            $session->addMessage("error", "This user already exists!");
            return false;
        }
    } else {
        $session->addMessage("error", "Username needs to be between 5 and 25 characters long, contain only alphanumeric characters and not begin with a digit!");
        return false;
    }
}

function isEmailValid(string $email): bool
{
    global $session;
    $userDB = getDatabaseConnection();

    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        if (!Client::isEmailUsed($userDB, $email))
            return true;
        else {
            $session->addMessage("error", "This email is already in use!");
            return false;
        }
    } else {
        $session->addMessage("error", "E-mail format not valid!");
        return false;
    }
}
