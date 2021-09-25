#!/bin/sh

while getopts r:a:f: flag
do
    case "${flag}" in
        r) ROOT_DIR=${OPTARG};;
        a) age=${OPTARG};;
        f) fullname=${OPTARG};;
    esac
done

echo "Starting listener"

inotifywait -m "$ROOT_DIR/*/*/inbox -e create |
    while read path action file; do
        /usr/bin/php ./application.php exobrain:message:processor
    done
echo "Listener stopped"
