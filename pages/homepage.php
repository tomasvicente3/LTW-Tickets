<?php

declare(strict_types=1);

require_once(__DIR__ . '/../utils/session.php');
require_once(__DIR__ . '/../templates/common.tpl.php');
require_once(__DIR__ . '/../templates/ticket.tpl.php');
require_once(__DIR__ . '/../database/ticket.class.php');
require_once(__DIR__ . '/../database/department.class.php');
require_once(__DIR__ . '/../database/agent.class.php');
require_once(__DIR__ . '/../database/connection.db.php');
require_once(__DIR__ . '/../database/date.class.php');

$session = new Session();
$db = getDatabaseConnection();

if (!$session->isLoggedIn()) die(header('Location: /'));

if ($_SESSION['clientFilter']) {
    $tickets = $_SESSION['clientTickets'];
    $session->removeArray('clientTickets', 'clientFilter');
} else {
    $tickets = Ticket::extractTicketsWithClient($db, $session->getId());
}

if ($_SESSION['agentFilter']) {
    $agentTickets = $_SESSION['agentTickets'];
    $session->removeArray('agentTickets', 'agentFilter');
} else {
    $agentTickets = Ticket::extractTicketsWithAgent($db, $session->getId());
}

drawHeader($session, "../css/homepage.css", "../javascript/filter.js");
drawTicketPreviewSection("Your Tickets", $tickets, false);
if ($session->getClearance() !== "client") {
    drawTicketPreviewSection("Your Assigned Tickets", $agentTickets, true);
}
drawFooter();
