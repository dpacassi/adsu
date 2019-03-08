<?php

// Includes.
require_once 'func.inc.php';

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

    if (!empty($project_json['list'])) {
      $project_nid = $project_json['list'][0]['nid'];
      $project_version_parts = get_version_parts($project['version']);
      $update_project = check_for_updates(
        $project_nid,
        $project_version_parts[0],
        $project_version_parts[1],
        $project_version_parts[2]
      );

      var_dump($update_project);

      break;
    }
  }
}
