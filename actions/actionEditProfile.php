<?php

declare(strict_types=1);

require_once(__DIR__ . '/../utils/session.php');
$session = new Session();

if (!$session->isLoggedIn()) die(header('Location: /'));
if ($_SESSION['csrf'] !== $_POST['csrf']) die(header('Location: /'));

require_once(__DIR__ . '/../utils/inputVerification.php');
require_once(__DIR__ . '/../database/connection.db.php');
require_once(__DIR__ . '/../database/client.class.php');
require_once(__DIR__ . '/../database/agent.class.php');

$db = getDatabaseConnection();

$client = Client::extractClientWithId($db, intval($_POST['id']));

if ($client) {
    if (empty($_POST['email']) || empty($_POST['firstName']) || empty($_POST['lastName']) || empty($_POST['username'])) {
        $session->addMessage("error", "Enter a value for all fields!");
        die(header("Location: ../pages/profile.php?id=" . $session->getId()));
    }

    if (($_POST['email'] !== $client->email && !isEmailValid($_POST['email'])) || ($_POST['username'] !== $client->username && !isUsernameValid($_POST['username']))) {
        die(header("Location: ../pages/profile.php?id=" . $session->getId()));
    }

    if ($_POST['newPassword']) {
        if ($client->verifyPassword($db, $_POST['currentPassword'])) {
            if (isPasswordValid($_POST['newPassword'])) {
                $client->alterPassword($db, $_POST['newPassword']);
            } else {
                die(header("Location: ../pages/profile.php?id=" . $session->getId()));
            }
        } else {
            $session->addMessage("error", "Wrong password for this user!");
            die(header("Location: ../pages/profile.php?id=" . $session->getId()));
        }
    } else if ($_POST['currentPassword']) {
        $session->addMessage("error", "Enter a value for all fields!");
        die(header("Location: ../pages/profile.php?id=" . $session->getId()));
    }

    $client->email = $_POST['email'];
    $client->firstName = $_POST['firstName'];
    $client->lastName = $_POST['lastName'];
    $client->username = $_POST['username'];

    $client->save($db);
    if(intval($_POST['id'] == $session->getId()))$session->updateSession($client);

    $agent = Agent::extractAgentWithId($db, intval($_POST['id']));

    if ($agent) {
        $agent->departments = array();
        foreach ($_POST['departments'] as $department) {
            $current = Department::extractDepartmentWithName($db, $department);
            if (!$current) {
                $session->addMessage("error", "A department entered does not exist!");
                die(header("Location: ../pages/profile.php?id=" . $session->getId()));
            } else {
                $agent->addDepartment($db, $current);
            }
        }
        $agent->save($db);
    }

    header("Location: ../pages/profile.php?id=" . $_POST['id']);
}
