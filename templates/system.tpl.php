<?php

declare(strict_types=1);

require_once(__DIR__ . '/../database/ticket.class.php');
require_once(__DIR__ . '/../database/date.class.php');
require_once(__DIR__ . '/../database/agent.class.php');
require_once(__DIR__ . '/../database/client.class.php');
require_once(__DIR__ . '/../database/department.class.php');
require_once(__DIR__ . '/../database/connection.db.php');
require_once(__DIR__ . '/../templates/ticket.tpl.php');


function drawSystemOverview(Session $session, array $tickets)
{
    $db = getDatabaseConnection();

    $clients = Client::getAllClients($db);
    $agents = Agent::getAllAgents($db);
    $admins = Agent::getAllAdmins($db);
    $departments = Department::getAllDepartments($db);

?>
    <div id="Tickets">
        <?php
        drawTicketPreviewSection("Tickets", $tickets, false);
        ?>
    </div>

    <div id="Clients">
        <?php
        drawUserSection($clients, "Client");
        ?>
    </div>

    <div id="Agents">
        <?php
        drawUserSection($agents, "Agent");
        ?>
    </div>

    <div id="Admins">
        <?php
        drawUserSection($admins, "Admin");
        ?>
    </div>
    <?php if ($session->getClearance() == "admin") { ?>

        <div id="Departments">
            <div class="ButtonTitle">
                <h1>Departments</h1>
                <button class="RoundButton" id="NewDepartment" type="button">+</button>
            </div>
            <section id="DepartmentList">
                <form id="DepartmentForm" method="post" action="../actions/actionCreateDepartment.php">
                    <?php
                    foreach ($departments as $department) { ?>
                        <div class="DepartmentField">
                            <input class="Tag DepartmentTag" type="text" name="departments[]" value="<?= htmlentities($department->name)  ?>" />
                            <i class="fa fa-xmark fa-lg RemoveDepartment"></i>
                        </div>
                    <?php } ?>
                    <input type="hidden" name="csrf" value="<?= $_SESSION['csrf'] ?>">
                    <button form="DepartmentForm" type="submit" id="SubmitDepartments"><i class="fa fa-check fa-2xs"></i></button>
                </form>
            </section>
        </div>
    <?php }
}

function drawUserSection(array $users, string $type)
{ ?>
    <header id="<?= $type ?>SectionTitle" class="SectionTitle">
        <h1><?= $type ?></h1>
    </header>
    <section class="<?= ucwords($type) ?>Section">
        <?php
        if (sizeof($users) == 0) {
        ?>
            <p class="NoContentMessage">This section doesn't have any <?= strtoLower($type) ?>s yet!</p>
        <?php
        }
        foreach ($users as $user) { ?>
            <div class="UserCard public<?= $type ?>Name" data-id=<?= $user->id ?>>
                <div class="UserInfo">
                    <a href="../pages/profile.php?id=<?= urlencode(strval($user->id)) ?>">
                        <h2><?= htmlentities($user->getFullName()) ?></h2>
                        <p><?= htmlentities($user->username) ?></p>
                        <p><?= htmlentities($user->email) ?></p>

                        <?php
                        if ($type !== "Client") { ?>
                            <div class="ButtonTitle">
                                <h2>Departments</h2>
                            </div>
                            <?php
                            if (sizeof($user->departments) == 0) {
                            ?>
                                <p class="NoContentMessage">No departments assigned</p>
                            <?php } ?>
                            <div class="Departments">
                                <?php
                                foreach ($user->departments as $department) { ?>
                                    <p class="DepartmentName"><?= htmlentities($department->name) ?></p>
                                <?php } ?>
                            </div>
                        <?php
                        } ?>
                    </a>
                </div>
            </div>

        <?php } ?>
    <?php } ?>