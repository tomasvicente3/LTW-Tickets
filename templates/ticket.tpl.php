<?php

declare(strict_types=1);

require_once(__DIR__ . '/../database/ticket.class.php');
require_once(__DIR__ . '/../database/date.class.php');
require_once(__DIR__ . '/../database/agent.class.php');
require_once(__DIR__ . '/../database/connection.db.php');

function drawTicketPreviewSection(string $title, array $tickets, bool $secondarySection): void
{
?>
    <header id="TicketSectionTitle">
        <div class="ButtonTitle">
            <h1><?= $title ?></h1>
            <?php if (!$secondarySection) { ?>
                <a href="../pages/newTicket.php">
                    <button class="RoundButton" type="button">+</button>
                </a>
            <?php }
            if (!$secondarySection) { ?>
                <form action="../actions/actionFilterTickets.php" method="post" id="FilterTicket">
                    <select id="filter" name="filter">
                        <option disabled selected hidden>Filter</option>
                        <optgroup label="Filter">
                            <option value="all" <?php if (isset($_COOKIE['filterValue']) && $_COOKIE['filterValue'] === 'all') echo 'selected'; ?>>All</option>
                            <option value="status" <?php if (isset($_COOKIE['filterValue']) && $_COOKIE['filterValue'] === 'status') echo 'selected'; ?>>Status</option>
                            <option value="priority" <?php if (isset($_COOKIE['filterValue']) && $_COOKIE['filterValue'] === 'priority') echo 'selected'; ?>>Priority</option>
                            <option value="hashtag" <?php if (isset($_COOKIE['filterValue']) && $_COOKIE['filterValue'] === 'hashtag') echo 'selected'; ?>>Hashtag</option>
                            <option value="department" <?php if (isset($_COOKIE['filterValue']) && $_COOKIE['filterValue'] === 'department') echo 'selected'; ?>>Department</option>
                            <option value="agent" <?php if (isset($_COOKIE['filterValue']) && $_COOKIE['filterValue'] === 'agent') echo 'selected'; ?>>Agent</option>
                        </optgroup>
                    </select>

                    <select id="sort" name="sort">
                        <option disabled selected hidden>Sort</option>
                        <optgroup label="Sort">
                            <option value="statusSort" <?php if (isset($_COOKIE['sortValue']) && $_COOKIE['sortValue'] === 'statusSort') echo 'selected'; ?>>Status</option>
                            <option value="prioritySort" <?php if (isset($_COOKIE['sortValue']) && $_COOKIE['sortValue'] === 'prioritySort') echo 'selected'; ?>>Priority</option>
                            <option value="date" <?php if (isset($_COOKIE['sortValue']) && $_COOKIE['sortValue'] === 'date') echo 'selected'; ?>>Date</option>
                        </optgroup>
                    </select>

                    <div id="HashtagSelection" style="display: none;">
                        <input class="HashtagsAutocomplete" id="HashtagInput" type="text" name="hashtag" />
                    </div>

                    <div id="DepartmentSelection" style="display: none;">

                        <input class="DepartmentAutocomplete" id="DepartmentInput" type="text" name="department" />
                    </div>

                    <div id="AgentSelection" style="display: none;">

                        <input class="AgentAutocomplete" id="AgentInput" type="text" name="agent" />
                    </div>

                    <div id="StatusSelection" style="display: none;">
                        <input id="open" type="radio" name="status" value="open" />
                        <label class="Tag StatusTag" for="open">Open</label>

                        <input id="assigned" type="radio" name="status" value="assigned" />
                        <label class="Tag StatusTag" for="assigned">Assigned</label>

                        <input id="closed" type="radio" name="status" value="closed" />
                        <label class="Tag StatusTag" for="closed">Closed</label>
                    </div>

                    <div id="PrioritySelection" style="display: none;">
                        <input id="low" type="radio" name="priority" value="low" />
                        <label class="Tag PriorityTag Low" for="low">Low</label>

                        <input id="medium" type="radio" name="priority" value="medium" />
                        <label class="Tag PriorityTag Medium" for="medium">Medium</label>

                        <input id="high" type="radio" name="priority" value="high" />
                        <label class="Tag PriorityTag High" for="high">High</label>

                        <input id="very high" type="radio" name="priority" value="very high" />
                        <label class="Tag PriorityTag VeryHigh" for="very high">Very High</label>
                    </div>

                    <button form="FilterTicket" type="submit" id="SubmitFilterTicket" style="display: none;">
                        <i class="fa fa-check "></i>
                    </button>
                </form>
        </div>
        <a href="../pages/faq.php">
            <div class="ButtonTitle">
                <h1>Frequently Asked Questions</h1>
                <button class="RoundButton" type="button">
                    <i class="fa fa-question"></i>
                </button>
            </div>
        </a>
    <?php } ?>
    </header>
    <section id=<?= $title ?> class="TicketSection">
        <?php
        if (sizeof($tickets) == 0) { ?>
            <p class="NoContentMessage">This section doesn't have any tickets yet!</p>
        <?php
        }
        foreach ($tickets as $ticket) {
            drawPreviewTicket($ticket);
        } ?>
    </section>
<?php }


function drawPreviewTicket(Ticket $ticket)
{ ?>
    <a href="../pages/ticket.php?id=<?= $ticket->id ?>">
        <div class="TicketCard">
            <div class="TicketHeader">
                <h2><?= htmlentities($ticket->title) ?></h2>
            </div>
            <div class="TicketBody">
                <p><?= htmlentities($ticket->description) ?></p>
                <button class="RoundButton" type="button">></button>
            </div>
        </div>
    </a>
<?php }

function drawExpandedTicket(Ticket $ticket, Session $session)

{
?>
    <div id="mySidebar" class="sidebar">
        <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
        <h1>Chat</h1>
        <div id="Messages">
            <?php foreach ($ticket->messages as $message) {
                if ($message->client->id !== $session->getId()) { ?>
                    <div class="received-msg">
                        <span class="author"><?= htmlentities($message->client->getFullName()) ?></span>
                        <p><?= $message->content ?></p>
                        <span class="time"><?= htmlentities($message->date->getTime()) ?> | <?= htmlentities($message->date->getDate(false)) ?></span>
                    </div>
                <?php } else { ?>
                    <div class="outgoing-msg">
                        <span class="author"><?= htmlentities($message->client->getFullName()) ?></span>
                        <p><?= $message->content ?></p>
                        <span class="time"><?= htmlentities($message->date->getTime()) ?> | <?= htmlentities($message->date->getDate(false)) ?></span>
                    </div>
                <?php } ?>
            <?php } ?>
        </div>
        <form action="../actions/actionCreateMessage.php?id=<?= urlencode(strval($ticket->id)) ?>" method="post" id="Chat">
            <input type="hidden" name="id" value="<?= $ticket->id ?>">
            <input type="text" id="message" name="message" placeholder="Write a message...">
            <button id="SendButton" type="submit"><i class="fa fa-paper-plane"></i></button>
        </form>
    </div>
    <?php if ($session->getMessages()[0]['type'] == 'error') { ?>
        <p class="ErrorMessage Ticket"><?= $session->getMessages()[0]['text'] ?></p>
    <?php } ?>
    <div id="TicketMain">
        <div class="Title">
            <h1><?= htmlentities($ticket->title) ?></h1>
        </div>
        <div class="TicketCard">
            <h2>Description</h2>
            <p><?= htmlentities($ticket->description) ?></p>
            <div class="Footer">
                <p data-id=<?= $ticket->client->id ?> class=<?= "public" . ucfirst(Client::getClearance(getDatabaseConnection(), $ticket->client->id)) . "Name" ?>><?= $ticket->client->getFullName() ?></p>
                <p> <?= $ticket->date->getDate(false) ?></p>
            </div>
            <a href="editTicket.php?id=<?= urlencode(strval($ticket->id)) ?>">
                <button class="RoundButton"><i class="fa fa-edit"></i></button>
            </a>
        </div>
        <?php
        if ($ticket->answer || $ticket->faqAnswer) { ?>
            <div id="TicketAnswers">
                <h2>Answer</h2>
                <?php
                if ($ticket->answer) { ?>
                    <div id="Answer">
                        <p><?= $ticket->answer ?></p>
                    </div>
                <?php }
                if ($ticket->faqAnswer) { ?>
                    <div id="FAQAnswer">
                        <a href=" ../pages/faqEntry.php?id=<?= $ticket->faqAnswer->id ?>">

                            <h2>FAQ Entry</h2>

                            <div id="FAQContent">
                                <div id="FAQDetails">
                                    <h3><?= $ticket->faqAnswer->title ?></h3>
                                    <p id="FAQPreview"><?= $ticket->faqAnswer->content ?></p>
                                </div>

                                <button class="RoundButton">></button>

                            </div>
                        </a>
                    </div>
                <?php } ?>
            </div>
        <?php }
        if ($ticket->agent && $session->getId() == $ticket->agent->id && $ticket->status !== "closed") { ?>
            <form id="AnswerTicket" autocomplete="off" method="post" action="../actions/actionCloseTicket.php">
                <input type="hidden" name="id" value="<?= $ticket->id ?>">

                <div class="TitleInput"> <label for="answer">Answer this ticket</label>
                    <textarea id="answer" name="answer"></textarea>
                </div>
                <div id="FormFooter">
                    <input type="hidden" name="csrf" value="<?= $_SESSION['csrf'] ?>">
                    <button type="submit">Close Ticket<i class="fa fa-check"></i></button>

                    <div class="TitleInput">
                        <label for="faqEntry">Reference a FAQ entry</label>
                        <input class="FaqAutocomplete" id="faqEntry" type="text" name="FAQEntry" />
                    </div>
                </div>
                <?php if ($session->getMessages()[0]['type'] == 'error') { ?>
                    <p class="ErrorMessage"><?= $session->getMessages()[0]['text'] ?></p>
                <?php
                } ?>
            <?php }
            ?>
            </form>
    </div>
    <div id="List">
        <div id="ChangesAndChat">
            <a href="changes.php?id=<?= urlencode(strval($ticket->id)) ?>">
                <p>View changes</p>
            </a>
            <button onclick="openNav()"><i class="fa fa-paper-plane"></i></button>
        </div>
        <dl>
            <dt>Department</dt>
            <?php if ($ticket->department == null) { ?>
                <dd class="NoContentMessage">No department yet</dd>
            <?php } else { ?>
                <dd><?= htmlentities($ticket->department->name) ?></dd>
            <?php } ?>

            <dt>Assigned agent</dt>
            <?php if ($ticket->agent == null) { ?>
                <dd class="NoContentMessage">No assigned agent yet</dd>
            <?php } else { ?>
                <dd data-id=<?= $ticket->agent->id ?> class=<?= "public" . ucfirst(Client::getClearance(getDatabaseConnection(), $ticket->agent->id)) . "Name" ?>><?= htmlentities($ticket->agent->firstName) ?> <?= htmlentities($ticket->agent->lastName) ?></dd>
            <?php } ?>

            <dt>Status</dt>
            <dd class="Tag StatusTag"><?= $ticket->status ?></dd>

            <dt>Priority</dt>
            <div class="PriorityTag Tag <?= str_replace(' ', '', ucwords($ticket->priority)) ?>">
                <dd><?= ucwords($ticket->priority) ?></dd>
            </div>

            <dt>Hashtags</dt>
            <?php if (empty($ticket->hashtags)) { ?>
                <dd class="NoContentMessage">No hashtags yet</dd>
            <?php } else { ?>
                <div class="Hashtags">
                    <?php foreach ($ticket->hashtags as $hashtag) { ?>
                        <dd class="Tag Hashtag"><?= htmlentities($hashtag) ?></dd>
                    <?php } ?>
                </div>
            <?php } ?>
        </dl>
    </div>
<?php }


function drawChanges(Ticket $ticket)
{
    $changes = $ticket->changes;
?>
    <div class="Title">
        <h1>Change history</h1>
        <div class="Changes">
            <a href="ticket.php?id=<?= urlencode(strval($ticket->id)) ?>">
                <p>View ticket</p>
            </a>
        </div>
    </div>

    <div class="TicketCard">
        <?php if (empty($changes)) { ?>
            <h3 class="NoContentMessage">No changes yet</h3>
        <?php } else { ?>
            <?php foreach ($changes as $change) { ?>
                <h2><?= htmlentities($change->date->getFullDate(false)) ?></h2>
                <div id="ChangeContainer">
                    <?php if ($change->type == "priority") {
                        $oldValue = ucwords($change->oldValue);
                        $newValue = ucwords($change->newValue);
                    ?>
                        <p><?= htmlentities($change->author->getFullName()) ?> changed priority from&nbsp;</p>
                        <div class="Tag PriorityTag <?= str_replace(' ', '', $oldValue) ?>">
                            <?= htmlentities($oldValue) ?>
                        </div>
                        <p>&nbsp;to&nbsp;</p>
                        <div class="Tag PriorityTag <?= str_replace(' ', '', $newValue) ?>">
                            <?= htmlentities($newValue) ?>
                        </div>
                        <?php
                    } else if ($change->type == "hashtag") {
                        $oldValue = explode(" ", $change->oldValue);
                        $newValue = explode(" ", $change->newValue);
                        $removal = array_diff($oldValue, $newValue);
                        $addition = array_diff($newValue, $oldValue);
                        if (empty($addition)) { ?>
                            <p><?= htmlentities($change->author->getFullName()) ?> removed hashtag(s)&nbsp;</p>
                            <?php foreach ($removal as $removed) { ?>
                                <div class="Tag Hashtag">
                                    <?= htmlentities($removed) ?>
                                </div>
                            <?php }
                        } else if (empty($removal)) { ?>
                            <p><?= htmlentities($change->author->getFullName()) ?> added hashtag(s)&nbsp;</p>
                            <?php foreach ($addition as $added) { ?>
                                <div class="Tag Hashtag">
                                    <?= htmlentities($added) ?>
                                </div>
                            <?php }
                        } else { ?>
                            <p><?= htmlentities($change->author->getFullName()) ?> removed hashtag(s)&nbsp;</p>
                            <?php foreach ($removal as $removed) { ?>
                                <div class="Tag Hashtag">
                                    <?= htmlentities($removed) ?>
                                </div>
                            <?php } ?>
                            <p>&nbsp;and added hashtag(s)&nbsp;</p>
                            <?php foreach ($addition as $added) { ?>
                                <div class="Tag Hashtag">
                                    <?= htmlentities($added) ?>
                                </div>
                        <?php }
                        }
                    } else if ($change->type == "agent") {
                        $oldAgent = Agent::extractAgentWithId(getDatabaseConnection(), intval($change->oldValue));
                        $newAgent = Agent::extractAgentWithId(getDatabaseConnection(), intval($change->newValue)); ?>
                        <p>
                            <?= htmlentities($change->author->getFullName()) ?> changed <?= $change->type ?> from&nbsp;</p>
                        <div class="Changed"><?= $oldAgent ? htmlentities($oldAgent->getFullName()) : "None" ?></div>
                        <p>&nbsp;to&nbsp;</p>
                        <div class="Changed"><?= $newAgent ?  htmlentities($newAgent->getFullName())  : "None" ?></div>
                    <?php } else if ($change->type == "department") {
                        $oldDep = Department::extractDepartmentWithId(getDatabaseConnection(), intval($change->oldValue));
                        $newDep = Department::extractDepartmentWithId(getDatabaseConnection(), intval($change->newValue)); ?>
                        <p>
                            <?= htmlentities($change->author->getFullName()) ?> changed <?= $change->type ?> from&nbsp;</p>
                        <div class="Changed"><?= $oldDep ? htmlentities($oldDep->name) : "None" ?></div>
                        <p>&nbsp;to&nbsp;</p>
                        <div class="Changed"><?= $newDep ? htmlentities($newDep->name) : "None" ?></div>
                    <?php } else if ($change->type == "status") { ?>
                        <p>
                            <?= htmlentities($change->author->getFullName()) ?> changed <?= $change->type ?> from&nbsp;</p>
                        <div class="Changed Tag StatusTag"><?= htmlentities($change->oldValue) ?></div>
                        <p>&nbsp;to&nbsp;</p>
                        <div class="Changed Tag StatusTag"><?= htmlentities($change->newValue) ?></div>
                    <?php } else { ?>
                        <p>
                            <?= htmlentities($change->author->getFullName()) ?> changed <?= $change->type ?> from&nbsp;</p>
                        <div class="Changed Text"><?= htmlentities($change->oldValue) ?></div>
                        <p>&nbsp;to&nbsp;</p>
                        <div class="Changed Text"><?= htmlentities($change->newValue) ?></div>
                    <?php } ?>
                </div>
            <?php } ?>
        <?php } ?>
    </div>
<?php }

function drawNewTicketForm(Session $session)
{
?>
    <h1>Submit a new ticket</h1>

    <?php if ($session->getMessages()[0]['type'] == 'error') { ?>
        <p class="ErrorMessage"><?= $session->getMessages()[0]['text'] ?></p>
    <?php } ?>

    <form autocomplete="off" action="../actions/actionCreateTicket.php" method="post" id="NewTicketForm">
        <div class="TitleInput">
            <label class="Mandatory" for="title">Title</label>
            <input id="title" type="text" name="title" />
        </div>

        <div class="TitleInput" id="DescriptionForm">
            <label class="Mandatory" for="description">Description</label>
            <textarea id="description" type="text" name="description"></textarea>
        </div>

        <div id="HashtagInput">
            <div class="TitleInput Autocomplete">
                <div id="HashtagTitle">
                    <label>Hashtag</label>
                    <button class="RoundButton" type="button" id="AddHashtag"><i class="fa fa-add fa-xs"></i></button>
                </div>
                <div class="HashtagField">
                    <div class="InputIcon">
                        <i>#</i>
                        <input class="HashtagsAutocomplete" type="text" name="hashtags[]" />
                    </div>
                    <i class="fa fa-xmark fa-lg RemoveHashtag"></i>
                </div>
            </div>
        </div>


        <div class="TitleInput Autocomplete">
            <label for="department">Department</label>
            <input class="DepartmentAutocomplete" id="department" type="text" name="department" />

        </div>

        <div class="TitleInput">
            <h2 class="Mandatory">Priority</h2>
            <div id="FormFooter">
                <div id="PrioritySelection">
                    <input id="low" type="radio" name="priority" value="low" />
                    <label class="Tag PriorityTag Low" for="low">Low</label>

                    <input class="Tag PriorityTag" id="medium" type="radio" name="priority" value="medium" />
                    <label class="Tag PriorityTag Medium" for="medium">Medium</label>

                    <input id="high" type="radio" name="priority" value="high" />
                    <label class="Tag PriorityTag High" for="high">High</label>

                    <input id="very high" type="radio" name="priority" value="very high" />
                    <label class="Tag PriorityTag VeryHigh" for="very high">Very High</label>
                </div>

                <button form="NewTicketForm" type="submit" id="SubmitNewTicket"><i class="fa fa-check "></i></button>
            </div>
        </div>
    </form>
<?php
}

function drawEditTicketForm(Session $session, Ticket $ticket)
{
?>
    <h1>Edit ticket</h1>

    <?php if ($session->getMessages()[0]['type'] == 'error') { ?>
        <p class="ErrorMessage"><?= $session->getMessages()[0]['text'] ?></p>
    <?php } ?>

    <form autocomplete="off" action="../actions/actionEditTicket.php" method="post" id="EditTicketForm">
        <input type="hidden" name="id" value="<?= $ticket->id ?>">

        <?php if ($session->getId() == $ticket->client->id) { ?>
            <div class="TitleInput">
                <label class="Mandatory" for="title">Title</label>
                <input id="title" type="text" name="title" value="<?= htmlentities($ticket->title) ?>" />
            </div>

            <div class="TitleInput" id="DescriptionForm">
                <label class="Mandatory" for="description">Description</label>
                <textarea id="description" type="text" name="description"><?= htmlentities($ticket->description) ?></textarea>
            </div>
        <?php } ?>

        <?php if ($session->getClearance() !== "client") { ?>
            <div id="HashtagInput">
                <div class="TitleInput Autocomplete">
                    <div id="HashtagTitle">
                        <label>Hashtag</label>
                        <button class="RoundButton" type="button" id="AddHashtag"><i class="fa fa-add fa-xs"></i></button>
                    </div>
                    <?php foreach ($ticket->hashtags as $hashtag) {
                    ?>
                        <div class="HashtagField">
                            <div class="InputIcon">
                                <i>#</i>
                                <input class="HashtagsAutocomplete" type="text" name="hashtags[]" value="<?= htmlentities($hashtag) ?>" />
                            </div>
                            <i class="fa fa-xmark fa-lg RemoveHashtag"></i>
                        </div>
                    <?php
                    } ?>
                </div>
            </div>

            <div class="TitleInput Autocomplete">
                <label for="agent">Assigned Agent</label>
                <input class="AgentAutocomplete" id="agent" placeholder="No assigned agent yet" type="text" name="agent" value="<?= $ticket->agent ? htmlentities($ticket->agent->username) : "" ?>" />
            </div>

            <div class="TitleInput Autocomplete">
                <label for="department">Department</label>
                <input class="DepartmentAutocomplete" id="department" type="text" name="department" value="<?= $ticket->department ? htmlentities($ticket->department->name) : null ?>" />

            </div>

            <div class="TitleInput">
                <h2 class="Mandatory">Status</h2>
                <div id="StatusSelection">
                    <input id="open" type="radio" name="status" value="open" <?php if ($ticket->status == "open") { ?> checked<?php } ?> />
                    <label class="Tag" for="open">Open</label>

                    <input class="Tag" id="assigned" type="radio" name="status" value="assigned" <?php if ($ticket->status == "assigned") { ?> checked<?php } ?> />
                    <label class="Tag" for="assigned">Assigned</label>

                    <input id="closed" type="radio" name="status" value="closed" <?php if ($ticket->status == "closed") { ?> checked<?php } ?> />
                    <label class="Tag" for="closed">Closed</label>
                </div>

            </div>

            <div class="TitleInput">
                <h2 class="Mandatory">Priority</h2>
                <div id="FormFooter">
                    <div id="PrioritySelection">
                        <input id="low" type="radio" name="priority" value="low" <?php if ($ticket->priority == "low") { ?> checked<?php } ?> />
                        <label class="Tag PriorityTag Low" for="low">Low</label>

                        <input class="Tag PriorityTag" id="medium" type="radio" name="priority" value="medium" <?php if ($ticket->priority == "medium") { ?> checked<?php } ?> />
                        <label class="Tag PriorityTag Medium" for="medium">Medium</label>

                        <input id="high" type="radio" name="priority" value="high" <?php if ($ticket->priority == "high") { ?> checked<?php } ?> />
                        <label class="Tag PriorityTag High" for="high">High</label>

                        <input id="very high" type="radio" name="priority" value="very high" <?php if ($ticket->priority == "very high") { ?> checked<?php } ?> />
                        <label class="Tag PriorityTag VeryHigh" for="very high">Very High</label>
                    </div>

                    <input type="hidden" name="csrf" value="<?= $_SESSION['csrf'] ?>">

                    <button form="EditTicketForm" type="submit" id="SubmitEditTicket"><i class="fa fa-check "></i></button>
                </div>
            </div>

        <?php } else { ?>
            <div id="FormFooter">
                <input type="hidden" name="csrf" value="<?= $_SESSION['csrf'] ?>">

                <button form="EditTicketForm" type="submit" id="SubmitEditTicket"><i class="fa fa-check "></i></button>
            </div>
        <?php } ?>
    </form>
<?php
}
