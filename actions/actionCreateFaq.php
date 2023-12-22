<?php

declare(strict_types=1);

require_once(__DIR__ . '/../utils/session.php');
require_once(__DIR__ . '/../database/connection.db.php');
require_once(__DIR__ . '/../database/faqEntry.class.php');

$session = new Session();
$db = getDatabaseConnection();

if (!$session->isLoggedIn() || ($session->getClearance() === "client")){
    die(header('Location: /'));
}

$agent = Agent::extractAgentWithId($db, $session->getId());

if ($agent){
    if (empty($_POST['title']) || empty($_POST['content'])){
        $session->addMessage("error", "Enter a value for all mandatory * fields!");
        die(header("Location: ../pages/newFaq.php"));
    }
    else if (strlen($_POST['title'])>40){
        $session->addMessage("error", "Enter a title with less than 40 characters!");
        die(header("Location: ../pages/newFaq.php"));
    }
    else if (FAQEntry::createFAQEntry($db, $_POST['title'], $_POST['content'], $agent)){
        $session->addMessage("success", "FAQ created Successfully!");
        die(header("Location: /../pages/faq.php"));
    }
}