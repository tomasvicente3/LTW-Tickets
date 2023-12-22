<?php

declare(strict_types=1);

require_once(__DIR__ . '/../database/faqEntry.class.php');

function drawFAQEntry(Session $session, FAQEntry $entry)
{
?> <div class="FAQCard">
        <a href="../pages/faqEntry.php?id=<?= urlencode(strval($entry->id)) ?>">
            <div class="IconTitle">
                <h2><?= htmlentities($entry->title) ?></h2>
            </div>
            <div class="FAQCardDetails">
                <div class="FAQCardContent">
                    <p><?= htmlentities($entry->content) ?></p>
                </div>
                <button class="RoundButton" type="button">></button>
            </div>
            <div class="FAQCardFooter">
                <p><?= htmlentities($entry->agent->getFullName()) ?></p> 
                <p><?=$entry->date->getDate(false) ?></p>
            </div>
        </a>
    </div>
<?php }

function drawFAQPreviewSection(Session $session, string $title, array $entries)
{
?> <div class="ButtonTitle">
        <h1><?= htmlentities($title) ?></h1>
        <?php if ($session->getClearance() !== "client") {  ?>
            <button class="RoundButton" type="button"><a href="newFaq.php">+</a></button> <?php }  ?>
    </div>
    <section id=<?= htmlentities($title) ?> class="FAQSection">
        <?php
        if (sizeof($entries) == 0) {
        ?>
            <p class="NoContentMessage">This section doesn't have any entries yet!</p>
        <?php
        }
        foreach ($entries as $entry) {
            drawFAQEntry($session, $entry);
        } ?>
    </section>
<?php }

function drawNewFaqForm(Session $session){ ?>

    <h1>Create a new FAQ</h1>

    <?php if ($session->getMessages()['0']['type'] === 'error') { ?>
        <p class="ErrorMessage"><?= $session->getMessages()[0]['text'] ?></p>
    <?php } ?>

    <form autocomplete="off" action="../actions/actionCreateFaq.php" method="post" id="NewFaqForm">
        <div class="TitleInput">
            <label class="Mandatory" for="title">Title</label>
            <input id="title" type="text" name="title">
        </div>

        <div class="TitleInput" id="ContentForm">
            <label class="Mandatory" for="content">Content</label>
            <textarea id="content" type="text" name="content"></textarea>
        </div>

        <div id ="FormFooter">
            <button form="NewFaqForm" type="submit" id="SubmitNewFaq"><i class="fa fa-check"></i></button>
        </div>
    </form>

<?php 
}

function drawExpandedFAQ(Session $session, FAQEntry $faq){ 
    $content = str_replace("\n",'<br>',htmlentities($faq->content));?>

<div class="container">
        <div class="Title">
            <h1><?= htmlentities($faq->title) ?></h1>
        </div>
        <div class="FAQCard">
            <h2>Answer</h2>
            <p><?=$content?></p>
            <div class="Footer">
                <p data-id=<?= $faq->agent->id ?> class=<?= "public" . ucfirst(Client::getClearance(getDatabaseConnection(), $faq->agent->id)) . "Name" ?>><?= htmlentities($faq->agent->getFullName()) ?></p>
                <p> <?= $faq->date->getDate(false) ?></p>
            </div>
            <?php 
            if ($session->getId() === $faq->agent->id || $session->getClearance() === "admin"){ ?>
                <button class="RoundButton" id="DeleteButton"><a href="/../actions/actionDeleteFaq.php?id=<?=urlencode(strval($faq->id))?>"><i class="fa fa-trash"></i></a></button>
                <button class="RoundButton" id="EditButton"><a href="editFaq.php?id=<?=urlencode(strval($faq->id))?>"><i class="fa fa-edit"></i></a></button>
            <?php 
            } ?>
        </div>
    </div>
<?php
}

function drawEditFaqForm(Session $session, FAQEntry $faq){ ?>

<h1>Edit FAQ</h1>

    <?php if ($session->getMessages()[0]['type'] == 'error') { ?>
        <p class="ErrorMessage"><?= $session->getMessages()[0]['text'] ?></p>
    <?php } ?>

    <form action="../actions/actionEditFaq.php" method="post" id="EditFaqForm">
        <input type="hidden" name="id" value="<?= $faq->id ?>">

            <div class="TitleInput">
                <label class="Mandatory" for="title">Title</label>
                <input id="title" type="text" name="title" value="<?= htmlentities($faq->title) ?>" />
            </div>

            <div class="TitleInput" id="ContentForm">
                <label class="Mandatory" for="content">Content</label>
                <textarea id="content" type="text" name="content"><?= htmlentities($faq->content) ?></textarea>
            </div>
            <div id="FormFooter">
                <button form="EditFaqForm" type="submit" id="SubmitEditTicket"><i class="fa fa-check "></i></button>
            </div>
    </form>

<?php
}

?>
