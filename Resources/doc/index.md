Porter Stemmer Bundle
============

Installation
------------

1. Add this bundle to your project in composer.json:

    PorterStemmer uses composer (http://www.getcomposer.org) to organize dependencies:

    ```json
    {
        "require": {
            "manhattan/porterstemmer-bundle": "dev-master",
        }
    }
    ```

2. Add this bundle to your app/AppKernel.php:

    ``` php
    // application/ApplicationKernel.php
    public function registerBundles()
    {
        return array(
            // ...
            new Manhattan\PorterStemmer\PorterStemmerBundle(),
            // ...
        );
    }
    ```

Documentation
-------------

1. [Setup](https://github.com/frodosghost/PorterStemmerBundle/blob/master/Resources/doc/setup.md)

2. [Command Line Functionality](https://github.com/frodosghost/PorterStemmerBundle/blob/master/Resources/doc/command-line.md)
