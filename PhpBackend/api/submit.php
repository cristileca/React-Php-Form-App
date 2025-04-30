<?php
require_once "../classes/Database.php";
require_once "../classes/Validator.php";
require_once "../classes/ImageHandler.php";
require_once "../classes/AuthHelper.php";

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Authorization, Content-Type");
header("Access-Control-Allow-Methods: POST, OPTIONS");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$headers = getallheaders();

if (!isset($headers['Authorization']) || $headers['Authorization'] !== 'Bearer ' . AUTH_TOKEN) {
    http_response_code(401);
    echo json_encode(["success" => false, "message" => "Unauthorized"]);
    exit;
}

$data = $_POST;
$files = $_FILES;

$validator = new Validator($data, $files);
$errors = $validator->validate();

if (!empty($errors)) {
    http_response_code(400);
    echo json_encode(["success" => false, "errors" => $errors]);
    exit;
}

try {
    $db = new Database();
    $conn = $db->getConnection();
} catch (\Throwable $th) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Database connection error"]);
    exit;
}

$email = $conn->real_escape_string($data['email']);
$name = $conn->real_escape_string($data['name']);
$imagePath = $conn->real_escape_string($imagePath ?? '');
$consent = ($data['consent'] ?? 'false') === 'true' ? 1 : 0;

$uniqueCheckQuery = "SELECT COUNT(*) FROM users WHERE email = '$email'";

$result = $conn->query($uniqueCheckQuery);
if ($result && $result->fetch_row()[0] > 0) {
    http_response_code(422);
    echo json_encode(["success" => false, "message" => "Email already exists"]);
    exit;
}

$imageHandler = new ImageHandler($files['image'] ?? null);
$imagePath = $imageHandler->processUpload();

$sql = "INSERT INTO users (email, name, consent, image_path) 
        VALUES ('$email', '$name', $consent, '$imagePath')";

if ($conn->query($sql)) {
    http_response_code(200);
    echo json_encode(["success" => true, "message" => "Form submitted successfully"]);
} else {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Database error"]);
}
