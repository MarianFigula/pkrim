<?php
/**
 * Description:
 * This endpoint allows creating a new art entry with details like title, description, price, and image.
 * The art data must be provided via a POST request, and an image file must be uploaded as well.
 *
 * Method: POST
 * URL: /api/art/create.php
 * 
 * Example CURL request that suceeded:
 * curl -X POST http://localhost/api/art/create.php -F "email=alicebobova@gmail.com" -F "title=My Artwork" -F "description=This is a test description" -F "price=100" -F "file=@C:/Resources/R.jpg"
 * the file path is going to be different and contain the image.
 * As far as I know, POSTMAN can't really post image in this way.
 *
 * Request Body (Form Data):
 * - "email": "user@example.com"          (string, required)
 * - "title": "Artwork Title"             (string, required)
 * - "description": "Artwork description" (string, required)
 * - "price": 100                         (integer, optional)
 * - "file": (file, required) - Image of the artwork
 *
 * Response Codes:
 * - 201 Created: Art was successfully created.
 * - 400 Bad Request: Invalid input provided or missing required fields.
 * - 404 Not Found: User not found.
 * - 500 Internal Server Error: Failed to create art due to server error.
 */

// IDEA: Consider ideas for checking image duplication

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Expose-Headers: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Max-Age: 3600"); // Cache the preflight response for 1 hour
header("Content-Type: application/json");

include_once '../../config/Database.php';
include_once '../../classes/Art.php';
include_once '../../classes/User.php';
include_once "../../config/cors.php";
include_once '../../config/Auth.php';

$database = new Database();
$db = $database->getConnection();

$art = new Art($db);

$method = $_SERVER['REQUEST_METHOD'];

if ($method !== "POST") {
    http_response_code(405);
    echo json_encode([
        "success" => false,
        "message" => "Method not allowed."
    ]);
    exit();
}

$file = $_FILES['file'];

if (!isset($_POST['title'], $_POST['description'], $_FILES['file'])) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Missing required fields."]);
    exit();
}

$allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif'];
$fileMimeType = mime_content_type($_FILES['file']['tmp_name']);

if (!in_array($fileMimeType, $allowedMimeTypes)) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Invalid file type."]);
    exit();
}

$title = $_POST['title'];
$description = $_POST['description'];
$price = isset($_POST['price']) ? intval($_POST['price']) : null;

$targetDir = '../../public/arts/';
$targetFile = $targetDir . basename($_FILES["file"]["name"]);

$img_url = "/arts/" . basename($_FILES["file"]["name"]);

if (!move_uploaded_file($_FILES["file"]["tmp_name"], $targetFile)) {
    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Failed to upload image."
    ]);
    exit();
}

try {
    $user_id = $decoded->id;

    // Set the Art properties
    $art->setUserId($user_id);
    $art->setImgUrl($img_url);
    $art->setTitle($title);
    $art->setDescription($description);
    $art->setPrice($price);

    // Insert the art
    if ($art->createArt()) {
        http_response_code(201); // Created
        echo json_encode([
            "success" => true,
            "message" => "Art successfully created.",
            "img_url" => $img_url
        ]);
    } else {
        throw new Exception("Failed to create art.");
    }

} catch (InvalidArgumentException $e) {
    http_response_code(200);
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
?>