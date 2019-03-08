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
  $url .= '&field_release_version_major=' . $major_version;
  $url .= '&field_release_version_minor=' . $minor_version;

  $project_releases = json_decode(file_get_contents($url), TRUE);
  $update_available = FALSE;

  foreach ($project_releases['list'] as $project_release) {
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
