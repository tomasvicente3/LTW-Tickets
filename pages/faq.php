<?php

declare(strict_types=1);

require_once(__DIR__ . "/../utils/session.php");
require_once(__DIR__ . '/../templates/faq.tpl.php');
require_once(__DIR__ . '/../templates/common.tpl.php');
require_once(__DIR__ . '/../database/faqEntry.class.php');
require_once(__DIR__ . '/../database/connection.db.php');

$session = new Session();
$db = getDatabaseConnection();

if (!$session->isLoggedIn()) die(header('Location: /'));

$db = getDatabaseConnection();
$faqs = FAQEntry::extractFAQs($db);

drawHeader($session, "../css/faq.css");
drawFAQPreviewSection($session, "Frequently Asked Questions",$faqs);
drawFooter();
