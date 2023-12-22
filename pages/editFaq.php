<?php

declare(strict_types=1);

require_once(__DIR__ . "/../utils/session.php");
require_once(__DIR__ . '/../database/connection.db.php');
require_once(__DIR__ . '/../templates/common.tpl.php');
require_once(__DIR__ . '/../templates/faq.tpl.php');
require_once(__DIR__ . '/../database/faqEntry.class.php');

$session = new Session();
if (!$session->isLoggedIn()) die(header('Location: /'));

$db = getDatabaseConnection();
$faq = FAQEntry::extractFAQWithId($db, intval($_GET['id']));

if (!$faq || $session->getClearance() === "client") die(header('Location: /faqEntry.php?id='.$_GET['id']));

drawHeader($session, "../css/faqForms.css");
drawEditFaqForm($session, $faq);
drawFooter();
