GoogleMapBundle
====================

Bundle which provide Twig tags to build simple Google Maps, based on only https://maps.google.fr/maps web service.
This bundle wants to stay simple, to use all over Google Map API, see https://github.com/egeloen/IvoryGoogleMapBundle.

## Installation

First you need to add GoogleMapBundle to `composer.json`:
```json
    {
       "require": {
            "nyxis/google-map-bundle": "dev-master"
        }
    }
```

Then download from composer
```shell
php composer.phar update nyxis/google-map-bundle
```

You also have to add GoogleMapBundle to your `AppKernel.php`:
```php
    // app/AppKernel.php
    ...
    class AppKernel extends Kernel
    {
        ...
        public function registerBundles()
        {
            $bundles = array(
                ...
                new Nyxis\GoogleMapBundle\NyxisGoogleMapBundle()
            );
            ...

            return $bundles;
        }
        ...
    }
```
