<?php
# GNU/Linux
# PHP 7

# Started 14.05.19 at 19:29
# 21:07 countAverageLineCount done.
#
# 22:10 Suspended due to sleep.
# 10:39 Resume.
# 11:02 Fully works.

// TODO Comments.

# TODO Absolute paths.
$users_filename = 'people.csv';
$txt_dir = 'texts/';
// TODO Create if does not exist.
$txt_output_dir = 'output_texts/';
$filename_delim = '-';
$action = 'countAverageLineCount';

// TODO get from args.
$csv_delim = ';';
$usage_str = "Usage: $argv[0] [comma/semicolon] [countAverageLineCount/replaceDates]" . PHP_EOL;

function countUserAvgLines($files, $user_id) {
  global $filename_delim, $txt_dir;
  $files_num = 0;
  $lines_num = 0;
  $result = 0.0;
  foreach ($files as $filename) {
    if ((int)explode($filename_delim, $filename)[0] === $user_id) {
      $files_num++;
      $file = fopen($txt_dir . $filename, 'r');
      while (fgets($file)) {
        $lines_num++;
      }
      fclose($file);
    }
  }
  if ($files_num) $result = $lines_num / $files_num;
  return $result;
}

function countAverageLineCount($users, $files) {
  foreach($users as $user_id => $username) {
    echo "$username: " . countUserAvgLines($files, $user_id) . PHP_EOL;
  }
}

function replaceDates($users, $files) {
  global $filename_delim, $txt_dir, $txt_output_dir;
  $users_stat = array();
  foreach ($users as $user_id => $username) $users_stat[$user_id] = 0;
  foreach ($files as $filename) {
    $file = fopen($txt_dir . $filename, 'r');
    $file_new = fopen($txt_output_dir . $filename, 'w');
    while ($line = fgets($file)) {
      $user_id = (int)explode($filename_delim, $filename)[0];
      // TODO Make regex some more precise.
      // В задании не уточнялось, как расширять год, так что считается, что все даты относятся к XXI веку.
      $line = preg_replace('&(\d+)/(\d+)/(\d+)&', '$2-$1-20$3', $line, -1, $counter);
      $users_stat[$user_id] += $counter;
      fwrite($file_new, $line);
    }
    fclose($file);
    fclose($file_new);
  }
  foreach ($users as $user_id => $username) {
    echo "$username: ${users_stat[$user_id]}" . PHP_EOL;
  }
}

if ($argc > 3) {
  echo $usage_str;
  exit;
}

for ($i = 1; $i < $argc; $i++) {
  $arg = $argv[$i];
  switch ($arg) {
  case 'comma':
    $csv_delim = ',';
    break;
  case 'semicolon':
    $csv_delim = ';';
    break;
  case 'countAverageLineCount':
    $action = 'countAverageLineCount';
    break;
  case 'replaceDates':
    $action = 'replaceDates';
    break;
  default:
    echo $usage_str;
    exit;
  }
}

$users_file = fopen($users_filename, 'r');
if (!$users_file) exit;
while ($parsed = fgetcsv($users_file, 0, $csv_delim)) {
  $users[(int)$parsed[0]] = $parsed[1];
}
fclose($users_file);

// TODO Include path.
$txt_files = array_diff(scandir($txt_dir), array('..', '.'));


switch ($action) {
case 'countAverageLineCount':
  countAverageLineCount($users, $txt_files);
  break;
case 'replaceDates':
  replaceDates($users, $txt_files);
  break;
}

?>
