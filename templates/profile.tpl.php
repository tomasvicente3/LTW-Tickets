<?php

declare(strict_types=1);

require_once(__DIR__ . '/../database/client.class.php');
require_once(__DIR__ . '/../utils/session.php');

function drawProfile(Session $session, Client $account)
{
    $accountClearance = ucwords(Client::getClearance(getDatabaseConnection(), $account->id));
?>
    <div class="ProfileHeader">
        <?php if ($session->getId() == $account->id) { ?>
            <h1>Welcome,</h1>
        <?php } ?>
        <h1 data-id=<?= $account->id ?> class="public<?= $accountClearance ?>Name"><?= htmlentities($account->getFullName()) ?></h1>
    </div>
    <div class="ProfileCard">
        <div class="CardHeader">
            <p class="Tag ClearanceTag">
                <?= $accountClearance ?>
            </p>
            <?php if ($session->getId() == $account->id || $session->getClearance() == "admin") { ?>
                <i class="fa-solid fa-pen-to-square fa-2xl EditButton"></i>
            <?php } ?>
        </div>

        <form action="../actions/actionEditProfile.php" method="post" id="EditForm">

            <div class="TitleInput">
                <h2>First Name</h2>
                <input class="UserInfo" type="text" name="firstName" value=<?= htmlentities($account->firstName) ?> />
            </div>


            <div class="TitleInput">
                <h2>Last Name</h2>

                <input class="UserInfo" type="text" name="lastName" value=<?= htmlentities($account->lastName) ?> />
            </div>


            <div class="TitleInput">
                <h2>Username</h2>
                <input class="UserInfo" type="text" name="username" value=<?= htmlentities($account->username) ?> />
            </div>


            <div class="TitleInput">
                <h2>Email</h2>
                <input class="UserInfo" type="text" name="email" value=<?= htmlentities($account->email) ?> />
            </div>

            <?php
            if ($session->getClearance() == 'agent' || $session->getClearance() == 'admin') {
                $agent = Agent::extractAgentWithId(getDatabaseConnection(), $account->id);
            ?>
                <div class="TitleInput Autocomplete" id="Departments">
                    <div class="ButtonTitle">
                        <h2>Departments</h2>
                        <button class="RoundButton" id="NewDepartment" type="button">+</button>
                    </div>
                    <section id="DepartmentsList">
                        <?php
                        if ($agent->departments === []) { ?>
                            <p class="NoContentMessage">No departments yet</p>
                        <?php }
                        foreach ($agent->departments as $department) { ?>
                            <div class="DepartmentField">
                                <input class="UserInfo Tag DepartmentTag DepartmentAutocomplete" type="text" name="departments[]" value="<?= htmlentities($department->name)  ?>" />
                                <i class="fa fa-xmark fa-lg RemoveDepartment"></i>
                            </div>
                        <?php }
                        if ($agent->departments) { ?>
                    </section>
                </div>
        <?php
                        }
                    } ?>
        <?php if ($session->getId() == $account->id) { ?>
            <button id="AlterPasswordButton" type="button">Change Password</button>

            <div class="TitleInput PasswordField">
                <h2>Current Password</h2>
                <input class="UserInfo PasswordInput" type="password" name="currentPassword" />
            </div>

            <div class="TitleInput PasswordField">
                <h2>New Password</h2>
                <input class="UserInfo PasswordInput" type="password" name="newPassword" />
            </div>
        <?php } ?>

        <input type="hidden" name="csrf" value="<?= $_SESSION['csrf'] ?>">
        <input type="hidden" name="id" value="<?= $account->id ?>">


        <button class="RoundButton" form="EditForm" type="submit" id="SubmitEdit"><i class="fa fa-check fa-2xs"></i></button>
        </form>

        <?php if ($session->getMessages()[0]['type'] == 'error') { ?>
            <p class="ErrorMessage"><?= $session->getMessages()[0]['text'] ?></p>
        <?php } ?>

    </div>
<?php
}
