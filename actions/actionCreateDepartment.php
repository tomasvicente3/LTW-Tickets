<?php

declare(strict_types=1);

require_once(__DIR__ . '/../utils/session.php');
$session = new Session();

if (!$session->isLoggedIn()) die(header('Location: /'));
if ($session->getClearance() !== "admin") die(header('Location: /'));
if ($_SESSION['csrf'] !== $_POST['csrf']) die(header('Location: /'));

require_once(__DIR__ . '/../database/connection.db.php');
require_once(__DIR__ . '/../database/department.class.php');

$db = getDatabaseConnection();
$departments = Department::getAllDepartmentNames($db);
$removed = array_diff($departments, $_POST['departments']);
$added = array_diff($_POST['departments'], $departments);

foreach ($added as $new) {
    Department::createDepartment($db, $new);
}

foreach ($removed as $old) {
    $department = Department::extractDepartmentWithName($db, $old);
    $department->deleteDepartment($db);
}

$session->addMessage("success", "Departments edited successfully!");
die(header("Location: ../pages/system.php"));
