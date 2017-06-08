<?php
// A simple test file for php-clamav-scan library
require 'Clamav.php';

// Directory where test files will be written
$test_dir = '/tmp';

// EICAR is a test string for AV scanners: https://en.wikipedia.org/wiki/EICAR_test_file
$bad_test = 'X5O!P%@AP[4\PZX54(P^)7CC)7}$EICAR-STANDARD-ANTIVIRUS-TEST-FILE!$H+H*';
$good_test = 'This is a safe string!';

$clamav = new Clamav();
if(file_put_contents("$test_dir/clamav_test.txt", $good_test)) {
    echo "Testing a good file...\n";
    if($clamav->scan("$test_dir/clamav_test.txt")) {
        echo "YAY, file is safe!\n";
    } else {
        echo "BOO, file is a virus!\n";
    }
    unlink("$test_dir/clamav_test.txt");
}
if(file_put_contents("$test_dir/clamav_test.txt", $bad_test)) {
    echo "Testing a bad file...\n";
    if($clamav->scan("$test_dir/clamav_test.txt")) {
        echo "YAY, file is safe!\n";
    } else {
        echo "BOO, file is a virus!\n";
    }
    unlink("$test_dir/clamav_test.txt");
}

