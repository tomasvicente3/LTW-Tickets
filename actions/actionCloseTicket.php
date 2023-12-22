<?php

declare(strict_types=1);

require_once(__DIR__ . '/../utils/session.php');
$session = new Session();

if (!$session->isLoggedIn()) die(header('Location: /'));
if ($_SESSION['csrf'] !== $_POST['csrf']) die(header('Location: /'));

require_once(__DIR__ . '/../database/connection.db.php');
require_once(__DIR__ . '/../database/ticket.class.php');
require_once(__DIR__ . '/../database/agent.class.php');
require_once(__DIR__ . '/../database/faqEntry.class.php');

$db = getDatabaseConnection();

$ticket = Ticket::extractTicketWithId($db, intval($_POST['id']));
$author = Agent::extractAgentWithId($db, $session->getId());

if ($ticket) {
    if (empty($_POST['answer']) && empty($_POST['FAQEntry'])) {
        $session->addMessage("error", "Provide an answer or FAQ entry for this ticket!");
        die(header("Location: ../pages/ticket.php?id=" . $ticket->id));
    } else if ($_POST['FAQEntry']) {
        $faq = FAQEntry::extractFAQWithTitle($db, $_POST['FAQEntry']);
        if (!$faq) {
            $session->addMessage("error", "Enter a valid FAQ entry!");
            die(header("Location: ../pages/ticket.php?id=" . $ticket->id));
        }
    }

    $ticket->addAnswer($db, $_POST['answer'], $faq);

    if ($ticket->status !== "closed") {
        Change::createChange($db, "status", "closed", $author, $ticket);
    }
}

$session->addMessage("success", "Ticket closed successfully!");
die(header("Location: ../pages/ticket.php?id=" . $ticket->id));
