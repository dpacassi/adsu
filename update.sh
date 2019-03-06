#!/bin/bash

# Load up .env
set -o allexport
[[ -f .env ]] && source .env
set +o allexport

