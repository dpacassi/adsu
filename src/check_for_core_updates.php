<?php

// Includes.
require_once 'func.inc.php';

// Read core.info file.
$core_version = file_get_contents(__DIR__ . '/../cache/core.info');
$core_version = explode(':', $core_version);
$core_version = trim($core_version[1]);

$core_version_parts = get_version_parts($core_version);

$update_core = check_for_updates(
  ADSU_DRUPALORG_CORE_ID,
  $core_version_parts[0],
  $core_version_parts[1],
  $core_version_parts[2]
);

prepare_update_file();

if ($update_core) {
  add_to_update_file('core');
}
