<?php

/**
 * Description:
 * This endpoint is designed to handle the creation of a new cart entry for a user.
 * It accepts a JSON payload in the request body and processes the data only if the HTTP method is POST.
 * The `user_id` is a mandatory field, and failure to provide it results in a 400 Bad Request error.
 *
 * Method: POST
 * URL: /api/cart/create.php
 *
 * Request Body:
 * {
 *   "user_id": 1  (int, required)
 * }
 *
 * Response Codes:
 * - 201 Created: Cart and associated art were successfully created.
 * - 400 Bad Request: Invalid input or missing required fields.
 * - 405 Method Not Allowed: The endpoint only supports the POST method.
 * - 500 Internal Server Error: An error occurred during the creation process.
 *
 * Notes:
 * - This endpoint interacts with the `Cart`, `User`, and `Art` classes to perform operations.
 * - It first validates the HTTP method and required an input (`user_id`).
 * - If the cart and art creation succeeds, it returns a success response with a 201 status code.
 * - Proper error handling is implemented for invalid input and server-side exceptions.
 */


header("Content-Type: application/json");

include_once '../../config/Database.php';
include_once '../../classes/Art.php';
include_once '../../classes/User.php';
include_once '../../classes/Cart.php';

$database = new Database();
$db = $database->getConnection();

$cart = new Cart($db);
$user = new User($db);
$art = new Art($db);

$method = $_SERVER['REQUEST_METHOD'];

$data = json_decode(file_get_contents("php://input"));

if ($method != "POST"){
    http_response_code(405);
    echo json_encode([
        "success" => false,
        "message" => "Method not allowed."]);
    exit();
}

if (!isset($data->user_id)) {
    http_response_code(400);
    echo json_encode(["message" => "Invalid input. User ID are required."]);
    exit;
}

try {
    $cart->setUserId($data->user_id);
    $stmt = $cart->createCart();

    if ($art->createArt()) {
        http_response_code(201);
        echo json_encode([
            "success" => true,
            "message" => "Art successfully created."
        ]);
    } else {
        throw new Exception("Failed to create art.");
    }

} catch (InvalidArgumentException $e) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
?>