<?php

/**
 * Description:
 * This endpoint allows for the deletion of a cart entry based on the provided `cart_id`.
 * It only supports the DELETE method and requires the `cart_id` to be included in the request body as a JSON payload.
 *
 * Method: DELETE
 * URL: /api/cart/delete.php
 *
 * Request Body:
 * {
 *   "cart_id": 123  (int, required)
 * }
 *
 * Response Codes:
 * - 200 OK: Cart was successfully deleted.
 * - 400 Bad Request: Missing or invalid `cart_id` in the request.
 * - 405 Method Not Allowed: The endpoint only supports the DELETE method.
 * - 500 Internal Server Error: Failed to delete the cart due to a server error.
 *
 * Notes:
 * - The HTTP method must be a DELETE; any other method results in a 405 response.
 * - The `cart_id` is mandatory and must be a valid integer.
 * - The script uses the `Cart` class to perform the deletion operation.
 * - Proper error handling is implemented for invalid input and server-side errors.
 */


header("Content-Type: application/json");
include_once '../../config/Database.php';
include_once '../../classes/Cart.php';

$database = new Database();
$db = $database->getConnection();

$cart = new Cart($db);

$method = $_SERVER['REQUEST_METHOD'];

if ($method !== "DELETE") {
    http_response_code(405);
    echo json_encode([
        "success" => false,
        "message" => "Method not allowed. Use DELETE to delete cart data."
    ]);
    exit();
}

$data = json_decode(file_get_contents("php://input"));

if (!isset($data->cart_id)) {
    http_response_code(400);
    echo json_encode(["message" => "Cart ID is required and should be a number."]);
    exit;
}

$cart->setId($data->cart_id);

// TODO: if user will be deleted, uncomment code below

//if ($cart->deleteCartById()) {
//    echo json_encode([
//        "success" => true,
//        "message" => "Cart deleted successfully."
//    ]);
//} else {
//    echo json_encode([
//        "success" => false,
//        "message" => "Failed to delete cart."
//    ]);
//    http_response_code(500);
//}

?>
