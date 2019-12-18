<?php

namespace App\EventSubscriber;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use ApiPlatform\Core\EventListener\EventPriorities;

class UserConfirmationSubscriber implements EventSubscriberInterface
{

  /**
   * @var UserRepository
   */
  private $userRepository;

  /**
   * @var \Doctrine\ORM\EntityManager
   */
  private $entityManager;

  public function __construct(
    UserRepository $userRepository,
    EntityManagerInterface $entityManager
  )
  {

    $this->userRepository = $userRepository;
    $this->entityManager = $entityManager;
  }

  /**
   * @inheritDoc
   */
  public static function getSubscribedEvents() {
    return [
      KernelEvents::VIEW => ['confirmUser', EventPriorities::POST_VALIDATE]
    ];
  }

  public function confirmUser(ViewEvent $event)
  {
    $request = $event->getRequest();

    if ('api_user_confirmations_post_collection' !== $request->get('_route')) {
      // return everything back for route hit by accident.
      return;
    }

    /** @var \App\Entity\UserConfirmation $confirmationToken */
    $confirmationToken = $event->getControllerResult();

    $user = $this->userRepository->findOneBy(['confirmationToken' => $confirmationToken->confirmationToken]);

     // User was NOT found.
     if (!$user) {
         throw new NotFoundHttpException();
     }

     $user->setEnabled(true);
     $user->setConfirmationToken(null);
     $this->entityManager->flush();
     $event->setResponse(new JsonResponse(null, Response::HTTP_OK));
  }
}