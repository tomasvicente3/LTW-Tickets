<?php

declare(strict_types=1);

require_once(__DIR__ . '/../utils/session.php');
require_once(__DIR__ . '/../templates/common.tpl.php');
require_once(__DIR__ . '/../templates/profile.tpl.php');
require_once(__DIR__ . '/../database/agent.class.php');
require_once(__DIR__ . '/../database/connection.db.php');

$session = new Session();

if (!$session->isLoggedIn()) die(header('Location: /'));

$db = getDatabaseConnection();
if ($_GET['id']) {
    $account = Client::extractClientWithId($db, intval($_GET['id']));
} else {
    Client::extractClientWithId($db, $session->getId());
}

drawHeader($session, "../css/profile.css", "../javascript/profile.js");
drawProfile($session, $account);
drawFooter();
