<?php

require __DIR__ . "/vendor/autoload.php";

$my_notes = file_get_contents($argv[1]);

$taskNamePattern = "/^- (\[ ]|\[x]).+/m";
$taskDescriptionPattern = "/^\s+- (\[ ]|\[x]).+/m";

$my_notes = explode(PHP_EOL, $my_notes);

foreach ($my_notes as $line) {
  if (!$line) {
    continue;
  }

  if (preg_match($taskNamePattern, $line, $nameMatches)) {
    $taskName = preg_replace("/^- (\[ ]|\[x])\s{1}/", "", $nameMatches[0]);
    $statusId = 3;

    if (preg_match("/^- \[x]/", $line, $statusClosedMatches)) {
        $statusId = 4;
    }

    $array[] = [
        "name" => $taskName,
        "statusId" => $statusId
    ];
  };

  $previousLine = &$array[count($array) - 1];

  if (preg_match($taskDescriptionPattern, $line, $descriptionMatches) && $previousLine) {
    $description = preg_replace("/^\s*/", "", $descriptionMatches[0]);
    isset($previousLine["description"])
      ? $previousLine["description"] .= PHP_EOL . $description
      : $previousLine["description"] = $description;
  }
}

$mysqli = mysqli_connect('127.0.0.1', 'sail', 'password', 'laravel', 3306);

date_default_timezone_set('Europe/Budapest');

$timestamp = time();

 foreach($array as $key => $item) {
     $created_at = date("Y-m-d H:i:s", $timestamp - $key);
     $name = $item['name'];
     $description = isset($item['description']) ? $item['description'] : "";
     $userId = 2;
     $statusId = $item['statusId'];

     $mysqli->query("INSERT INTO tasks (name, description, user_id, status_id, created_at) VALUES ('{$name}', '{$description}', '{$userId}', '{$statusId}', '{$created_at}')");
 }
