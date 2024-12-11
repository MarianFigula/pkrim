<?php

/**
 * Description:
 * This endpoint allows adding a specific art item to a user's cart.
 * It requires the POST method and a JSON request body containing the `user_id` and `art_id`.
 *
 * Method: POST
 * URL: /api/cart_art/create.php
 *
 * Request Body:
 * {
 *   "user_id": 1,   (int, required) - The ID of the user whose cart is being updated.
 *   "art_id": 123   (int, required) - The ID of the art to add to the cart.
 * }
 *
 * Response Codes:
 * - 201 Created: Art successfully added to the cart.
 * - 400 Bad Request: Missing or invalid `user_id` or `art_id` in the request body.
 * - 404 Not Found: Cart not found for the specified user.
 * - 405 Method Not Allowed: The endpoint only supports the POST method.
 * - 500 Internal Server Error: Failed to add the art due to a server-side error.
 *
 * Notes:
 * - The HTTP method must be POST; any other method results in a 405 response.
 * - Both `user_id` and `art_id` are required in the request body.
 * - The script uses the `Cart` and `CartArt` classes to locate the user's cart and add the specified art item.
 * - If the cart is not found, an appropriate error message is returned.
 * - Proper error handling is implemented for invalid input, not found cases, and server-side errors.
 */

header("Content-Type: application/json");

include_once '../../config/Database.php';
include_once '../../classes/CartArt.php';
include_once '../../classes/Cart.php';
include_once '../../classes/User.php';
include_once "../../config/cors.php";
include_once '../../config/Auth.php';

$database = new Database();
$db = $database->getConnection();

$cartArt = new CartArt($db);
$cart = new Cart($db);
$user = new User($db);

$method = $_SERVER['REQUEST_METHOD'];

if ($method !== "POST") {
    http_response_code(405);
    echo json_encode([
        "success" => false,
        "message" => "Method not allowed."
    ]);
    exit();
}

$data = json_decode(file_get_contents("php://input"));

if (empty($data->art_id)) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Art ID is required."
    ]);
    exit();
}

$user_id = $decoded->id; // `Auth.php` populates the $decoded variable
$art_id = $data->art_id;

try {
    $cart->setUserId($user_id);
    $stmt = $cart->getCartByUserId();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        http_response_code(404);
        echo json_encode([
            "success" => false,
            "message" => "Cart not found."
        ]);
        exit();
    }

    $cartArt->setCartId($row["id"]);
    $cartArt->setArtId($art_id);

    if ($cartArt->createCartArt()) {
        http_response_code(201);
        echo json_encode([
            "success" => true,
            "message" => "CartArt successfully created."
        ]);
    } else {
        throw new Exception("Failed to create CartArt.");
    }


}catch (Exception $e){
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Failed to delete reviews: " . $e->getMessage()
    ]);
}

