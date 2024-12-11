<?php

/**
 * Description:
 * This endpoint allows the user to buy and remove specific art items from their cart.
 * It requires the POST method and a JSON request body containing the `user_id` and an array of `art_ids` to be purchased.
 *
 * Method: POST
 * URL: /api/cart_art/buy.php
 *
 * Request Body:
 * {
 *   "user_id": 1,          (int, required) - The ID of the user whose cart is being updated.
 *   "art_ids": [1, 2, 3]   (array, required) - An array of art IDs to be bought and removed from the cart.
 * }
 *
 * Response Codes:
 * - 200 OK: Arts successfully bought and removed from the cart.
 * - 400 Bad Request: Missing or invalid `user_id` or `art_ids` in the request body.
 * - 404 Not Found: Cart not found for the specified user or art items not found.
 * - 405 Method Not Allowed: The endpoint only supports the POST method.
 * - 500 Internal Server Error: Failed to remove arts from the cart or delete them due to a server-side error.
 *
 * Notes:
 * - The HTTP method must be POST; any other method results in a 405 response.
 * - Both `user_id` and `art_ids` are required in the request body.
 * - The script uses the `Cart`, `CartArt`, and `Art` classes to locate the user's cart and remove the specified art items.
 * - If the cart is not found, an appropriate error message is returned.
 * - Proper error handling is implemented for missing or invalid data, not found cases, and server-side issues.
 */


header("Content-Type: application/json");
include_once '../../config/Database.php';
include_once '../../classes/Cart.php';
include_once '../../classes/Art.php';
include_once '../../classes/CartArt.php';
include_once "../../config/cors.php";
include_once '../../config/Auth.php';

$database = new Database();
$db = $database->getConnection();

$cart = new Cart($db);
$art = new Art($db);
$cartArt = new CartArt($db);

$method = $_SERVER['REQUEST_METHOD'];

if ($method !== "POST") {
    http_response_code(405);
    echo json_encode([
        "success" => false,
        "message" => "Method not allowed. Only POST is permitted."
    ]);
    exit();
}

$data = json_decode(file_get_contents("php://input"));

if (empty($data->art_ids) || !is_array($data->art_ids)) {
    http_response_code(400); // Bad Request
    echo json_encode(["success" => false, "message" => "Art IDs are required and must be an array."]);
    exit();
}

$user_id = $decoded->id; // Extracted from JWT
$art_ids = array_map('intval', $data->art_ids); // Sanitize art IDs

try {
    $cart->setUserId($user_id);
    $stmt = $cart->getCartByUserId();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        http_response_code(404); // Not Found
        echo json_encode(["success" => false, "message" => "Cart not found."]);
        exit();
    }

    $cart_id = $row["id"];
    $cartArt->setCartId($cart_id);

    if ($cartArt->clearCartArt($art_ids) && $art->deleteArtsByIds($art_ids)) {
        http_response_code(200);
        echo json_encode([
            "success" => true,
            "message" => "Arts successfully bought"
        ]);
        exit();
    } else {
        throw new Exception("Failed to remove art from the cart.");
    }
} catch (Exception $e) {
    // Rollback the transaction on error
    $db->rollBack();
    http_response_code(500); // Internal Server Error
    echo json_encode([
        "success" => false,
        "message" => "An error occurred while processing the purchase: " . $e->getMessage()
    ]);
}