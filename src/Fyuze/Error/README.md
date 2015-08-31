## Fyuze Error Component

This is the error handling component for fyuze. It is intended to provide a consistent and friendly
way of providing information about the error to the developer or sysadmin.

## Notes

This documentation is a small excerpt of the [original]() and is purely used for demonstrating usage 
outside the framework. Also, be sure to remember that this component converts all errors into [ErrorExecptions](http://php.net/ErrorException).

## Usage

### Initialize handler

Just initialize the class after you've bootstrapped your application.

    $handler = new \Fyuze\Error\ErrorHandler;

### Register new handler
 
 Registering a handler is easy, just describe the exception you want handle and how with a closure
 
     $handler->register('Acme\Exception\Whoops', function($exception) {
        echo 'my handler has problems';
        
        return true;
     });
     
   
## License

This Fyuze component is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT).
