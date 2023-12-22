<?php

declare(strict_types=1);

require_once(__DIR__ . "/../utils/session.php");
require_once(__DIR__ . '/../templates/common.tpl.php');
require_once(__DIR__ . '/../templates/system.tpl.php');
require_once(__DIR__ . '/../database/connection.db.php');

$session = new Session();
$db = getDatabaseConnection();

if (!$session->isLoggedIn()) die(header('Location: /'));
if ($session->getClearance() == "client") die(header('Location: /'));

if ($_SESSION['clientFilter']) {
    $tickets = $_SESSION['clientTickets'];
    $session->removeArray('clientTickets', 'clientFilter');
} else {
    $tickets = Ticket::getAllTickets($db);
}

drawHeader($session, "../css/system.css", "../javascript/filter.js");
drawSystemOverview($session, $tickets);
drawFooter();
