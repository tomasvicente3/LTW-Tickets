<?php

declare(strict_types=1);

require_once(__DIR__ . '/../utils/session.php');
$session = new Session();

require_once(__DIR__ . '/../database/connection.db.php');
require_once(__DIR__ . '/../database/agent.class.php');

$db = getDatabaseConnection();

$agents = Agent::getAllAgentNames($db);

echo json_encode(array_values($agents));
