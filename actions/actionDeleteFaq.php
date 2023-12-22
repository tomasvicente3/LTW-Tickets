<?php

declare(strict_types=1);

require_once(__DIR__ . '/../utils/session.php');
require_once(__DIR__ . '/../database/connection.db.php');
require_once(__DIR__ . '/../database/faqEntry.class.php');

$session = new Session();
$db = getDatabaseConnection();
$faq = FAQEntry::extractFAQWithId($db, intval($_GET['id']));

if (!$session->isLoggedIn()) die(header('Location: /'));
if ($session->getId() !== $faq->agent->id && $session->getClearance() !== "admin") die(header('Location: /pages/faqEntry.php?id='.urlencode(strval($faq->id))));

$faq->deleteFAQ($db);
die(header('Location: /pages/faq.php'));

?>
