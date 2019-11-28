<?php


namespace App\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class PasswordHashSubscriber
 * Subscribing to the password hashing event
 * Intercept password and hash it before it save to the DB.
 * @package App\EventSubscriber
 */
class PasswordHashSubscriber  implements EventSubscriberInterface
{

  /**
   * @var UserPasswordEncoderInterface
   */
  private $passwordEncoder;

  public function __construct(UserPasswordEncoderInterface $passwordEncoder)
  {
       $this->passwordEncoder = $passwordEncoder;
  }

  public static function getSubscribedEvents() {
    return [
          KernelEvents::VIEW => ['hashPassword', EventPriorities::PRE_WRITE]
    ];
  }

  public function hashPassword(ViewEvent $event)
  {
    $user = $event->getControllerResult();
    $method = $event->getRequest()->getMethod();
    if (!$user instanceof User || !in_array($method, [Request::METHOD_POST, Request::METHOD_PUT])) {
      return;
    }

    //It is an User, we need to hash password here.
    $user->setPassword(
      $this->passwordEncoder->encodePassword($user, $user->getPassword())
    );
  }
}