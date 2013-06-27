<?php

namespace Manhattan\PorterStemmerBundle\Mapping\Annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * Stem annotation for PorterStemmer extension
 *
 * @Annotation
 * @Target("PROPERTY")
 *
 * @author James Rickard <james@frodosghost.com>
 */
final class Stem extends Annotation
{
    public $weight = 1;
}

