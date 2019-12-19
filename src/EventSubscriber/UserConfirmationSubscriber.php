<?php

namespace App\EventSubscriber;

use App\Repository\UserRepository;
use App\Security\UserConfirmationService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use ApiPlatform\Core\EventListener\EventPriorities;

class UserConfirmationSubscriber implements EventSubscriberInterface
{

  /**
   * @var UserConfirmationService
   */
  private $confirmationService;

  public function __construct(UserConfirmationService $confirmationService)
  {
    $this->confirmationService = $confirmationService;
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
    $this->confirmationService->confirmUser($confirmationToken->confirmationToken);
     
    $event->setResponse(new JsonResponse(null, Response::HTTP_OK));
  }
}