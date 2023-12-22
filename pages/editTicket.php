<?php

declare(strict_types=1);

require_once(__DIR__ . "/../utils/session.php");
require_once(__DIR__ . '/../templates/common.tpl.php');
require_once(__DIR__ . '/../templates/ticket.tpl.php');
require_once(__DIR__ . '/../database/ticket.class.php');
require_once(__DIR__ . '/../database/department.class.php');
require_once(__DIR__ . '/../database/agent.class.php');
require_once(__DIR__ . '/../database/connection.db.php');

$session = new Session();
if (!$session->isLoggedIn()) die(header('Location: /'));

$db = getDatabaseConnection();
$ticket = Ticket::extractTicketWithId($db, intval($_GET['id']));

if (!$ticket) die(header('Location: /'));
if ($session->getId() !== $ticket->client->id && $session->getClearance() == "client") die(header('Location: /'));

drawHeader($session, "../css/ticketForms.css", "../javascript/ticket.js");
drawEditTicketForm($session, $ticket);
drawFooter();
