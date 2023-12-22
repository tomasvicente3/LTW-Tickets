<?php

declare(strict_types=1);

require_once(__DIR__ . '/../utils/session.php');
require_once(__DIR__ . '/../templates/common.tpl.php');
require_once(__DIR__ . '/../templates/auth.tpl.php');

$session = new Session();

drawHeader($session, "../css/login.css");
drawLoginForm($session);
drawFooter();
