<?php

declare(strict_types=1);

require_once(__DIR__ . "/../utils/session.php");
require_once(__DIR__ . '/../templates/common.tpl.php');
require_once(__DIR__ . '/../templates/faq.tpl.php');
require_once(__DIR__ . '/../database/faqEntry.class.php');

$session = new Session();

if ($session->getClearance() !== "agent" && $session->getClearance() !== "admin"){
    die(header('Location: /'));
}

drawHeader($session, "../css/faqForms.css");
drawNewFaqForm($session);
drawFooter()

?>
