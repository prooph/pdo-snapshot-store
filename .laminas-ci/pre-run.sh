#!/bin/bash

# about pre/post run commands 
# see https://github.com/laminas/laminas-continuous-integration-action/#prepost-commands
# The .laminas-ci/pre-run.sh command runs immediately prior to the QA command, and will receive the following arguments:

# $1: the user the QA command will run under
# $2: the WORKDIR path
# $3: the $JOB passed to the entrypoint

set -e

TEST_USER=$1
WORKSPACE=$2
JOB=$3

COMMAND=$(echo "${JOB}" | jq -r '.command')

if [[ ! ${COMMAND} =~ phpunit ]]; then
    exit 0
fi

if [ ! -f .laminas-ci/phpunit.xml."$DB_VENDOR" ]
then
    echo "File .laminas-ci/phpunit.xml.$DB_VENDOR does not exist"
    exit 1
else
    echo "Installing '/.laminas-ci/phpunit.xml.$DB_VENDOR' version as '/phpunit.xml'"
    cp .laminas-ci/phpunit.xml."$DB_VENDOR" phpunit.xml
    exit 0
fi
