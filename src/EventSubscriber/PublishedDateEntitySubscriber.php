<?php


namespace App\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Entity\PublishedDateEntityInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\ViewEvent;

/**
 * Class AuthoredEntitySubscriber
 * Intercept event "getAuthenticatedUser" in order to get user by provided token
 * and add such user as an author of the blog post.
 *
 * @package App\EventSubscriber
 */
class PublishedDateEntitySubscriber implements EventSubscriberInterface {

  /**
   * @inheritDoc
   */
  public static function getSubscribedEvents() {
    return [
      KernelEvents::VIEW => ['setDatePublished', EventPriorities::PRE_WRITE]
    ];
  }

  public function setDatePublished(ViewEvent $event) {
    $entity = $event->getControllerResult();
    $method = $event->getRequest()->getMethod();
    
    if ((!$entity instanceof PublishedDateEntityInterface) || Request::METHOD_POST !== $method) {
      return;
    }
    /** Add currently authorized user as the author of the blog post entity */
    $entity->setPublished(new \DateTime());
  }
}