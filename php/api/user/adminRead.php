<?php
header("Content-Type: application/json");

include_once '../../config/Database.php';
include_once '../../classes/User.php';
include_once "../../config/cors.php";
include_once '../../config/Auth.php';

$database = new Database();
$db = $database->getConnection();

$user = new User($db);

$method = $_SERVER['REQUEST_METHOD'];

if ($method !== "GET"){
    http_response_code(405);
    echo json_encode([
        "success" => false,
        "message" => "Method not allowed"]);
    exit();
}

try {
    if ($decoded->role !== "S"){
        echo json_encode([
            "success" => false,
            "message" => "Not admin"]);
        exit();
    }

    $stmt = $user->getAllUsers();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    http_response_code(200); // Success
    echo json_encode([
        "success" => true,
        "data" => $users,
        "admin" => $decoded->role
    ]);
    exit();
} catch (InvalidArgumentException $e) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
    exit();
} catch (Exception $e) {
    http_response_code(500); // Internal Server Error
    echo json_encode([
        "success" => false,
        "message" => "An error occurred: " . $e->getMessage()
    ]);
}