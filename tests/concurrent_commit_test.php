<?php

if ($argc < 3) {
    echo "Usage: php concurrent_commit_test.php <purchase_order_id> <token> [num_requests]\n";
    exit(1);
}

$purchaseOrderId = $argv[1];
$token = $argv[2];
$numRequests = $argv[3] ?? 5;

$endpoint = "http://localhost/IMS/api/purchase-order/{$purchaseOrderId}/partial-commit";

echo "====================================================\n";
echo "        CONCURRENT COMMIT FORENSIC REPORT\n";
echo "====================================================\n";
echo "Endpoint: {$endpoint}\n";
echo "Requests: {$numRequests}\n";
echo "Start Time: ".date('Y-m-d H:i:s')."\n";
echo "----------------------------------------------------\n\n";

sleep(2);

$multi = curl_multi_init();
$handles = [];

$globalStart = microtime(true);

// create requests
for ($i = 0; $i < $numRequests; $i++) {

    $ch = curl_init();

    $payload = json_encode([
        'transactionType' => 'TransferIN',
        'Vehicle_tempIN' => 8,
        'isClosed' => 0,
    ]);

    curl_setopt_array($ch, [
        CURLOPT_URL => $endpoint,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $payload,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Accept: application/json',
            "Authorization: Bearer {$token}",
        ],
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 60,
    ]);

    curl_multi_add_handle($multi, $ch);

    $handles[$i] = [
        'ch' => $ch,
        'start' => microtime(true),
    ];
}

// execute
$running = null;
do {
    curl_multi_exec($multi, $running);
    curl_multi_select($multi);
} while ($running > 0);

$globalEnd = microtime(true);

echo "================ REQUEST DETAILS ================\n\n";

$results = [];

foreach ($handles as $i => $data) {

    $ch = $data['ch'];

    $response = curl_multi_getcontent($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $totalTime = curl_getinfo($ch, CURLINFO_TOTAL_TIME);

    $end = microtime(true);

    $decoded = json_decode($response, true);

    $statusText = match ($httpCode) {
        200 => "SUCCESS (COMMITTED)",
        409 => "BLOCKED (LOCKED)",
        default => "ERROR",
    };

    $results[] = [
        'req' => $i,
        'code' => $httpCode,
        'status' => $statusText,
        'time' => $totalTime,
        'message' => $decoded['message'] ?? 'N/A',
        'body' => $decoded,
    ];

    echo "Request #{$i}\n";
    echo "Status     : {$statusText}\n";
    echo "HTTP Code  : {$httpCode}\n";
    echo "Duration   : ".round($totalTime, 4)." sec\n";
    echo "Response   : ".($decoded['message'] ?? $response)."\n";
    echo "----------------------------------------------------\n";

    curl_multi_remove_handle($multi, $ch);
    curl_close($ch);
}

curl_multi_close($multi);

// sort by duration (to show real execution order)
usort($results, fn($a, $b) => $a['time'] <=> $b['time']);

echo "\n================ EXECUTION ORDER ================\n";

foreach ($results as $r) {
    echo "#{$r['req']} -> {$r['status']} ({$r['time']}s)\n";
}

echo "\n================ SUMMARY ========================\n";

$totalSuccess = count(array_filter($results, fn($r) => $r['code'] === 200));
$totalBlocked = count(array_filter($results, fn($r) => $r['code'] === 409));

echo "Total Time (wall clock): ".round($globalEnd - $globalStart, 3)." sec\n";
echo "SUCCESS: {$totalSuccess}\n";
echo "BLOCKED: {$totalBlocked}\n";

echo "\n=================================================\n";

if ($totalSuccess === 1 && $totalBlocked === ($numRequests - 1)) {
    echo "RESULT: LOCK MECHANISM WORKING PERFECTLY\n";
} else {
    echo "RESULT: POSSIBLE ISSUE - REVIEW REQUIRED\n";
}

echo "=================================================\n";
