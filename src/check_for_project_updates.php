<?php

// Read pml.json file.
$projects = json_decode(file_get_contents(__DIR__ . '/../cache/pml.json'), TRUE);

foreach ($projects as $project) {
  if (!empty($project['version'])) {
    // This is a project with a version, check for updates.
    // Projects on dev or custom projects don't have a version.
    $project_type = 'project_' . $project['type'];
    $url = 'https://www.drupal.org/api-d7/node.json?type=' . $project_type;
    $url .= '&field_project_machine_name=' . $project['name'];

    $project_json = json_decode(file_get_contents($url), TRUE);
    $project_nid = $project_json['list'][0]['nid'];

    var_dump($project_nid);

    break;
  }
}
