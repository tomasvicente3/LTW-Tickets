<?php

declare(strict_types=1);

require_once(__DIR__ . '/../utils/session.php');
require_once(__DIR__ . '/../database/connection.db.php');
require_once(__DIR__ . '/../database/faqEntry.class.php');

$session = new Session();
$db = getDatabaseConnection();
$faq = FAQEntry::extractFAQWithId($db, intval($_POST['id']));

if (!$session->isLoggedIn()) die(header('Location: /'));
if (empty($_POST['id'])) die(header('Location: /'));

if ($faq){
    if (empty($_POST['title']) || empty($_POST['content'])){
        $session->addMessage("error", "Enter a value for all mandatory * fields!");
        die(header('Location: ' . $_SERVER['HTTP_REFERER']));
    }
    if (strlen($_POST['title'])>40){
        $session->addMessage("error","Enter a title with less than 40 characters!");
        die(header('Location: ' . $_SERVER['HTTP_REFERER']));
    }
    else{
        $stmt = $db->prepare("
            UPDATE FAQEntry 
            SET title = ?, content = ? 
            WHERE id = ? 
        ");
        $stmt->execute(array($_POST['title'], $_POST['content'], $faq->id));
        $faq->title = $_POST['title']; $faq->content = $_POST['content'];
        $session->addMessage("success","FAQ updated successfuly!");
        die(header('Location: /../pages/faqEntry.php?id='.$_POST['id']));
    }

}
