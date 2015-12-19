## Fyuze Debug Component

This is the debug component used by fyuze. It provides an a simple toolbar providing information about your registered services though provided collectors.

## Notes

This documentation is a small excerpt of the [original]() and is purely used for demonstrating usage 
outside the framework.

## Usage
    

### Initialize

Instantiate your toolbar as soon as possible:

    $toolbar = new Toolbar();

### Add collectors
 
Accessing and changing config values is done via get and set methods.
 
    $toolbar->addCollector(new \Fyuze\Debug\Collectors\Performance);
    
### Rendering the toolbar

Getting the toolbar markup is easy:

    $toolbar->render();
    
Based on your application components, you may be able to register the toolbar when responses are dispatched, here's an example used in the skeleton when bootstraping fyuze:


    $container->make('response')->modify(function ($body) use ($container) {
        return str_replace('</body>', $container->make('toolbar')->render() . '</body>', $body);
    });
   
## License

This Fyuze component is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT).
