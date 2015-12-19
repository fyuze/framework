## Fyuze Event Component

This is the event component used for registering and firing events(yes very descriptive).

## Notes

This documentation is a small excerpt of the [original]() and is purely used for demonstrating usage 
outside the framework.

## Usage

### Initialize emitter

Just initialize the class after you've bootstrapped your application.

    $emitter = new \Fyuze\Event\Emitter;

### Event listeners

Event registration just takes a name and closure, the emitter will return the response of the last listener registered.
 
     $emitter->listen('foo', function() {
        return 'bar';
     });
    
Multiple listeners can be registered to the same event:

    $emitter->listen('foo', function() {
        return 'baz';
    });
    
You may also check if any listeners have been registered for a specific event:

    if($emitter->has('bar') === false) {
        ...
    }
    
And drop all listeners for a specific event:

    $emitter->drop('foo');
    
### Firing events

Firing events just requires the first parameter which takes the event name. optional parameters may be supplied to pass to the listener.

    $result = $emitter->emit('foo'); // returns 'baz' - based on example above with multiple listeners
    
### Psr Logging

The event emitter also optionally supports logging, though the psr logging interface.

    $emitter = (new \Fyuze\Event\Emitter)
        ->setLogger(new MyPsrLogger);
      
This will logs for events fired and events fired without registered listeners.

## Todo

- Customizable logging
- Event subscribers
     
## License

This Fyuze component is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT).
