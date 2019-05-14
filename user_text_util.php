<?php
# GNU/Linux
# PHP 7

# Started 14.05.19 at 19:29
# 21:07 countAverageLineCount done.
#
# TODO Absolute paths.

$users_filename = 'people.csv';
$txt_dir = 'texts/';
$txt_output_dir = '';
$filename_delim = '-';

// TODO get from args.
$csv_delim = ';';

function countUserAvgLines($files, $user_id) {
  global $filename_delim, $txt_dir;
  $files_num = 0;
  $lines_num = 0;
  $result = 0.0;
  foreach ($files as $filename) {
    if ((int)explode($filename_delim, $filename)[0] === $user_id) {
      $files_num++;
      $file = fopen($txt_dir . $filename, 'r');
      fgets($file);
      while (!feof($file)) {
        $lines_num++;
        fgets($file);
      }
    }
  }
  if ($files_num) $result = $lines_num / $files_num;
  return $result;
}

function countAverageLineCount($users, $files) {
  foreach($users as $user_id => $username) {
    echo $username . ': ' . countUserAvgLines($files, $user_id) . PHP_EOL;
  }
}

function replaceDates($users, $files) {
  global $filename_delim, $txt_dir;
  foreach ($files as $filename) {
    //if ((int)explode($filename_delim, $filename)[0] === $user_id) {
    $file = fopen($txt_dir . $filename, 'r');
  }
}

$users_file = fopen($users_filename, 'r');
if (!$users_file) exit;

while ($parsed = fgetcsv($users_file, 0, $csv_delim)) {
  $users[(int)$parsed[0]] = $parsed[1];
}

// TODO Include path.
$txt_files = array_diff(scandir($txt_dir), array('..', '.'));

//var_dump($txt_files);
echo countAverageLineCount($users, $txt_files);

?>
