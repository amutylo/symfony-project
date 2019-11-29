<?php


namespace App\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Entity\AuthoredEntityInterface;
use App\Entity\BlogPost;
use App\Entity\Comment;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Class AuthoredEntitySubscriber
 * Intercept event "getAuthenticatedUser" in order to get user by provided token
 * and add such user as an author of the blog post.
 * @package App\EventSubscriber
 */
class AuthoredEntitySubscriber implements EventSubscriberInterface
{
  private $tokenStorage;

  public function __construct(TokenStorageInterface $tokenStorage)
  {
    $this->tokenStorage = $tokenStorage;
  }

  /**
   * @inheritDoc
   */
  public static function getSubscribedEvents()
  {
    return [
      KernelEvents::VIEW => ['getAuthenticatedUser', EventPriorities::PRE_WRITE]
    ];
  }

  public function getAuthenticatedUser(ViewEvent $event)
  {
     $entity = $event->getControllerResult();
     $method = $event->getRequest()->getMethod();

    /** @var UserInterface $author  */
     $author = $this->tokenStorage->getToken()->getUser();
     
     if ((!$entity instanceof AuthoredEntityInterface) || Request::METHOD_POST !== $method) {
       return;
     }
     /** Add currently authorized user as the author of the blog post entity */
     $entity->setAuthor($author);
  }
}