<?php

declare(strict_types=1);

require_once(__DIR__ . "/../utils/session.php");
require_once(__DIR__ . '/../templates/common.tpl.php');
require_once(__DIR__ . '/../templates/faq.tpl.php');
require_once(__DIR__ . '/../database/faqEntry.class.php');
require_once(__DIR__ . '/../database/connection.db.php');

$session = new Session();
$db = getDatabaseConnection();

$faq = FAQEntry::extractFAQWithId($db, intval($_GET['id']));

if ($faq){
    drawHeader($session, "/../css/faqEntry.css");
    drawExpandedFAQ($session, $faq);
    drawFooter();
}
else{
    die(header('Location: /'));
}

?>
