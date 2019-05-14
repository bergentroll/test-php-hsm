<?php
# GNU/Linux
# PHP 7

# Started 14.05.19 at 19:29

$users_filename = 'people.csv';
$txt_dir = "texts";

// TODO get from args.
$delim = ';';

function countAverageLineCount($files, $user_id) {
  foreach ($files as $filename) {
    if (explode('-', $filename)[0] == $user_id) {
      echo "$filename" + PHP_EOL;
    }
  }
  $count = 0;
  return $count;
}

$users_file = fopen($users_filename, 'r');
if (!$users_file) exit;

while ($parsed = fgetcsv($users_file, 0, $delim)) {
  $users[$parsed[0]] = $parsed[1];
}

$txt_files = array_diff(scandir($txt_dir), array('..', '.'));

//var_dump($txt_files);
countAverageLineCount($txt_files, 0);

?>
