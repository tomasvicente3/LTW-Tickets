<?php

declare(strict_types=1);

require_once(__DIR__ . '/../utils/session.php');
$session = new Session();

if (!$session->isLoggedIn()) die(header('Location: /'));

require_once(__DIR__ . '/../database/connection.db.php');
require_once(__DIR__ . '/../database/client.class.php');
require_once(__DIR__ . '/../database/agent.class.php');
require_once(__DIR__ . '/../database/ticket.class.php');
require_once(__DIR__ . '/../database/message.class.php');

$db = getDatabaseConnection();

$client = Client::extractClientWithId($db, $session->getId());
$department = null;

if ($client) {
    if (empty($_POST['title']) || empty($_POST['description']) || empty($_POST['priority'])) {
        $session->addMessage("error", "Enter a value for all mandatory * fields!");
        die(header("Location: ../pages/newTicket.php"));
    }
    if (!empty($_POST['department'])) {
        $department = Department::extractDepartmentWithName($db, $_POST['department']);
        if (!$department) {
            $session->addMessage("error", "Enter a valid department!");
            die(header("Location: ../pages/newTicket.php"));
        }
    }
    if (Ticket::createTicket($db, $_POST['title'], $_POST['description'], $_POST['hashtags'], "open", $_POST['priority'], $client, $department)) {
        $session->addMessage("success", "Ticket created successfully!");
        die(header("Location: ../pages/homepage.php"));
    } else {
        $session->addMessage("error", "Enter valid values for all fields!");
        die(header("Location: ../pages/newTicket.php"));
    }
}
