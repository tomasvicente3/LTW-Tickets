<?php

declare(strict_types=1);
require_once(__DIR__ . '/../database/agent.class.php');
class Client
{
    public int $id;
    public string $email;
    public string $firstName;
    public string $lastName;
    public string $username;

    public function __construct(int $id, string $email, string $firstName, string $lastName, string $username)
    {
        $this->id = $id;
        $this->email = $email;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->username = $username;
    }

    function getFullName() : string
    {
        return $this->firstName . " " . $this->lastName;
    }

    static function extractClientWithPassword(PDO $db, string $username, string $password)
    {
        $smtm = $db->prepare('
                SELECT *
                FROM Client
                WHERE lower(username) = ?
            ');
        $smtm->execute(array(strtolower($username)));

        $client = $smtm->fetch();
        if ($client && (password_verify($password, $client['password']))) {
            return new Client(
                intval($client['id']),
                $client['email'],
                $client['firstName'],
                $client['lastName'],
                $client['username'],
            );
        } else return null;
    }

    public function verifyPassword(PDO $db, string $password): bool
    {
        $smtm = $db->prepare('
                SELECT password
                FROM Client
                WHERE id = ?
            ');
        $smtm->execute(array($this->id));
        $client = $smtm->fetch();

        if (password_verify($password, $client['password'])) return true;
        return false;
    }

    static function extractClientWithId(PDO $db, int $id): ?Client
    {
        $smtm = $db->prepare('
                SELECT *
                FROM Client
                WHERE id = ?
            ');
        $smtm->execute(array($id));
        $client = $smtm->fetch();

        return new Client(
            intval($client['id']),
            $client['email'],
            $client['firstName'],
            $client['lastName'],
            $client['username'],
        );
    }

    static function extractClientWithName(PDO $db, string $fullName): ?Client
    {
        $temp = explode(" ", $fullName);
        $firstName = $temp[0];
        $lastName = $temp[1];

        $smtm = $db->prepare('
                SELECT *
                FROM Client
                WHERE lower(firstName) = ? AND lower(lastName) = ?
            ');
        $smtm->execute(array(strtolower($firstName), strtolower($lastName)));
        $client = $smtm->fetch();

        return new Client(
            intval($client['id']),
            $client['email'],
            $client['firstName'],
            $client['lastName'],
            $client['username'],
        );
    }

    static function extractClientWithEmail(PDO $db, string $email): ?Client
    {
        $smtm = $db->prepare('
                SELECT *
                FROM Client
                WHERE email = ?
            ');
        $smtm->execute(array($email));
        $client = $smtm->fetch();

        return new Client(
            intval($client['id']),
            $client['email'],
            $client['firstName'],
            $client['lastName'],
            $client['username'],
        );
    }

    static function isUsernameUsed(PDO $db, string $username)
    {
        $smtm = $db->prepare('
            SELECT *
            FROM Client
            WHERE lower(username) = ?
            ');
        $smtm->execute(array(strtolower($username)));

        if ($smtm->fetch()) {
            return true;
        } else {
            return false;
        }
    }

    static function isEmailUsed(PDO $db, string $email)
    {
        $smtm = $db->prepare('
            SELECT *
            FROM Client
            WHERE lower(email) = ?
            ');
        $smtm->execute(array(strtolower($email)));

        if ($smtm->fetch()) {
            return true;
        } else {
            return false;
        }
    }

    static function createClient(PDO $db, string $email, string $firstName, string $lastName, string $username, string $password)
    {
        $stmt = $db->prepare('
                INSERT INTO Client(email,firstName, lastName, username, password) 
                VALUES(?,?,?,?,?)
            ');
        $stmt->execute(array($email, $firstName, $lastName, $username, $password));
    }

    public function save(PDO $db)
    {
        $stmt = $db->prepare('
        UPDATE Client SET email = ?, firstName = ?, lastName = ?, username = ?
        WHERE id = ?
      ');

        $stmt->execute(array($this->email, $this->firstName, $this->lastName, $this->username, $this->id));
    }

    public function upgradeToAgent(PDO $db, array $departments): Agent
    {
        $stmt = $db->prepare('
                INSERT INTO Agent VALUES (?)
            ');
        $stmt->execute(array($this->id));

        $agent = new Agent(
            $this->id,
            $this->email,
            $this->firstName,
            $this->lastName,
            $this->username,
            false
        );

        foreach ($departments as $department) {
            $agent->addDepartment($db, $department);
        }

        return $agent;
    }

    public function alterPassword(PDO $db, string $newPassword)
    {
        $stmt = $db->prepare('
        UPDATE Client SET password = ?
        WHERE id = ?
      ');
        $stmt->execute(array(password_hash($newPassword, PASSWORD_DEFAULT), $this->id));
    }

    static public function getClearance(PDO $db, int $clientId): string
    {
        $agent = Agent::extractAgentWithId($db, $clientId);
        if (!$agent) return "client";
        if ($agent->isAdmin) return "admin";
        return "agent";
    }


    static public function getAllClients(PDO $db): array
    {
        $smtm = $db->prepare('
                SELECT *
                FROM Client
            ');
        $smtm->execute();
        $raw = $smtm->fetchAll();

        $result = [];
        foreach ($raw as $client) {
            $agent = Agent::extractAgentWithId($db, intval($client['id']));
            if (!$agent) {
                $curr = new Client(
                    intval($client['id']),
                    $client['email'],
                    $client['firstName'],
                    $client['lastName'],
                    $client['username'],
                );
                array_push($result, $curr);
            }
        }
        usort($result, function (Client $a, Client $b) {
            return strcmp($a->getFullName(), $b->getFullName());
        });
        return $result;
    }
}
