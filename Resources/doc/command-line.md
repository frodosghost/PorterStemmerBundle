Porter Stemmer Bundle
============

Command Line Functionality
------------

Functionality has been added to create reference tables to save terms. When the command is run it will find all of the Entities within a specified bundle and Update the search table.

It is a simple way to add the Bundle and populate a Searchable table.

This command uses Doctrine2.

1. Setup the tables and relationships as in [Setup](https://github.com/frodosghost/PorterStemmerBundle/blob/master/Resources/doc/setup.md).

2. You can run the command as following:

    $ php app/console porter:stemmer AcmeDemoBundle:Post
