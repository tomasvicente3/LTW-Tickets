<?php

declare(strict_types=1);

require_once(__DIR__ . '/../utils/session.php');
$session = new Session();

if (!$session->isLoggedIn()) die(header('Location: /'));

require_once(__DIR__ . '/../database/connection.db.php');
require_once(__DIR__ . '/../database/ticket.class.php');


$db = getDatabaseConnection();


$tickets = Ticket::extractTicketsWithClient($db, $session->getId());
$agentTickets = Ticket::extractTicketsWithAgent($db, $session->getId());

//Filter
if ($_POST['filter'] == "status" && !empty($_POST['status'])) {
    $tickets = Ticket::filterByStatus($tickets, $_POST['status']);
    $agentTickets = Ticket::filterByStatus($agentTickets, $_POST['status']);
} else if ($_POST['filter'] == "priority" && !empty($_POST['priority'])) {
    $tickets = Ticket::filterByPriority($tickets, $_POST['priority']);
    $agentTickets = Ticket::filterByPriority($agentTickets, $_POST['priority']);
} else if ($_POST['filter'] == "hashtag" && !empty($_POST['hashtag'])) {
    $tickets = Ticket::filterByHashtag($tickets, $_POST['hashtag']);
    $agentTickets = Ticket::filterByHashtag($agentTickets, $_POST['hashtag']);
} else if ($_POST['filter'] == "department" && !empty($_POST['department'])) {
    $tickets = Ticket::filterByDepartment($tickets, $_POST['department']);
    $agentTickets = Ticket::filterByDepartment($agentTickets, $_POST['department']);
} else if ($_POST['filter'] == "agent" && !empty($_POST['agent'])) {
    $tickets = Ticket::filterByAgent($tickets, $_POST['agent']);
    $agentTickets = Ticket::filterByAgent($agentTickets, $_POST['agent']);
}

if ($_POST['sort'] == "statusSort") {
    usort($tickets, 'sortTicketByStatus');
    usort($agentTickets, 'sortTicketByStatus');
} else if ($_POST['sort'] == "prioritySort") {
    usort($tickets, 'sortTicketByPriority');
    usort($agentTickets, 'sortTicketByPriority');
} else if ($_POST['sort'] == "date") {
    usort($tickets, 'sortTicketByDate');
    usort($agentTickets, 'sortTicketByDate');
}

$session->setArray($tickets, 'clientTickets', 'clientFilter');
$session->setArray($agentTickets, 'agentTickets', 'agentFilter');

header('Location: ' . $_SERVER['HTTP_REFERER']);
?>