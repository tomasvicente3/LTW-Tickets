<?php

declare(strict_types=1);

require_once(__DIR__ . '/../utils/session.php');

//Ask how to make last submissions data persist in the form

function drawLoginForm(Session $session)
{ ?>
    <form action="../actions/actionLogin.php" method="post" id="login">
        <h2>Log In</h2>
        <div class="IconField">
            <i class="fa-solid fa-circle-user fa-xl"></i>
            <input type="text" name="username" placeholder="Username">
        </div>
        <div class="IconField">
            <i class="fa-solid fa-lock fa-xl"></i>
            <input type="password" name="password" placeholder="Password">
        </div>
        <?php if ($session->getMessages()[0]['type'] == 'error') { ?>
            <p class="ErrorMessage"><?= $session->getMessages()[0]['text'] ?></p>
        <?php
        } ?>
        <a href="signup.php">
            <p>Don't have an account?</p>
        </a>
        <button class="RoundButton" type="submit">></button>
    </form>
<?php }
?>

<?php
function drawSignupForm(Session $session)
{ ?>
    <form action="../actions/actionSignup.php" method="post" id="signup">
        <h2>Sign Up</h2>
        <div class="IconField">
            <i class="fa-solid fa-envelope fa-xl"></i>
            <input type="text" name="email" placeholder="E-mail">
        </div>

        <div class="IconField">
            <i class="fa-solid fa-file-signature fa-xl"></i>
            <input type="text" name="firstName" placeholder="First Name">
        </div>

        <div class="IconField">
            <i class="fa-solid fa-house-chimney-user fa-xl"></i>
            <input type="text" name="lastName" placeholder="Last Name">
        </div>

        <div class="IconField">
            <i class="fa-solid fa-circle-user fa-xl"></i>
            <input type="text" name="username" placeholder="Username">

        </div>

        <div class="IconField">
            <i class="fa-solid fa-lock fa-xl"></i>
            <input type="password" name="password" placeholder="Password">
        </div>

        <div class="IconField">
            <i class="fa-solid fa-check-double fa-xl"></i>
            <input type="password" name="password_verification" placeholder="Confirm your password">
        </div>

        <a href="login.php">
            <p>Already have an account?</p>
        </a>
        <button class="RoundButton" type="submit">></button>

        <?php if ($session->getMessages()[0]['type'] == 'error') { ?>
            <p class="ErrorMessage"><?= $session->getMessages()[0]['text'] ?></p>
        <?php
        } ?>
    </form>
<?php }
?>
