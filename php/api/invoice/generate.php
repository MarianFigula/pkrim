<?php
require __DIR__ . '/../../vendor/autoload.php';

include_once '../../config/Database.php';
include_once '../../classes/User.php';
include_once "../../config/cors.php";
include_once '../../config/Auth.php';

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

$method = $_SERVER['REQUEST_METHOD'];
if ($method !== "GET") {
    http_response_code(405);
    echo json_encode([
        "success" => false,
        "message" => "Method not allowed"
    ]);
    exit();
}

$database = new Database();
$db = $database->getConnection();

$user = new User($db);

if (!isset($_GET['url'])) {
    header("Content-Type: application/json");
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "URL parameter is required."]);
    exit();
}

try {
    $url = $_GET['url'];
    $user_id = $decoded->id;

    $user->setId($user_id);

    $stmt = $user->getUserById();
    $user_row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user_row) {
        http_response_code(404);
        echo json_encode([
            "success" => false,
            "message" => "User not found."
        ]);
        exit();
    }

    $url = $_GET['url'];
    $htmlContent = @file_get_contents($url);

    if ($htmlContent === false) {
        header("Content-Type: application/json");
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Failed to fetch HTML content from the URL."]);
        exit();
    }

    $cartArtDetails = json_decode($_GET['arts'], true);
    $totalToPay = $_GET['total'];

    $htmlContent = str_replace(
        ['{{USER_NAME}}', '{{USER_EMAIL}}', '{{TOTAL}}'],
        [htmlspecialchars($user_row['username']), htmlspecialchars($user_row['email']), htmlspecialchars($totalToPay)],
        $htmlContent
    );

    $artRows = '';
    foreach ($cartArtDetails as $art) {
        $artRows .= '
    <tr>
        <td>' . htmlspecialchars($art['title']) . '</td>
        <td>$' . htmlspecialchars($art['price']) . '</td>
    </tr>';
    }

    $htmlContent = str_replace('{{ART_ROWS}}', $artRows, $htmlContent);

    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

    $pdf->SetCreator('Your Name');
    $pdf->SetAuthor('Your Name');
    $pdf->SetTitle('Invoice');
    $pdf->SetSubject('Invoice');

    $pdf->AddPage();

    $pdf->writeHTML($htmlContent, true, false, true, false, '');

    $pdf->Output('invoice.pdf', 'D');
}catch (Exception $e) {
    header("Content-Type: application/json");
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "An error occurred, Please try again."]);
    exit();
}