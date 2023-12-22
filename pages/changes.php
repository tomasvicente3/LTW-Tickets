<?php

declare(strict_types=1);

require_once(__DIR__ . "/../utils/session.php");
require_once(__DIR__ . '/../templates/common.tpl.php');
require_once(__DIR__ . '/../templates/ticket.tpl.php');
require_once(__DIR__ . '/../database/ticket.class.php');
require_once(__DIR__ . '/../database/department.class.php');
require_once(__DIR__ . '/../database/agent.class.php');
require_once(__DIR__ . '/../database/date.class.php');
require_once(__DIR__ . '/../database/change.class.php');

$session = new Session();
$db = getDatabaseConnection();
$ticket = Ticket::extractTicketWithId($db, intval($_GET['id']));

if (!$ticket) die(header('Location: /'));

drawHeader($session, "../css/changes.css");
drawChanges($ticket);
drawFooter();
