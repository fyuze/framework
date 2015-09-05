## Fyuze Config Component

This is the config component used by fyuze. It provides an easy way to process supported config files with php.

## Notes

This documentation is a small excerpt of the [original]() and is purely used for demonstrating usage 
outside the framework. Also, be sure to remember that this component converts all errors into [ErrorExecptions](http://php.net/ErrorException).

## Usage

### Initialize

Just initialize the class after you've bootstrapped your application with the directory containing your configs:

    $config = new \Fyuze\Config\Config('path/to/configs');

### Getters/Setters
 
Accessing and changing config values is done via get and set methods.
 
    // Access config with defualt value
    $config->get('foo', 'bar'); // If fo is not set, bar will be returned
     
    // If foo does not exist, null will be returned
    $config->get('foo');
    
    // Config values can also be set at runtime
    $config->set('foo', 'bar');
    
## Todo

- Write configs to file
- Arbitrary parameters for setting multiple configs at once. e.g: `$config->set(['foo' => 'bar', 'bar' => 'baz']);`
   
## License

This Fyuze component is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT).
