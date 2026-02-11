<?php
require_once 'config.php';

header('Content-Type: text/plain; charset=utf-8');

echo "DB check\n";

// Print connection info
if ($conn->connect_error) {
    echo "Connection error: " . $conn->connect_error . "\n";
    exit;
}

$db_res = $conn->query("SELECT DATABASE() AS db");
if ($db_res) {
    $row = $db_res->fetch_assoc();
    echo "Using database: " . ($row['db'] ?? 'unknown') . "\n";
} else {
    echo "Could not determine database: " . $conn->error . "\n";
}

$count_res = $conn->query("SELECT COUNT(*) AS cnt FROM cars");
if ($count_res) {
    $r = $count_res->fetch_assoc();
    echo "Cars table row count: " . ($r['cnt'] ?? '0') . "\n";
} else {
    echo "Error querying cars table: " . $conn->error . "\n";
    echo "Tip: import database_setup.sql or create the 'cars' table.\n";
}

// Show first 5 rows (if any) for debugging
$rows = $conn->query("SELECT id, make, model, year, price FROM cars LIMIT 5");
if ($rows) {
    echo "\nSample rows:\n";
    while ($row = $rows->fetch_assoc()) {
        echo implode(' | ', $row) . "\n";
    }
} else {
    echo "No sample rows or error: " . $conn->error . "\n";
}

?>
