<?php

declare(strict_types=1);
require_once(__DIR__ . '/../database/client.class.php');
require_once(__DIR__ . '/../database/department.class.php');

class Agent extends Client
{
    public array $departments;
    public bool $isAdmin;

    public function __construct(int $id, string $email, string $firstName, string $lastName, string $username, bool $isAdmin)
    {
        parent::__construct($id, $email, $firstName, $lastName, $username);
        $this->isAdmin = $isAdmin;
    }

    private function fillDepartments(PDO $db): void
    {
        $this->departments = array();
        $stmt = $db->prepare('
                SELECT d.id, d.name
                FROM Department d INNER JOIN AgentDepartment ad ON d.id = ad.idDepartment
                WHERE ad.idAgent = ?
            ');
        $stmt->execute(array($this->id));
        $raw = $stmt->fetchAll();

        foreach ($raw as $department) {
            $curr = new Department(
                intval($department['id']),
                $department['name']
            );
            array_push($this->departments, $curr);
        }
        usort($this->departments, function (Department $a, Department $b) {
            return strcmp($a->name, $b->name);
        });
    }

    public function addDepartment(PDO $db, Department $department): void
    {
        if (array_search($department, $this->departments)) return;
        array_push($this->departments, $department);
        usort($this->departments, function (Department $a, Department $b) {
            return strcmp($a->name, $b->name);
        });

        $stmt = $db->prepare('
                INSERT INTO AgentDepartment VALUES (? , ?)
            ');
        $stmt->execute(array($this->id, $department->id));
    }

    public function removeDepartment(PDO $db, Department $department): void
    {
        if (!(array_search($department, $this->departments))) return;
        $this->departments = array_diff($this->departments, [$department]);

        $stmt = $db->prepare('
                DELETE FROM AgentDepartment
                WHERE idAgent = ? AND idDepartment = ?
            ');
        $stmt->execute(array($this->id, $department->id));
    }

    static function extractAgentWithPassword(PDO $db, string $username, string $password): ?Agent
    {
        $smtm = $db->prepare('
                SELECT c.id, c.email, c.firstName, c.lastName, c.username, c.password
                FROM Client c INNER JOIN Agent a ON a.id=c.id
                WHERE lower(username) = ?
            ');
        $smtm->execute(array(strtolower($username)));

        $agent = $smtm->fetch();
        if ($agent && password_verify($password, $agent['password'])) {
            $agentid = $agent['id'];
            $smtm = $db->prepare('
                    SELECT *
                    FROM Admin
                    WHERE id = ?
                ');
            $smtm->execute(array($agentid));
            $isAdmin = false;
            if ($data = $smtm->fetch()) $isAdmin = true;

            $curr = new Agent(
                intval($agent['id']),
                $agent['email'],
                $agent['firstName'],
                $agent['lastName'],
                $agent['username'],
                $isAdmin
            );
            $curr->fillDepartments($db);
            return $curr;
        } else return null;
    }

    static function extractAgentWithId(PDO $db, int $id): ?Agent
    {
        $smtm = $db->prepare('
                SELECT c.id, c.email, c.firstName, c.lastName, c.username
                FROM Client c INNER JOIN Agent a ON a.id=c.id
                WHERE c.id = ?
            ');
        $smtm->execute(array($id));
        $agent = $smtm->fetch();

        if ($agent) {
            $smtm = $db->prepare('
                    SELECT *
                    FROM Admin
                    WHERE id = ?
                ');
            $smtm->execute(array($id));
            $isAdmin = false;
            if ($data = $smtm->fetch()) $isAdmin = true;

            $curr = new Agent(
                intval($agent['id']),
                $agent['email'],
                $agent['firstName'],
                $agent['lastName'],
                $agent['username'],
                $isAdmin
            );
            $curr->fillDepartments($db);
            return $curr;
        }
        return null;
    }

    static function extractAgentWithUsername(PDO $db, string $username): ?Agent
    {
        $smtm = $db->prepare('
                SELECT c.id, c.email, c.firstName, c.lastName, c.username
                FROM Client c INNER JOIN Agent a ON a.id=c.id
                WHERE c.username = ?
            ');
        $smtm->execute(array($username));
        $agent = $smtm->fetch();

        if ($agent) {
            $smtm = $db->prepare('
                    SELECT *
                    FROM Admin
                    WHERE id = ?
                ');
            $smtm->execute(array($agent['id']));
            $isAdmin = false;
            if ($data = $smtm->fetch()) $isAdmin = true;

            $curr = new Agent(
                intval($agent['id']),
                $agent['email'],
                $agent['firstName'],
                $agent['lastName'],
                $agent['username'],
                $isAdmin
            );
            $curr->fillDepartments($db);
            return $curr;
        }
        return null;
    }

    static function extractAgentWithName(PDO $db, string $fullName): ?Agent
    {
        $temp = explode(" ", $fullName);
        $firstName = $temp[0];
        $lastName = $temp[1];

        $smtm = $db->prepare(
            '
                SELECT c.id, c.email, c.firstName, c.lastName, c.username
                FROM Client c INNER JOIN Agent a ON a.id=c.id
                WHERE lower(c.firstName) = ? AND lower(c.lastName) = ?
            '
        );
        $smtm->execute(array(strtolower($firstName), strtolower($lastName)));
        $agent = $smtm->fetch();

        if ($agent) {
            $smtm = $db->prepare('
                    SELECT *
                    FROM Admin
                    WHERE id = ?
                ');
            $smtm->execute(array($agent['id']));
            $isAdmin = false;
            if ($data = $smtm->fetch()) $isAdmin = true;

            $curr = new Agent(
                intval($agent['id']),
                $agent['email'],
                $agent['firstName'],
                $agent['lastName'],
                $agent['username'],
                $isAdmin
            );
            $curr->fillDepartments($db);
            return $curr;
        }
        return null;
    }

    static function extractAgentWithEmail(PDO $db, string $email): ?Agent
    {
        $smtm = $db->prepare('
                SELECT c.id, c.email, c.firstName, c.lastName, c.username
                FROM Client c INNER JOIN Agent a ON a.id=c.id
                WHERE c.email = ?
            ');
        $smtm->execute(array($email));
        $agent = $smtm->fetch();

        if ($agent) {
            $smtm = $db->prepare('
                    SELECT *
                    FROM Admin
                    WHERE id = ?
                ');
            $smtm->execute(array($agent['id']));
            $isAdmin = false;
            if ($data = $smtm->fetch()) $isAdmin = true;

            $curr = new Agent(
                intval($agent['id']),
                $agent['email'],
                $agent['firstName'],
                $agent['lastName'],
                $agent['username'],
                $isAdmin
            );
            $curr->fillDepartments($db);
            return $curr;
        }
        return null;
    }


    public function downgradeToClient(PDO $db): Client
    {
        $stmt = $db->prepare('
                DELETE FROM Agent
                WHERE id = ?
            ');
        $stmt->execute(array($this->id));

        $client = new Client(
            $this->id,
            $this->email,
            $this->firstName,
            $this->lastName,
            $this->username,
        );
        foreach ($this->departments as $department) {
            $this->removeDepartment($db, $department);
        }
        return $client;
    }

    public function upgradeToAdmin(PDO $db): void
    {
        $this->isAdmin = true;
        $smtm = $db->prepare("INSERT INTO Admin VALUES ($this->id)");
        $smtm->execute();
    }

    public function downgradeToAgent(PDO $db): void
    {
        $this->isAdmin = false;
        $smtm = $db->prepare("DELETE FROM Admin WHERE id = $this->id");
        $smtm->execute();
    }

    public function save(PDO $db)
    {
        $stmt = $db->prepare('
        DELETE FROM AgentDepartment
        WHERE idAgent = ?
      ');

        $stmt->execute(array($this->id));

        $stmt = $db->prepare('
        INSERT INTO AgentDepartment
        VALUES (?, ?)
        ');

        foreach ($this->departments as $department) {
            $stmt->execute(array($this->id, $department->id));
        }
    }

    static public function getAllAgentNames(PDO $db): array
    {
        $stmt = $db->prepare("SELECT username FROM Agent a INNER JOIN Client c on c.id = a.id");
        $stmt->execute();
        $raw = $stmt->fetchAll();
        $usernames = [];
        foreach ($raw as $array) {
            array_push($usernames, $array['username']);
        }
        return $usernames;
    }

    static public function getAllAgents(PDO $db): array
    {
        $smtm = $db->prepare('
                SELECT c.id, c.email, c.firstName, c.lastName, c.username
                FROM Client c INNER JOIN Agent a ON a.id=c.id
            ');
        $smtm->execute();
        $raw = $smtm->fetchAll();

        $result = [];
        foreach ($raw as $agent) {
            if ($agent) {
                $smtm = $db->prepare('
                    SELECT *
                    FROM Admin
                    WHERE id = ?
                ');
                $smtm->execute(array($agent['id']));
                $isAdmin = false;
                if ($data = $smtm->fetch()) $isAdmin = true;

                if($isAdmin) continue;

                $curr = new Agent(
                    intval($agent['id']),
                    $agent['email'],
                    $agent['firstName'],
                    $agent['lastName'],
                    $agent['username'],
                    $isAdmin
                );
                $curr->fillDepartments($db);
                array_push($result, $curr);
            }
        }
        usort($result, function (Agent $a, Agent $b) {
            return strcmp($a->getFullName(), $b->getFullName());
        });
        return $result;
    }

    static public function getAllAdmins(PDO $db): array
    {
        $smtm = $db->prepare('
                SELECT c.id, c.email, c.firstName, c.lastName, c.username
                FROM Client c INNER JOIN Agent a ON a.id=c.id
            ');
        $smtm->execute();
        $raw = $smtm->fetchAll();

        $result = [];
        foreach ($raw as $agent) {
            if ($agent) {
                $smtm = $db->prepare('
                    SELECT *
                    FROM Admin
                    WHERE id = ?
                ');
                $smtm->execute(array($agent['id']));
                $isAdmin = false;
                if ($data = $smtm->fetch()) $isAdmin = true;

                if(!$isAdmin) continue;

                $curr = new Agent(
                    intval($agent['id']),
                    $agent['email'],
                    $agent['firstName'],
                    $agent['lastName'],
                    $agent['username'],
                    $isAdmin
                );
                $curr->fillDepartments($db);
                array_push($result, $curr);
            }
        }
        usort($result, function (Agent $a, Agent $b) {
            return strcmp($a->getFullName(), $b->getFullName());
        });
        return $result;
    }
}
