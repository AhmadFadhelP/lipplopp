<?php
// Test case 1: Successful insertion
$_POST['berat'] = '500g';
ob_start();
include '/home/lipplopp/playground/padell/lipplopp/simpan_pakan.php';
$output = ob_get_clean();
$expected_output = '{"success":true}';
if ($output === $expected_output) {
    echo "Test case 1 passed\n";
} else {
    echo "Test case 1 failed\n";
    echo "Expected: $expected_output\n";
    echo "Actual: $output\n";
}

// Test case 2: No data received
$_POST = [];
ob_start();
include '/home/lipplopp/playground/padell/lipplopp/simpan_pakan.php';
$output = ob_get_clean();
$expected_output = '{"success":false,"message":"No data received"}';
if ($output === $expected_output) {
    echo "Test case 2 passed\n";
} else {
    echo "Test case 2 failed\n";
    echo "Expected: $expected_output\n";
    echo "Actual: $output\n";
}
?>