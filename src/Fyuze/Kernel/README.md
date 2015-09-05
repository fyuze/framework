## Fyuze Kernel Component

This is core component for Fyuze that brings all the component pieces together. Currently, it this library
depends on `fyuze/http` which will only be the case until the PSR-7 implementation is live. We also depend on `fyuze\config`
which is for finding and binding defined services, unsure of how long this dependency will remain for.

## Notes

This documentation is a small excerpt of the [original]() and is purely used for demonstrating usage 
outside the framework.

## Usage

### Create your Application

#### Bootstrapping

You need to define some constants to be consumed by the kernel, this is the implementation fyuze uses:

    // bootstrap.php
    define('APP_START', microtime(true));
    define('APP_PATH', __DIR__);
    define('BASE_PATH', APP_PATH . '/../');
    
    require BASE_PATH . '/vendor/autoload.php';

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

### Registry

The registry is fyuze's bastard child of an IoC container/service locator with a beyond simple api.

    $registry = Registry::init();
    $someClass = $registry->make('SomeClass');
    
The above demonstration shows how a class is bound and returned from the IoC, during creation, you'll always get an instance of the same object you provided.
    
Class dependencies are also resolved automatically if made from the registry, e.g:

    class A {}
    class B { 
      public function __construct(A $a) {}
    }
 
    $bWithDependency = $registry->make('B');
    
As you can see, without A ever being bound to the registry, we can still create and resolve it by creating B.
    



### Services

Binding additional services is a breeze and should be familiar if you've used a framework before.

    use Fyuze\Kernel\Service as BaseService;
    
    class MyService extends BaseService 
    {
        public function services() 
        {
            $this->registry->make('SomeClass');
        }
    }
    
Inside your defined APP_PATH, will you provide a folder called `configs` which will contain a file named `app.php`, here's some boilerplate:

    return [
        'timezone' => 'UTC',
        'charset' => 'utf-8',
        'services' => [
            'MyService'
        ],
    ];

     
## License

This Fyuze component is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT).
