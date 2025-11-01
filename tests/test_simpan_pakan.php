<?php
// Test case 1: Successful insertion
$ch = curl_init('http://localhost:8080/simpan_pakan.php');
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, 'berat=500g');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$output = curl_exec($ch);
curl_close($ch);
$expected_output = '{"success":true}';
if ($output === $expected_output) {
    echo "Test case 1 passed\n";
} else {
    echo "Test case 1 failed\n";
    echo "Expected: $expected_output\n";
    echo "Actual: $output\n";
}

// Test case 2: No data received
$ch = curl_init('http://localhost:8080/simpan_pakan.php');
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, '');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$output = curl_exec($ch);
curl_close($ch);
$expected_output = '{"success":false,"message":"No data received"}';
if ($output === $expected_output) {
    echo "Test case 2 passed\n";
} else {
    echo "Test case 2 failed\n";
    echo "Expected: $expected_output\n";
    echo "Actual: $output\n";
}
?>
