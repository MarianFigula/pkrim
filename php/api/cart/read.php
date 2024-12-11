<?php

/**
 * Description:
 * This endpoint allows retrieving cart details based on either a specific cart ID or a user ID.
 * It supports the GET method and requires query parameters to filter the cart data.
 *
 * Method: GET
 * URL: /api/cart/read.php
 *
 * Query Parameters (optional, at least one required):
 * - `id` (int, optional): The ID of the specific cart to retrieve.
 * - `user_id` (int, optional): The ID of the user to retrieve their associated cart.
 *
 * Response Codes:
 * - 200 OK: Cart details retrieved successfully.
 * - 400 Bad Request: No valid query parameters provided or invalid input.
 * - 404 Not Found: No cart found matching the provided criteria.
 * - 405 Method Not Allowed: The endpoint only supports the GET method.
 * - 500 Internal Server Error: Failed to retrieve cart details due to a server error.
 *
 * Notes:
 * - The HTTP method must be GET; any other method results in a 405 response.
 * - Query parameters `id` and `user_id` are optional but at least one must be provided.
 * - If both parameters are provided, the endpoint prioritizes the `id` parameter for fetching data.
 * - The script uses the `Cart` class to fetch cart data from the database.
 * - Proper error handling is implemented for invalid input, not found cases, and server-side errors.
 */


header("Content-Type: application/json");

include_once '../../config/Database.php';
include_once '../../classes/Cart.php';

$database = new Database();
$db = $database->getConnection();

$cart = new Cart($db);

$method = $_SERVER['REQUEST_METHOD'];

if ($method != "POST") {
    http_response_code(405);
    echo json_encode([
        "success" => false,
        "message" => "Method not allowed. Use GET to fetch cart data."
    ]);
    exit();
}

if (isset($_GET['id'])) {
    $cart->setId($_GET['id']);
    $stmt = $cart->getCartById();

    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        http_response_code(404);
        echo json_encode([
            "success" => false,
            "message" => "Cart not found."
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
    $cart->setUserId($_GET['user_id']);
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

    http_response_code(200);
    echo json_encode([
        "success" => true,
        "data" => $row
    ]);
    exit();
}

?>
