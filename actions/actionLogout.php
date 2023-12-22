<?php

declare(strict_types=1);

require_once("../utils/session.php");

$session = new Session();
$session->logout();

$session->addMessage('success', 'Sign out successful!');
die(header("Location: ../index.php"));
