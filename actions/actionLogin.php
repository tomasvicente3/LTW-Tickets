<?php

    declare(strict_types=1);

    require_once("../utils/session.php");
    $session = new Session();

    require_once("../database/connection.db.php");
    require_once("../database/client.class.php");
    require_once("../database/agent.class.php");

    
    $userDB = getDatabaseConnection();

    $loginUsername = $_POST['username'];
    $loginPassword = $_POST['password'];

    $client = Agent::extractAgentWithPassword($userDB,$loginUsername, $loginPassword);

    if ($client){
        $session->updateSessionOnAgent($client);
        $session->addMessage('success','Login successful!');
        die(header("Location: ../index.php"));
    }
    else { 
        $client = Client::extractClientWithPassword($userDB, $loginUsername, $loginPassword);        
    }

    if ($client){
        $session->updateSessionOnClient($client);
        $session->addMessage('success','Login successful!');
        die(header("Location: ../index.php"));
    }
    else{
        $session->addMessage('error','Wrong username or password!');
        die(header("Location: ../pages/login.php"));
    }
?>
