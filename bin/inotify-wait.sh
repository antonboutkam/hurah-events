#!/bin/sh

# Usage:
# ./inotify-wait.sh <event-directory> <handler-name> <handler-fully-qualified-class-name>
#


echo "Starting listener in $1"
DIRECTORY="$1/$3/$2_listener/inbox"

echo "Event directory $DIRECTORY";

if [ ! -d "$DIRECTORY" ]; then
  echo "Creating $DIRECTORY which will act as the event inbox"
  mkdir -p "$DIRECTORY"
fi

inotifywait -m "$DIRECTORY" -e create |
    while read path action file; do
	       echo "$action - $path - $file"
	       /usr/bin/pwd
        /usr/bin/php ./application.php worker:runner $2 $3 $4 $1
    done

echo "Listener stopped"
