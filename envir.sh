#!/usr/bin/env bash

set -e

if [ "$1" == "-n" ]
then
   USER_NAME=$(whoami)
   USER_UID=$(id -u)
   USER_GID=$(id -g)
else
    if [ ! -z "$1" ] && [ ! -z "$2" ] &&  [ ! -z "$3" ]
    then
        USER_NAME="$1"
        USER_UID="$2"
        USER_GID="$3"
    else
        echo "Wrong number of parameters"
        exit 2
    fi
fi

if [ ! -z "$USER_NAME" ] && [ ! -z "$USER_UID" ] && [ ! -z "$USER_GID" ]
then
    cat <<EOT > .envfile
    export USER_NAME=$USER_NAME
    export USER_UID=$USER_UID
    export USER_GID=$USER_GID
EOT
exit 0
fi

echo "Error during script execution"
exit 2
