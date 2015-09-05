## Fyuze Kernel Component

This is core component for Fyuze that brings all the component pieces together. Currently, it this library
depends on `fyuze/http` which will only be the case until the PSR-7 implementation is live.

## Notes

This documentation is a small excerpt of the [original]() and is purely used for demonstrating usage 
outside the framework. Also, be sure to remember that this component converts all errors into [ErrorExecptions](http://php.net/ErrorException).

## Usage

### Bootstrap your application

You need to define some constants to be consumed by the kernel, this is the implementation fyuze uses:

    // bootstrap.php
    define('APP_START', microtime(true));
    define('APP_PATH', __DIR__);
    define('BASE_PATH', APP_PATH . '/../');
    
    require BASE_PATH . '/vendor/autoload.php';

### Create your application

#### Web Application

Once you've created your web application, you may visit the file that has the following code over HTTP.

    use Fyuze\Kernel\Application\Web;
    
    include __DIR__ . '/../app/bootstrap.php';
    
    (new Web(BASE_PATH))->boot()->send();

#### Console Application

Console applications don't use requests/responses, they use input and output streams so execution is a bit different:

    // filename: fyuze
    use Fyuze\Kernel\Application\Console;
    
    include __DIR__ . '/../app/bootstrap.php';
    
    (new Console(BASE_PATH))->boot();
    
To execute, just call the file with the via command line, e.g: `php fyuze`
     
## License

This Fyuze component is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT).
