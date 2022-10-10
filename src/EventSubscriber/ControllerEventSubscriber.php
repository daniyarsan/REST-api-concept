<?php

namespace App\EventSubscriber;

use App\Controller\PageController;
use App\Entity\Author;
use App\Service\Util;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ControllerEventSubscriber implements EventSubscriberInterface
{
    public function __construct(
        protected EntityManagerInterface $entityManager,
        protected Util $util,
    )
    {
    }

    public function onKernelController(ControllerEvent $event): void
    {
        $controller = $event->getController();

        // when a controller class defines multiple action methods, the controller
        // is returned as [$controllerInstance, 'methodName']
        if (is_array($controller)) {
            $controller = $controller[0];
        }

        if ($controller instanceof PageController) {
            $requestData = json_decode($event->getRequest()->getContent(), true);

            /* Create Author on the fly */
            if (isset($requestData['authorName'])) {
                $authorSlug = $this->util->slugify($requestData['authorName']);
                $author = $this->entityManager->getRepository(Author::class)->findOneBy(['slug' => $authorSlug]);
                if (!$author) {
                    $author = new Author();
                    $author->setSlug($authorSlug);
                    $this->entityManager->persist($author);
                    $this->entityManager->flush();
                }
            }
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelController',
        ];
    }
}
