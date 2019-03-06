<?php

// Read pml.json file.
$projects = json_decode(file_get_contents(__DIR__ . '/../cache/pml.json'), TRUE);

foreach ($projects as $project) {
  var_dump($project);
}
