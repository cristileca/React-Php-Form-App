<?php
require_once "../classes/Database.php";
require_once "../classes/AuthHelper.php";

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Content-Type: text/csv");
header("Content-Disposition: attachment; filename=users_export.csv");

$headers = getallheaders();

if (!isset($headers['Authorization']) || $headers['Authorization'] !== 'Bearer ' . AUTH_TOKEN) {
    http_response_code(401);
    echo json_encode(["success" => false, "message" => "Unauthorized"]);
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

$sql = "SELECT id, email, name, consent, image_path, created_at FROM users";
$result = $conn->query($sql);

$output = fopen('php://output', 'w');

fputcsv($output, ['ID', 'Email', 'Name', 'Consent', 'Image Path', 'Created At']);

while ($row = $result->fetch_assoc()) {
    fputcsv($output, $row);
}

fclose($output);
