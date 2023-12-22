<?php

declare(strict_types=1);

require_once("./utils/session.php");
$session = new Session();

if ($session->isLoggedIn()) {
  die(header('Location: pages/homepage.php'));
} else {
  die(header('Location: pages/login.php'));
}
