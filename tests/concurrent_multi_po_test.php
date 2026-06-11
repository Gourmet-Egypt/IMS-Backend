<?php

/**
 * Concurrent Multi-PO Partial Commit Test
 *
 * Tests partial commit on multiple different POs simultaneously
 *
 * Usage: php tests/concurrent_multi_po_test.php <token> <po_id_1> <po_id_2> <po_id_3> ...
 */

if ($argc < 3) {
    echo "Usage: php concurrent_multi_po_test.php <token> <po_id_1> <po_id_2> ...\n";
    echo "Example: php concurrent_multi_po_test.php your-token 201421 201422 201423 201424 201425\n";
    exit(1);
}

$token = $argv[1];
$poIds = array_slice($argv, 2);

$baseUrl = 'http://localhost/IMS/api/purchase-order';

echo "====================================================\n";
echo "     CONCURRENT MULTI-PO PARTIAL COMMIT TEST\n";
echo "====================================================\n";
echo "PO IDs: " . implode(', ', $poIds) . "\n";
echo "Total POs: " . count($poIds) . "\n";
echo "Start Time: " . date('Y-m-d H:i:s') . "\n";
echo "----------------------------------------------------\n\n";

sleep(2);

$multi = curl_multi_init();
$handles = [];

$payload = json_encode([
    'transactionType' => 'TransferIN',
    'Vehicle_tempIN' => 8,
    'isClosed' => 0,
]);

// Create requests for each PO
foreach ($poIds as $i => $poId) {
    $endpoint = "{$baseUrl}/{$poId}/partial-commit";

    $ch = curl_init();

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
        CURLOPT_TIMEOUT => 120,
    ]);

    curl_multi_add_handle($multi, $ch);

    $handles[$i] = [
        'ch' => $ch,
        'po_id' => $poId,
        'endpoint' => $endpoint,
    ];
}

echo "Sending " . count($poIds) . " requests simultaneously...\n\n";

$globalStart = microtime(true);

// Execute all requests simultaneously
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
    $poId = $data['po_id'];

    $response = curl_multi_getcontent($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $totalTime = curl_getinfo($ch, CURLINFO_TOTAL_TIME);

    $decoded = json_decode($response, true);

    $statusText = match ($httpCode) {
        200 => "SUCCESS",
        409 => "CONFLICT (LOCKED)",
        404 => "NOT FOUND",
        422 => "VALIDATION ERROR",
        500 => "SERVER ERROR",
        default => "ERROR ({$httpCode})",
    };

    $results[] = [
        'po_id' => $poId,
        'code' => $httpCode,
        'status' => $statusText,
        'time' => $totalTime,
        'message' => $decoded['message'] ?? 'N/A',
    ];

    echo "PO #{$poId}\n";
    echo "Status     : {$statusText}\n";
    echo "HTTP Code  : {$httpCode}\n";
    echo "Duration   : " . round($totalTime, 4) . " sec\n";
    echo "Response   : " . ($decoded['message'] ?? substr($response, 0, 100)) . "\n";
    echo "----------------------------------------------------\n";

    curl_multi_remove_handle($multi, $ch);
    curl_close($ch);
}

curl_multi_close($multi);

// Sort by duration
usort($results, fn($a, $b) => $a['time'] <=> $b['time']);

echo "\n================ EXECUTION ORDER ================\n";
echo "(Sorted by response time)\n\n";

foreach ($results as $r) {
    $time = round($r['time'], 4);
    echo "PO #{$r['po_id']} -> {$r['status']} ({$time}s)\n";
}

echo "\n================ SUMMARY ========================\n";

$grouped = [
    'success' => [],
    'conflict' => [],
    'not_found' => [],
    'error' => [],
];

foreach ($results as $r) {
    if ($r['code'] === 200) {
        $grouped['success'][] = $r['po_id'];
    } elseif ($r['code'] === 409) {
        $grouped['conflict'][] = $r['po_id'];
    } elseif ($r['code'] === 404) {
        $grouped['not_found'][] = $r['po_id'];
    } else {
        $grouped['error'][] = $r['po_id'];
    }
}

$totalTime = round($globalEnd - $globalStart, 3);

echo "Total Time (wall clock): {$totalTime}s\n";
echo "SUCCESS     : " . count($grouped['success']) . " [" . implode(', ', $grouped['success']) . "]\n";
echo "CONFLICT    : " . count($grouped['conflict']) . " [" . implode(', ', $grouped['conflict']) . "]\n";
echo "NOT FOUND   : " . count($grouped['not_found']) . " [" . implode(', ', $grouped['not_found']) . "]\n";
echo "OTHER ERROR : " . count($grouped['error']) . " [" . implode(', ', $grouped['error']) . "]\n";

echo "\n=================================================\n";

if (count($grouped['success']) === count($poIds)) {
    echo "RESULT: ALL POs COMMITTED SUCCESSFULLY IN PARALLEL\n";
} elseif (count($grouped['success']) > 0) {
    echo "RESULT: PARTIAL SUCCESS - CHECK FAILED POs\n";
} else {
    echo "RESULT: ALL FAILED - REVIEW REQUIRED\n";
}

echo "=================================================\n";
