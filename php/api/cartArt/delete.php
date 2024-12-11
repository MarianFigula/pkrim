<?php

/**
 * Description:
 * This endpoint allows removing a specific art item from a user's cart.
 * It requires the DELETE method and a JSON request body containing the `user_id` and `art_id`.
 *
 * Method: DELETE
 * URL: /api/cart_art/delete.php
 *
 * Request Body:
 * {
 *   "user_id": 1,   (int, required) - The ID of the user whose cart is being modified.
 *   "art_id": 123   (int, required) - The ID of the art to remove from the cart.
 * }
 *
 * Response Codes:
 * - 200 OK: Art successfully removed from the cart.
 * - 400 Bad Request: Missing or invalid `user_id` or `art_id` in the request body.
 * - 404 Not Found: Cart not found for the specified user.
 * - 405 Method Not Allowed: The endpoint only supports the DELETE method.
 * - 500 Internal Server Error: Failed to remove the art due to a server-side error.
 *
 * Notes:
 * - The HTTP method must be a DELETE; any other method results in a 405 response.
 * - Both `user_id` and `art_id` are required in the request body.
 * - The script uses the `Cart` and `CartArt` classes to locate the user's cart and remove the specified art item.
 * - If the cart or art is not found, appropriate error messages are returned.
 * - Proper error handling is implemented for invalid input, not found cases, and server-side errors.
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
$cartArt = new CartArt($db);

$method = $_SERVER['REQUEST_METHOD'];

if ($method !== 'DELETE') {
    http_response_code(405); // Method Not Allowed
    echo json_encode([
        "success" => false,
        "message" => "Invalid request method."
    ]);
    exit();
}

$data = json_decode(file_get_contents("php://input"));

if (empty($data->art_id)) {
    http_response_code(400); // Bad Request
    echo json_encode([
        "success" => false,
        "message" => "Art ID is required."
    ]);
    exit();
}

$user_id = $decoded->id; // Populated by `auth.php`
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

    $cart_id = $row["id"];
    $cartArt->setCartId($cart_id);


    $cartArt->setArtId($art_id);

    //echo json_encode($cart_id, $art_id);
    //exit();

    if ($cartArt->deleteCartArtByCartIdAndArtId()) {
        http_response_code(200);
        echo json_encode([
            "success" => true,
            "message" => "Art successfully removed from the cart."
        ]);
    } else {
        throw new Exception("Failed to remove art from the cart.");
    }

    // TODO volanie moze byt aj ine napr api/buy a tam sa zavola ta funkcia a na delete bude iba z kosika a mozno to dat do ls len
    // aby sa nemuselo refreshovat - alebo na button click sa odstrani

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "An error occurred: " . $e->getMessage()
    ]);
}