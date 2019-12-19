<?php


namespace App\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Security\TokenGenerator;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Email\Mailer;

/**
 * Class PasswordHashSubscriber
 * Subscribing to the password hashing event
 * Intercept password and hash it before it save to the DB.
 * @package App\EventSubscriber
 */
class UserRegisterSubscriber  implements EventSubscriberInterface
{

  /**
   * @var UserPasswordEncoderInterface
   */
  private $passwordEncoder;

  /**
   * @var TokenGenerator
   */
  private $tokenGenerator;

  /**
   * @var Mailer
   */
  private $mailer;


  public function __construct(
    UserPasswordEncoderInterface $passwordEncoder,
    TokenGenerator $tokenGenerator,
    Mailer $mailer
  )
  {
    $this->passwordEncoder = $passwordEncoder;
    $this->tokenGenerator = $tokenGenerator;
    $this->mailer = $mailer;
  }

  public static function getSubscribedEvents() {
    return [
          KernelEvents::VIEW => ['userRegistered', EventPriorities::PRE_WRITE]
    ];
  }

  public function userRegistered(ViewEvent $event)
  {
    $user = $event->getControllerResult();
    $method = $event->getRequest()->getMethod();
    if (!$user instanceof User || !in_array($method, [Request::METHOD_POST])) {
      return;
    }

    //It is an User, we need to hash password here.
    $user->setPassword(
      $this->passwordEncoder->encodePassword($user, $user->getPassword())
    );

    //Generate confirmation token.
    $user->setConfirmationToken($this->tokenGenerator->geRandomSecureToken());

    // Send generated token by mail
    $this->mailer->sendConfirmationEmail($user);
  }
}