<?php

// Read core.info file.
$core_version = file_get_contents(__DIR__ . '/../cache/core.info');
$core_version = explode(':', $core_version);
$core_version = trim($core_version[1]);

var_dump($core_version);
