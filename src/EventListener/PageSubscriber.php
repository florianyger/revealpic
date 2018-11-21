<?php

namespace App\EventListener;

use App\Entity\Page;
use App\Service\PictureService;
use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;

class PageSubscriber implements EventSubscriber
{
    /**
     * @var string
     */
    private $pictureService;

    /**
     * @param PictureService $pictureService
     */
    public function __construct(PictureService $pictureService)
    {
        $this->pictureService = $pictureService;
    }

    /**
     * @see EventSubscriber
     */
    public function getSubscribedEvents()
    {
        return [
            Events::postPersist,
        ];
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        if ($entity instanceof Page) {
            $em = $args->getObjectManager();

            $pieces = $this->pictureService->cutPictureInPieces($entity);
            foreach ($pieces as $piece) {
                $em->persist($piece);
            }

            $em->flush();
        }
    }
}
