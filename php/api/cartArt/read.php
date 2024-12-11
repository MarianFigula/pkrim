<?php

/**
 * Description:
 * This endpoint allows retrieving a list of art IDs associated with a user's cart.
 * It supports the GET method and requires a `user_id` to fetch the cart and its associated art data.
 *
 * Method: GET
 * URL: /api/cart_art/read.php
 *
 * Query Parameters:
 * - `user_id` (int, required): The ID of the user whose cart and associated art data should be retrieved.
 *
 * Response Codes:
 * - 200 OK: Successfully retrieved the list of art IDs associated with the cart.
 * - 400 Bad Request: Missing or invalid `user_id` query parameter.
 * - 404 Not Found: No cart found for the provided user ID, or no associated art found.
 * - 405 Method Not Allowed: The endpoint only supports the GET method.
 * - 500 Internal Server Error: Failed to retrieve cart or art data due to a server error.
 *
 * Notes:
 * - The HTTP method must be GET; any other method results in a 405 response.
 * - The `user_id` query parameter is mandatory and must be a valid integer.
 * - The script first retrieves the cart for the given `user_id` and then fetches the associated art IDs using the `Cart` and `CartArt` classes.
 * - If no cart or associated art is found, appropriate error messages are returned.
 * - Proper error handling is implemented for invalid input, not found cases, and server-side errors.
 */


header("Content-Type: application/json");
include_once '../../config/Database.php';
include_once '../../classes/CartArt.php';
include_once '../../classes/Cart.php';
include_once "../../config/cors.php";
include_once '../../config/Auth.php';

use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;

$database = new Database();
$db = $database->getConnection();

$cart = new Cart($db);
$cartArt = new CartArt($db);

$method = $_SERVER['REQUEST_METHOD'];

if ($method !== "GET") {
    http_response_code(405);
    echo json_encode([
        "success" => false,
        "message" => "Method not allowed. Only GET is permitted."
    ]);
    exit();
}

$user_id = $decoded->id;

try {
    $cart->setUserId($user_id);
    $stmt = $cart->getCartByUserId();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        http_response_code(404);
        echo json_encode([
            "success" => false,
            "message" => "Cart not found for the user."
        ]);
        exit();
    }

    // Get all art IDs from the user's cart
    $cartArt->setCartId($row["id"]);
    $stmt = $cartArt->getCartArtsByCartId();
    $cartArts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($cartArts)) {
        echo json_encode([
            "success" => true,
            "data" => []
        ]);
        exit();
    }

    $artIds = array_column($cartArts, "art_id");

    echo json_encode([
        "success" => true,
        "data" => $artIds
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "An error occurred while fetching cart art IDs: " . $e->getMessage()
    ]);
}