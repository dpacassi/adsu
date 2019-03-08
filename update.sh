#!/bin/bash

# Load up .env
set -o allexport
[[ -f .env ]] && source .env
set +o allexport

CURRENT_PATH="$(pwd)"

if [ -z "$ADSU_PHP_BIN" ]; then
  ADSU_PHP_BIN=php
fi

# Create the pml.csv file.
if [ ! -z "$ADSU_DRUPAL_PRODUCTION_PATH" ]; then
  cd $ADSU_DRUPAL_PRODUCTION_PATH && ./vendor/bin/drush pml --format=json --status=enabled --no-core --fields="name,type,version" > "$CURRENT_PATH"/cache/pml.json
  cd $ADSU_DRUPAL_PRODUCTION_PATH && ./vendor/bin/drush status | grep "Drupal version" > "$CURRENT_PATH"/cache/core.info
else
  if [ -z "$ADSU_DRUPAL_PRODUCTION_SA" ]; then
    echo "Please provide a .env file and either provide the ADSU_DRUPAL_PRODUCTION_PATH or the ADSU_DRUPAL_PRODUCTION_SA variable."
    exit 1
  fi

  cd $ADSU_DRUPAL_UPDATE_PATH && ./vendor/bin/drush "$ADSU_DRUPAL_PRODUCTION_SA" pml --format=json --status=enabled --no-core --fields="name,type,version" > "$CURRENT_PATH"/cache/pml.json
  cd $ADSU_DRUPAL_UPDATE_PATH && ./vendor/bin/drush "$ADSU_DRUPAL_PRODUCTION_SA" status | grep "Drupal version" > "$CURRENT_PATH"/cache/core.info

  # Remove the last line of the json file.
  # Since we've used a Drush site alias, Drush adds a "Connection to <ip> closed." message.
  sed -i '' -e '$ d' "$CURRENT_PATH"/cache/pml.json
fi

# Retrieve update data for core.
eval "cd ${CURRENT_PATH} && ${ADSU_PHP_BIN} ./src/check_for_core_updates.php"

# Retrieve update data per project.
eval "cd ${CURRENT_PATH} && ${ADSU_PHP_BIN} ./src/check_for_project_updates.php"
