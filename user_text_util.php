<?php
# GNU/Linux
# PHP 7

# Started 14.05.19 at 19:29
# 21:07 countAverageLineCount done.
#
# 22:10 Suspended due to sleep.
# 10:39 Resume.
# 11:02 Fully works.

// Globals
/// Name of a csv formatted file with user identifiers and names.
$users_filename = 'people.csv';
/// Relative path to directory with users' files.
$txt_dir = 'texts/';
/// Relative path to directory for replaceDates output.
$txt_output_dir = 'output_texts/';
/// Delimiter used in filenames of users' files.
$filename_delim = '-';
/// Which delimiter supposed to be in file, pointed with $users_filename.
$csv_delim = ';';
/// Default action.
$action = 'countAverageLineCount';

/// Helper message.
$usage_str = "Usage: $argv[0] [comma/semicolon] [countAverageLineCount/replaceDates]" . PHP_EOL;

// Make relative paths absolute.
$cwd = getcwd();
$users_filename = "$cwd/$users_filename";
$txt_dir = "$cwd/$txt_dir";
$txt_output_dir = "$cwd/$txt_output_dir";

/** @brief Count average lines number for user's files.
 *  @param $files List of valid filenames to parse. Filenames must begins with
 *  user identifier and delimiter.
 *  @param $user_id Valid identifier of user. Must be integer.
 *  @param $dir Path to directory where files keeped.
 *  @param $delim Delimiter in filenames.
 *  @return Average number of lines for one user's file.
 */
function countUserAvgLines($files, $user_id, $dir, $delim = '-') {
  $files_num = 0;
  $lines_num = 0;
  $result = 0.0;
  foreach ($files as $filename) {
    if ((int)explode($delim, $filename)[0] === $user_id) {
      $files_num++;
      $file = fopen($dir . $filename, 'r');
      while (fgets($file)) {
        $lines_num++;
      }
      fclose($file);
    }
  }
  if ($files_num) $result = $lines_num / $files_num;
  return $result;
}

/** @brief Count average lines number for user's files for multiple users and print
 *  result to stdout.
 *  @param $users Array in format $user_id => $user_name.
 *  @param $files List of valid filenames to parse. Filenames must begins with
 *  user identifier and delimiter.
 *  @param $dir Path to directory where files keeped.
 *  @param $delim Delimiter in filenames.
 */
function countAverageLineCount($users, $files, $dir, $delim = '-') {
  foreach($users as $user_id => $username) {
    echo "$username: " . countUserAvgLines($files, $user_id, $dir, $delim) . PHP_EOL;
  }
}

/** @brief Change fromat of dates from dd-mm-yy to mm-dd-yyyy.
 *
 *  yy-formatted years will be expanded to 20yy.
 *  @param $users Array in format $user_id => $user_name.
 *  @param $files List of valid filenames to process. Filenames must begins with
 *  user identifier and delimiter.
 *  @param $input_dir Path to directory where files keeped.
 *  @param $output_dir Path to put processed files.
 *  @param $delim Delimiter in filenames.
 *  @throw Exception When $input_dir is the same as $output_dir.
 */
function replaceDates($users, $files, $input_dir, $output_dir, $delim = '-') {
  if ($input_dir == $output_dir) throw New Exception('Can not write files to input directory.');

  $users_stat = array();
  foreach ($users as $user_id => $username) $users_stat[$user_id] = 0;

  if (!file_exists($output_dir)) mkdir($output_dir);

  foreach ($files as $filename) {
    $user_id = (int)explode($delim, $filename)[0];
    if (!isset($users_stat[$user_id])) continue;
    $file = fopen($input_dir . $filename, 'r');
    $file_new = fopen($output_dir . $filename, 'w');
    while ($line = fgets($file)) {
      // Так как не указано иное, принято, что все даты относятся к XXI веку.
      $line = preg_replace('&([0-3]\d)/([0-1]\d)/(\d{2})[^\d]&', '$2-$1-20$3', $line, -1, $counter);
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

// Process command line arguments.
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

// Parse file with users info.
$users_file = fopen($users_filename, 'r');
if (!$users_file) exit;
while ($parsed = fgetcsv($users_file, 0, $csv_delim)) {
  $users[(int)$parsed[0]] = $parsed[1];
}
fclose($users_file);

// List files in directory with users' files.
$txt_files = array_diff(scandir($txt_dir), array('..', '.'));

// Perform selected action.
switch ($action) {
case 'countAverageLineCount':
  countAverageLineCount($users, $txt_files, $txt_dir, $filename_delim);
  break;
case 'replaceDates':
  replaceDates($users, $txt_files, $txt_dir, $txt_output_dir, $filename_delim);
  break;
}

?>
