<?php
require_once "../classes/Database.php";
require_once "../classes/AuthHelper.php";

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Content-Type: application/json");

$headers = getallheaders();

if (!isset($headers['Authorization']) || $headers['Authorization'] !== 'Bearer ' . AUTH_TOKEN) {
    http_response_code(401);
    echo json_encode(["success" => false, "message" => "Unauthorized"]);
    exit;
}

$sort = $_GET['sort'] ?? 'email';
$order = $_GET['order'] ?? 'asc';
$page = max(1, (int) $_GET['page'] ?? 1);
$limit = max(10, (int) $_GET['limit'] ?? 10);

$validSortFields = ['email', 'name', 'created_at'];
if (!in_array($sort, $validSortFields)) {
    $sort = 'email';
}
if (!in_array($order, ['asc', 'desc'])) {
    $order = 'asc';
}

$offset = ($page - 1) * $limit;

try {
    $db = new Database();
    $conn = $db->getConnection();
} catch (\Throwable $th) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Database connection error"]);
    exit;
}

$sql = "SELECT * FROM users ORDER BY $sort $order LIMIT $limit OFFSET $offset";
$result = $conn->query($sql);

$records = [];
while ($row = $result->fetch_assoc()) {
    $records[] = $row;
}

$totalRecordsResult = $conn->query("SELECT COUNT(*) AS total FROM users");
$totalRecords = $totalRecordsResult->fetch_assoc()['total'];

$response = [
    "data" => $records,
    "total_records" => $totalRecords,
    "page" => $page,
    "limit" => $limit
];

echo json_encode($response);
