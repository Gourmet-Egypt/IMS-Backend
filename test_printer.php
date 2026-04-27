<?php

/**
 * Printer Connection Test Script
 * Run: php test_printer.php
 * Run with different IP: php test_printer.php 192.168.1.100
 */

$printerIp = $argv[1] ?? '192.168.27.84';
$printerPort = $argv[2] ?? 9100;
$timeout = 5; // seconds

echo "===========================================\n";
echo "Printer Connection Test\n";
echo "===========================================\n";
echo "IP: $printerIp\n";
echo "Port: $printerPort\n";
echo "Timeout: {$timeout}s\n";
echo "===========================================\n\n";

// Test 0: Show this server's IP
echo "[TEST 0] This server's network info...\n";
$ipconfig = shell_exec("ipconfig | findstr /i \"IPv4\"");
echo $ipconfig . "\n";

// Test 1: Basic ping
echo "[TEST 1] Ping test...\n";
$pingResult = shell_exec("ping -n 2 $printerIp");
echo $pingResult . "\n";

// Test 2: ARP table check (see if printer was ever seen)
echo "[TEST 2] ARP table check (known devices)...\n";
$arpResult = shell_exec("arp -a | findstr \"$printerIp\"");
if ($arpResult) {
    echo "Found in ARP table:\n$arpResult\n";
} else {
    echo "Not found in ARP table (device never communicated)\n";
}
echo "\n";

// Test 3: Traceroute (see where connection fails)
echo "[TEST 3] Traceroute (first 5 hops)...\n";
$traceResult = shell_exec("tracert -d -h 5 $printerIp");
echo $traceResult . "\n";

// Test 4: Port scan common printer ports
echo "[TEST 4] Testing common printer ports...\n";
$ports = [9100, 515, 631, 80, 443];
foreach ($ports as $port) {
    $socket = @fsockopen($printerIp, $port, $errno, $errstr, 2);
    if ($socket) {
        echo "  Port $port: OPEN\n";
        fclose($socket);
    } else {
        echo "  Port $port: CLOSED/FILTERED\n";
    }
}
echo "\n";

// Test 5: Socket connection to main port
echo "[TEST 5] Socket connection to port $printerPort...\n";
$socket = @fsockopen($printerIp, $printerPort, $errno, $errstr, $timeout);

if ($socket) {
    echo "SUCCESS: Connected to printer at $printerIp:$printerPort\n";

    // Test 6: Try sending a simple test
    echo "\n[TEST 6] Sending test data...\n";
    $testData = "\n\n\n";
    $bytesSent = fwrite($socket, $testData);

    if ($bytesSent !== false) {
        echo "SUCCESS: Sent $bytesSent bytes to printer\n";
    } else {
        echo "FAILED: Could not send data to printer\n";
    }

    fclose($socket);
    echo "\nConnection closed.\n";
} else {
    echo "FAILED: Could not connect to printer\n";
    echo "Error Code: $errno\n";
    echo "Error Message: $errstr\n";
}

// Test 6: Scan network for printers (optional)
echo "\n[TEST 6] Scanning local subnet for port 9100...\n";
$baseIp = implode('.', array_slice(explode('.', $printerIp), 0, 3));
echo "Scanning $baseIp.1 to $baseIp.10 (quick scan)...\n";
for ($i = 1; $i <= 10; $i++) {
    $testIp = "$baseIp.$i";
    $socket = @fsockopen($testIp, 9100, $errno, $errstr, 1);
    if ($socket) {
        echo "  FOUND PRINTER at $testIp:9100\n";
        fclose($socket);
    }
}
echo "Quick scan done.\n";

echo "\n===========================================\n";
echo "Test completed at " . date('Y-m-d H:i:s') . "\n";
echo "===========================================\n";
echo "\nUsage: php test_printer.php [IP] [PORT]\n";
echo "Example: php test_printer.php 192.168.1.100 9100\n";
