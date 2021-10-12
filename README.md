> :warning: **No locking**: This package is intended as a simple
solution for task delegation in situations where scaling won't be an issue. It
assumes each handler for a specific task runs only once. Running multiple
instances of the same handler concurrently may result in tasks being executed
multiple times.


# Hurah events
Simple file based task delegation system.

## Use cases
- Make generic software extensible.
- Move time consuming work to a separate background process.
- Prevent slow or possibly unavailable resources from slowing down the performance
of your system.

## Installation
```
    composer require hurah/events
```

## Setup
Since this is a file based system, an empty directory is needed for the system
to store it's data in. Running the handler can probably best be done with
something like Inotifywait if you want to have the event handler to run as
close to real-time as possible. A cron job could also be used if some delay is
not an issue and Inotifywait cannot be installed.

## Dispatch an event
From the code where a task or some tasks need to be delegated
```
$exampleContextData = ['product_id' => 123];
$dispatcher = new Dispatcher('/some/root/directory/where/events/will/live');
$dispatcher->dispatch('product/created', $exampleContextData);
```

## Implement handler(s)
Create a new class and have it extend AbstractHandler. In it's most basic form
your handler will only implement handleTask which you should use to implement
the logic required to do whatever job you have in mind.

```
class MyHandler extends AbstractHandler
{
    protected function handleTask(Context $myTaskData): int
    {
      return Task::SUCCESS;
    }
}
```
### Return type and failures
When implementing your handler as above, you must return the right directive
for the handler to know what to do next.


|  Return | Action  |
|---|---|
|Task::SUCCESS   | The task will be moved to an archive directory.   |
|Task::INVALID   | The task will be moved to an error directory for later manual processing / debugging / analysis. The handler will continue to process all other tasks waiting in the queue
|Task::FAILURE   | Processing of tasks is terminated, leaving the task that caused the problem in your inbox as the first task to pick up as soon as the failure is resolved.     |
|Task::RETRY   | The task will stay in the inbox, an attempt will be done in the next run to process the task, this will be repeated until maxAttempts() is reached which you must implement your self as maxAttempts() defautls to 0. Be carefull not to clog up your system. When the maximum number of attempts is hit the task is moved to the error directory.   |


## Events
When an event is dispatched, it’s identified by a unique name
(e.g. product/created), which any number of handlers might be listening to. A
sepearate json file containing the context data is stored for each handler.
When the handler is triggered each job is executed in sequential order.

### Event tree
The event type is made up out of one or more strings separated by a forward
slash. Event listeners can be bound to any level of the event tree. A listener
that is set up to listen for "product" events will be triggered when a "product/created" event was dispatched etc.

## Running handlers
You need some mechanism to trigger the handler code on demand or (not favorable)
run the handler code periodically to check for new events and handle them.

#### Manual / periodically
```
php bin/application.php worker:runner <handler_name> <event_type> <handler> <event_root>
```

|  Argument | Description  |
|---|---|
|handler_name   | Name used to uniquely identify this handler, will be mapped to an automatically created folder on disk.   |
|event_type   | The type of event that this handler will receive
|handler   | Fully qualified name of the class that implements the handling logic (e.g. MyNamespace\\MyHandler)     |
|event_root   | Should match with whatever you chose to be the root of your events   |

#### On demand / automatically
The bin folder contains the bash script below which you can use to monitor
event directories. This script should run an a per event basis, probably using
supervisor or perhaps systemd to autorestart in case something fails.

```
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
```
