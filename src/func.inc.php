<?php

// Static.
define('ADSU_DRUPALORG_CORE_ID', 3060);
define('ADSU_DRUPALORG_SECURITY_UPDATES_VOCABULARY', 'taxonomy_vocabulary_7');
define('ADSU_DRUPALORG_SECURITY_UPDATES_TERM_ID', 100);

/**
 * Checks with the drupal.org API if there is an update available for a
 * specific project in a specific major and minor version.
 *
 * @param int $project_nid
 *   The node id of the project on drupal.org.
 * @param int $major_version
 *   The currently installed major version of the project.
 * @param int $minor_version
 *   The currently installed minor version of the project.
 * @param int $patch_version
 *   The currently installed patch version of the project.
 *
 * @return bool
 *   If there is an update available.
 */
function check_for_updates($project_nid, $major_version, $minor_version, $patch_version) {
  $url = 'https://www.drupal.org/api-d7/node.json';
  $url .= '?type=project_release&sort=nid&direction=DESC';
  $url .= '&field_release_project=' . $project_nid;

  if ($project_nid === ADSU_DRUPALORG_CORE_ID) {
    $url .= '&field_release_version_major=' . $major_version;
    $url .= '&field_release_version_minor=' . $minor_version;
  }
  else {
    // Contrib projects on drupal.org are stored differently
    $url .= '&field_release_version_major=' . $minor_version;
  }

  $project_releases = json_decode(file_get_contents($url), TRUE);
  $update_available = FALSE;

  foreach ($project_releases['list'] as $project_release) {
    if (get_version_parts($project_release['field_release_version'])[0] != $major_version) {
      // Not our major version, skip.
      continue;
    }

    if ($project_release['field_release_version_patch'] <= $patch_version) {
      // We've reached the currently installed patch version of the project
      // without finding any security updates.
      break;
    }

    // Check if this release was a security release.
    if (array_key_exists(ADSU_DRUPALORG_SECURITY_UPDATES_VOCABULARY, $project_release)) {
      foreach ($project_release[ADSU_DRUPALORG_SECURITY_UPDATES_VOCABULARY] as $term) {
        if ($term['id'] == ADSU_DRUPALORG_SECURITY_UPDATES_TERM_ID) {
          $update_available = TRUE;
          break;
        }
      }
    }
  }

  return $update_available;
}

/**
 * Returns the separated major, minor and patch versions of a string.
 * Also removes the 'x-' so 8.x-1.26 would be resolved to 8.1.26.
 *
 * @param string $version
 *   The version string to be separated, e.g. 8.x-1.26.
 *
 * @return array
 *   An array containing the major, minor and patch versions.
 */
function get_version_parts($version) {
  $version_parts = [];
  $string_parts = explode('.', $version);

  foreach ($string_parts as $string_part) {
    $version_parts[] = str_replace('x-', '', $string_part);
  }

  return $version_parts;
}

/**
 * Returns the drupal.org node id for a specific project.
 *
 * @param $project_type
 *   The project type, e.g. 'module' or 'theme'.
 * @param $project_name
 *   The project machine name, e.g. 'webform'.
 *
 * @return int|null
 *   The project's drupal.org node id or NULL if not found.
 */
function get_project_nid($project_name, $project_type) {
  // Prepend 'project' for drupal.org.
  $project_type = 'project_' . $project_type;

  // Check if we have cached the drupal.org nid for this project.
  $project_filename = __DIR__ . '/../cache/' . $project_name . '.nid';

  if (file_exists($project_filename)) {
    return file_get_contents($project_filename);
  }

  $url = 'https://www.drupal.org/api-d7/node.json?type=' . $project_type;
  $url .= '&field_project_machine_name=' . $project_name;

  $project_json = json_decode(file_get_contents($url), TRUE);

  if (!empty($project_json['list'])) {
    $project_nid = $project_json['list'][0]['nid'];

    // Cache the project nid.
    file_put_contents($project_filename, $project_nid);

    return $project_nid;
  }

  return NULL;
}

function prepare_update_file() {
  $update_filename = __DIR__ . '/../cache/update.txt';
  file_put_contents($update_filename, '');
}

function add_to_update_file($package) {
  $update_filename = __DIR__ . '/../cache/update.txt';
  $command = 'composer update drupal/' . $package . ' --with-dependencies' . PHP_EOL;
  file_put_contents($update_filename, $command, FILE_APPEND);
}
