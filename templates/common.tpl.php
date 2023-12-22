<?php

declare(strict_types=1);

require_once(__DIR__ . '/../utils/session.php');

function drawFooter()
{
?>
    </main>
    </body>

    </html>
<?php  }


function drawHeader(Session $session, string $stylesheet, string $script = null)
{ ?>
    <!DOCTYPE html>
    <html lang="en-US">

    <head>
        <title>Tickets</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="../css/common.css">
        <link rel="stylesheet" href=<?= $stylesheet ?>>
        <script src="https://kit.fontawesome.com/861da2e5c3.js" crossorigin="anonymous"></script>
        <script src="../javascript/common.js" defer></script>
        <?php if ($script) { ?>
            <script src="<?= $script ?>" defer> </script> <?php } ?>
        <style>
            @import url('https://fonts.googleapis.com/css2?family=Inter:wght@500;600;700&display=swap');
        </style>
    </head>

    <body>

        <header id="NavBar">
            <a href="/">
                <img src="../assets/logo.png" alt="Our brand's ticket logo"> </img>
            </a>
            <?php if ($session->isLoggedIn()) { ?>
                <i class="fa-solid fa-ellipsis fa-2xl" id="Menu"></i>
            <?php } ?>
        </header>
        <?php if ($session->isLoggedIn()) { ?>

            <div id="FloatingSettingsBox">
                <ul>
                    <a href="../pages/profile.php?id=<?= $session->getId() ?>">
                        <li>
                            <i class="fa-solid fa-circle-user fa-xl"></i>
                            <p>Your Profile</p>
                        </li>
                    </a>
                    <?php if ($session->getClearance() !== "client") { ?>
                        <a href="../pages/system.php">
                            <li>
                                <i class="fa-solid fa-globe fa-xl"></i>
                                <p>System Overview</p>
                            </li>
                        </a>
                    <?php    } ?>
                    <a href="../actions/actionLogout.php">

                        <li>
                            <i class="fa-solid fa-door-open fa-xl"></i>
                            <p>Sign Out</p>
                        </li>
                    </a>

                </ul>
            </div>
        <?php } ?>

        <main>
        <?php }
        ?>