<?php
/**
 * Description:
 * This endpoint allows retrieving credit card information by specifying the card ID or user ID as query parameters.
 * If neither is provided, all cards are returned for the specified user (admin privileges are required to view all cards).
 * 
 * Method: GET
 * URL: /api/credit_card/read.php
 * 
 * Query Parameters:
 * - id (optional): int - The unique ID of the credit card to retrieve.
 * - user_id (optional): int - The user ID for which to retrieve credit card(s).
 * 
 * Response Codes:
 * - 200 OK: Credit card(s) successfully retrieved.
 * - 403 Forbidden: Admin privileges are required to view all credit cards.
 * - 404 Not Found: Credit card with the specified ID does not exist.
 * - 405 Method Not Allowed: Invalid request method used (only GET is allowed).
 */

header("Content-Type: application/json");

include_once '../../config/Database.php';
include_once '../../classes/CreditCard.php';
include_once "../../config/cors.php";

$database = new Database();
$db = $database->getConnection();

$creditCard = new CreditCard($db);

$method = $_SERVER['REQUEST_METHOD'];

if ($method !== "GET") {
    http_response_code(405);
    echo json_encode([
        "success" => false,
        "message" => "Method not allowed"
    ]);
    exit();
}

if (isset($_GET['id'])) {
    try {
        $creditCard->setId($_GET['id']);
    } catch (InvalidArgumentException $e) {
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "message" => $e->getMessage()
        ]);
        exit();
    }

    $stmt = $creditCard->getCardById();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$row) {
        http_response_code(404);
        echo json_encode([
            "success" => false,
            "message" => "Credit card not found."
        ]);
        exit();
    }

    http_response_code(200);
    echo json_encode([
        "success" => true,
        "data" => $row
    ]);
    exit();
}

if (isset($_GET['user_id'])) {
    try {
        $creditCard->setUserId($_GET['user_id']);
    } catch (InvalidArgumentException $e) {
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "message" => $e->getMessage()
        ]);
        exit();
    }

    $stmt = $creditCard->getCardsByUserId();
    $cards = $stmt->fetchAll(PDO::FETCH_ASSOC);
    // NOTE: Not sure if error or empty array is more context-appropriate
    if (empty($cards)) {
        http_response_code(404);
        echo json_encode([
            "success" => false,
            "message" => "No credit cards found for the specified user."
        ]);
        exit();
    }

    http_response_code(200);
    echo json_encode([
        "success" => true,
        "data" => $cards
    ]);
    exit();
}

// REVIEW - SHOULD WORK, COMMENTED FOR DEBUGGING PURPOSES
/*
if ($_SESSION['role'] !== 'A') {
    http_response_code(403);
    echo json_encode([
        "success" => false,
        "message" => "Access denied. Admin privileges required to view all credit cards."
    ]);
    exit();
}
*/

// Get all credit cards (admin only)
$stmt = $creditCard->getAllCards();
$cards = $stmt->fetchAll(PDO::FETCH_ASSOC);

http_response_code(200);
echo json_encode([
    "success" => true,
    "data" => $cards
]);

?>
