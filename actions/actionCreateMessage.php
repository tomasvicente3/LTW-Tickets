<?php

declare(strict_types=1);

require_once(__DIR__ . '/../utils/session.php');
$session = new Session();

if (!$session->isLoggedIn()) die(header('Location: /'));
if (empty($_POST['id'])) die(header('Location: /'));

require_once(__DIR__ . '/../database/connection.db.php');
require_once(__DIR__ . '/../database/client.class.php');
require_once(__DIR__ . '/../database/agent.class.php');
require_once(__DIR__ . '/../database/ticket.class.php');
require_once(__DIR__ . '/../database/message.class.php');

$db = getDatabaseConnection();

$client = Client::extractClientWithId($db, $session->getId());
$ticket = Ticket::extractTicketWithId($db, intval($_POST['id']));

if ($client) {
    if (empty($_POST['message'])) {
        $session->addMessage("error", "Enter a valid message!");
        die(header("Location: ../pages/ticket.php?id=" . urlencode($_POST['id'])));
    }
     else {
        Message::createMessage($db, $ticket, $_POST['message'], $session->getId());
        die(header("Location: ../pages/ticket.php?id=" . urlencode($_POST['id'])));
    }
}
