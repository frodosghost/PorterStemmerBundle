<?php

namespace Manhattan\PorterStemmerBundle\EventListener;

use Doctrine\ORM\Events;
use Doctrine\Common\EventArgs;
use Doctrine\Common\EventSubscriber;

/**
 * StemmerListener
 *
 * @author James Rickard <james@frodosghost.com>
 */
class StemmerListener implements EventSubscriber
{
    /**
     * Specifies the list of events to listen
     *
     * @return array
     */
    public function getSubscribedEvents()
    {
        return array(
            Events::onFlush
        );
    }

    /**
     * Generate slug on objects being updated during flush
     * if they require changing
     *
     * @param EventArgs $args
     * @return void
     */
    public function onFlush(EventArgs $args)
    {
        $em = $eventArgs->getEntityManager();
        $uow = $em->getUnitOfWork();
    }

}
