<?php

// Static.
define('ADSU_DRUPALORG_CORE_ID', 3060);
define('ADSU_DRUPALORG_SECURITY_UPDATES_VOCABULARY', 'taxonomy_vocabulary_7');
define('ADSU_DRUPALORG_SECURITY_UPDATES_TERM_ID', 100);

// Read core.info file.
$core_version = file_get_contents(__DIR__ . '/../cache/core.info');
$core_version = explode(':', $core_version);
$core_version = trim($core_version[1]);

$core_version_parts = explode('.', $core_version);
$core_version_major = $core_version_parts[0];
$core_version_minor = $core_version_parts[1];
$core_version_patch = $core_version_parts[2];

$url = 'https://www.drupal.org/api-d7/node.json';
$url .= '?type=project_release&sort=nid&direction=DESC&field_release_project=3060';
$url .= '&field_release_version_major=' . $core_version_major;
$url .= '&field_release_version_minor=' . $core_version_minor;

$core_releases = json_decode(file_get_contents($url), TRUE);
$update_core = FALSE;

foreach ($core_releases['list'] as $core_release) {
  if ($core_release['field_release_version_patch'] <= $core_version_patch) {
    // We've reached our version, abort.
    break;
  }

  // Check if this release was a security release.
  if (array_key_exists(ADSU_DRUPALORG_SECURITY_UPDATES_VOCABULARY, $core_release)) {
    foreach ($core_release[ADSU_DRUPALORG_SECURITY_UPDATES_VOCABULARY] as $term) {
      if ($term['id'] == ADSU_DRUPALORG_SECURITY_UPDATES_TERM_ID) {
        $update_core = TRUE;
        break;
      }
    }
  }
}
