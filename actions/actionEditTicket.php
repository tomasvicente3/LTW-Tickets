<?php

declare(strict_types=1);

require_once(__DIR__ . '/../utils/session.php');
$session = new Session();

if (!$session->isLoggedIn()) die(header('Location: /'));
if ($_SESSION['csrf'] !== $_POST['csrf']) die(header('Location: /'));
if (empty($_POST['id'])) die(header('Location: /'));

require_once(__DIR__ . '/../database/connection.db.php');
require_once(__DIR__ . '/../database/client.class.php');
require_once(__DIR__ . '/../database/agent.class.php');
require_once(__DIR__ . '/../database/ticket.class.php');
require_once(__DIR__ . '/../database/change.class.php');

$db = getDatabaseConnection();
$ticket = Ticket::extractTicketWithId($db, intval($_POST['id']));

if ($ticket) {
    if ($session->getClearance() !== "client") {
        $author = Agent::extractAgentWithId($db, $session->getId());
    } else {
        $author = Client::extractClientWithId($db, $session->getId());
    }

    //Client Changes
    if (($session->getId() == $ticket->client->id)) {
        //Input Validation
        if (empty($_POST['title']) || empty($_POST['description'])) {
            $session->addMessage("error", "Enter a value for all mandatory * fields!");
            die(header('Location: ' . $_SERVER['HTTP_REFERER']));
        }
        if ($_POST['description'] !== $ticket->description) {
            Change::createChange($db, "description", $_POST['description'], $author, $ticket);
        }
        if ($_POST['title'] !== $ticket->title) {
            Change::createChange($db, "title", $_POST['title'], $author, $ticket);
        }
    }

    //Agent Changes
    if ($session->getClearance() !== "client") {
        //Input Validation
        if (!empty($_POST['department'])) {
            $department = Department::extractDepartmentWithName($db, $_POST['department']);
            if (!$department) {
                $session->addMessage("error", "Enter a valid department!");
                die(header('Location: ' . $_SERVER['HTTP_REFERER']));
            } else if ($_POST['department'] !== $ticket->department->name) {
                Change::createChange($db, "department", strval($department->id), $author, $ticket);
            }
        }
        if ($_POST['priority'] !== $ticket->priority) {
            if (!Change::createChange($db, "priority", $_POST['priority'], $author, $ticket)) {
                $session->addMessage("error", "Enter valid values for all fields!");
                die(header('Location: ' . $_SERVER['HTTP_REFERER']));
            }
        }
        if ($_POST['hashtags'] !== $ticket->hashtags) {
            if ($_POST['hashtags']) {
                Change::createChange($db, "hashtag", implode(' ', $_POST['hashtags']), $author, $ticket);
            }
        }
        if ($_POST['status'] !== $ticket->status) {
            if (!Change::createChange($db, "status", $_POST['status'], $author, $ticket)) {
                $session->addMessage("error", "Enter valid values for all fields!");
                die(header('Location: ' . $_SERVER['HTTP_REFERER']));
            }
        }
        if ($_POST['agent'] !== ($ticket->agent ? $ticket->agent->username : null)) {
            $agent = Agent::extractAgentWithUsername($db, $_POST['agent']);
            Change::createChange($db, "agent", $agent ? strval($agent->id) : null, $author, $ticket);
            if ($ticket->status == "open" && $_POST['agent']) {
                Change::createChange($db, "status", "assigned", $author, $ticket);
            } else if ($ticket->status == "assigned" && !$_POST['agent']) {
                Change::createChange($db, "status", "open", $author, $ticket);
            }
        }
    }
}
$session->addMessage("success", "Ticker edited successfully!");
die(header("Location: ../pages/ticket.php?id=" . urlencode(strval($ticket->id))));
