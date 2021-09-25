# Hurah events
Simple file / folder based task delegation system that employs Inotifywait for task delegation and async event handling.

## Example
On the creator side you dispath an event, in the example an event is dispatched because a product was created. A 
separate process that employs inotify wait will receive the event and pass it to the appropriate handler with the 
context data that you provided.

### Setup
The worker / listener part needs to run in a separate process. You need to set this up using for instance supervisord 
and inotify wait must be installed on the system to work. 

### Create your event handler
Create a class that implements \Hurah\Event\HandlerInterface and implement the handle and getTypes

You can start the worker / lister part as follows
php ./bin/application.php worker:runner '/some/root/directory/where/events/will/live' '\Fully\Qualified\Name\Of\Your\Handler'

### Event trigger
$dispatcher = new Dispatcher('/some/root/directory/where/events/will/live');
$dispatcher->dispatch('product/created', $contextData);

### Event listener
The listener runs in a separate process, for instance via a process manager,perhaps supervisor or maybe systemd.

$listener = new Listener('/some/root/directory/where/events/will/live');





## Use case
When you want some piece of code to be triggered as an individual process.

## How it works
The Delegator class is passed an event type and some context data. The event type is a directory structure like string
that is mapped directly to a folder on the harddisk. When registering a listener for a specific event type the listener
will create a sub folder inside the event type folder. When an event is fired the delegator will iterate over all the
listeners directories and stores a copy of the event data as a json encoded string. Listeners are running as individual 
processes each monitoring the contents of their inbox via inotify wait.

## Creator


## Example
```
delegator.php

$someData = ['product_id' => 2];

$delegator = new Delegator("/event/root/path");
$delegator->trigger("/product/created", $someData)
```


```
handler.php

$listener = new Listener("/event/root/path");
$listener->register('product/created', YourHandler::class);

```