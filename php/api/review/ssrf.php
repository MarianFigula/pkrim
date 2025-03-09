<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Expose-Headers: *");
header("Access-Control-Allow-Methods: POST, GET");
header("Access-Control-Max-Age: 3600"); // Cache the preflight response for 1 hour
header("Content-Type: application/json");

include_once '../../config/Database.php';
include_once "../../config/cors.php";

// ssrf.php - Vulnerable endpoint
if (isset($_GET['url'])) {
    $url = $_GET['url']; // No validation!

    // Your JWT token
    $authToken = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6IjMiLCJlbWFpbCI6ImJvYi5hbGljb3Z5QGdhbGxlcnkuZmVpIiwicm9sZSI6IlUiLCJleHAiOjE3NDQ4Nzg5MzV9.h7PGXy8FJw38tcdV84Fva0Ac7iWv4sGFQcbd-K6SjFI";
    // Set HTTP headers with Authorization token
    $options = [
        "http" => [
            "header" => "Authorization: Bearer " . $authToken
        ]
    ];

    $context = stream_context_create($options);

    // Perform the request
    $response = @file_get_contents($url, false, $context);
    var_dump($response); die;
    if ($response === FALSE) {
        echo json_encode([
            "success" => false,
            "message" => "Request failed"
        ]);
    } else {
        echo json_encode([
            "success" => true,
            "message" => $response
        ]);
    }

    exit();
}
