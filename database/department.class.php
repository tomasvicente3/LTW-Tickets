<?php

declare(strict_types=1);

class Department
{
    public int $id;
    public string $name;

    public function __construct(int $id, string $name)
    {
        $this->id = $id;
        $this->name = $name;
    }

    static function extractDepartmentWithId(PDO $db, int $id): ?Department
    {
        $stmt = $db->prepare('
                SELECT *
                FROM Department
                WHERE id = ?
            ');
        $stmt->execute(array($id));
        $curr = $stmt->fetch();

        if ($curr) {
            return new Department(intval($curr['id']), $curr['name']);
        }
        return null;
    }

    static function extractDepartmentWithName(PDO $db, string $name): ?Department
    {
        $stmt = $db->prepare('
                SELECT *
                FROM Department
                WHERE name = ?
            ');
        $stmt->execute(array($name));
        $curr = $stmt->fetch();

        if ($curr) {
            return new Department(intval($curr['id']), $curr['name']);
        }
        return null;
    }

    static function existsDepartment(PDO $db, string $name): bool
    {
        $stmt = $db->prepare('
            SELECT *
            FROM Department
            WHERE name = ?
            ');
        $stmt->execute(array($name));

        $department = $stmt->fetch();

        return $department ? true : false;
    }

    static function createDepartment(PDO $db, string $name): ?Department
    {
        if (Department::existsDepartment($db, $name)) {
            return null;
        }

        $stmt = $db->prepare('
            INSERT INTO Department (name) VALUES (?)
            ');
        $stmt->execute(array("$name"));
        return Department::extractDepartmentWithName($db, $name);
    }

    public function deleteDepartment(PDO $db)
    {
        $stmt = $db->prepare('
            DELETE FROM Department WHERE id = ?
            ');
        $stmt->execute(array($this->id));
    }

    static function getAllDepartmentNames(PDO $db): array
    {
        $stmt = $db->prepare("SELECT name FROM Department");
        $stmt->execute();
        $raw = $stmt->fetchAll();
        $departments = [];
        foreach ($raw as $array) {
            array_push($departments, $array['name']);
        }
        sort($departments);
        return $departments;
    }

    static public function getAllDepartments(PDO $db): array
    {
        $stmt = $db->prepare('
                SELECT *
                FROM Department
            ');
        $stmt->execute();
        $curr = $stmt->fetchAll();

        $departments = [];
        foreach ($curr as $curr) {
            array_push($departments, new Department(intval($curr['id']), $curr['name']));
        }
        sort($departments);
        return $departments;
    }
}
