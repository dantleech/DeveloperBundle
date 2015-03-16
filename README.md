Sulu Developer Bundle
=====================

This bundle aims to include lots of fixtures to quickly setup a testing
environment for the [Sulu CMF](https://sulu.io).

Installation
------------

````bash
$ composer require dtl/developer-bundle
````

Add it to your application kernel:

```bash
// app/AbstractKernel.php
// ...

abstract class AbstractKernel extends SuluKernel
{
    
    public function registerBundles()
    {
        // ...
        if (in_array($this->getEnvironment(), array('dev', 'test'))) {
            $bundles[] = new Sulu\Bundle\DeveloperBundle\DeveloperBundle();
            // ...
        }

        //...
    }
}
````


Loading fixtures
----------------

Load the fixtures as follows:

````bash
$ ./app/console doctrine:fixtures:load
````

Or as part of the normal Sulu build process:

````bash
$ ./app/console sulu:build dev
````
