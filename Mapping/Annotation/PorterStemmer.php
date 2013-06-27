<?php

namespace Manhattan\PorterStemmerBundle\Mapping\Annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * PorterStemmer annotation for PorterStemmer extension
 *
 * @Annotation
 * @Target("CLASS")
 *
 * @author James Rickard <james@frodosghost.com>
 */
final class PorterStemmer extends Annotation
{
    /** @var string @required */
    public $class;
}

