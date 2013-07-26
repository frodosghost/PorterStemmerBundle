Porter Stemmer Bundle
============

Setup
-----

Porter Stemmer Bundle allows the saving of stemmed words into a single table. It creates a search reference from a saved Entity into a specified table.

Currently the objects are setup with Annotations. Apologies.

1. Create the Search Entity

This entity is populated with a Word and a Weight each time the target entity is persisted. This table must have the fields word, weight and a relationship to the target entity.

This Entity does *not* have to be configured with annotations. It will be handled by a persist listener when the target entity is updated or created.

``` php
namespace Acme\DemoBundle\Entity;

use Acme\ExampleBundle\Entity\Post;

class Search
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $word;

    /**
     * @var string
     */
    private $weight;

    /**
     * @var Acme\ExampleBundle\Entity\Post
     */
    private $post;

    ...
}

```

2. Setup the object that will be searched for

The class tag specifies the class just created in step one.

``` php
...

namespace Acme\ExampleBundle\Entity;

use Manhattan\PorterStemmerBundle\Mapping\Annotation as Stem;

/**
 * Post
 *
 * @Stem\PorterStemmer(class="Acme\DemoBundle\Entity\Search")
 */
class Post
{
    ...
}

```

1. Add the Annotation to specify the fields to Stemmed

When the Entity is persisted each field specified with the Stem() annotation will have each word "stemmed" and saved into the __Search__ entity.

The weight option adds an importance to the field allowing you to adjust the value fields have within the __Search__ entity. It multiplies the words when they are saved, thus creating a higher count and allowing you to rank search results on the number of times a word appears.

``` php
...

/**
 * @var string
 *
 * @Stem\Stem(weight=3)
 */
private $title;

/**
 * @var string
 *
 * @Stem\Stem()
 */
private $body;

...

```
