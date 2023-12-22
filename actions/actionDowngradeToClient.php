<?php

declare(strict_types=1);

require_once(__DIR__ . '/../utils/session.php');
$session = new Session();

if (!$session->isLoggedIn()) die(header('Location: /'));
if (!$session->getClearance() == "client") die(header('Location: /'));

require_once(__DIR__ . '/../utils/inputVerification.php');
require_once(__DIR__ . '/../database/connection.db.php');
require_once(__DIR__ . '/../database/agent.class.php');

$db = getDatabaseConnection();

if (!empty($_POST['id'])) {
    $agent = Agent::extractAgentWithId($db, intval($_POST['id']));
} 

if ($agent) {
    $client = $agent->downgradeToClient($db);
    if ($_GET['id'] == $session->getId())
        $session->updateSessionOnClient($client);
    $session->addMessage('success', 'Downgrade successful!');
}

header('Location: ' . $_SERVER['HTTP_REFERER']);
