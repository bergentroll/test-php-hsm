<?php
# GNU/Linux
# PHP 7

# Started 14.05.19 at 19:29

$users_filename = 'people.csv';
$txt_dir = "texts";

// TODO get from args.
$delim = ';';

//$users_file = file($users_filename);
//$users = array_map('str_getcsv', $users_file);

$users_file = fopen($users_filename, 'r');
if (!$users_file) exit;

while ($parsed = fgetcsv($users_file, 0, $delim)) {
  $users[$parsed[0]] = $parsed[1];
}

$txt_files = array_diff(scandir($txt_dir), array('..', '.'));

var_dump($txt_files);

?>
