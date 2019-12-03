<?php

namespace App\Controller;

use ApiPlatform\Core\Validator\ValidatorInterface;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ResetPasswordAction {

  /**
   * @var ValidatorInterface
   */
  private $validator;

  /**
   * @var UserPasswordEncoderInterface
   */
  private $userPasswordEncoder;

  /**
   * @var \Doctrine\ORM\EntityManagerInterface
   */
  private $entityManager;

  /**
   * @var JWTTokenManagerInterface
   */
  private $tokenManager;

  public function __construct(
    ValidatorInterface $validator,
    UserPasswordEncoderInterface $userPasswordEncoder,
    EntityManagerInterface $entityManager,
    JWTTokenManagerInterface $tokenManager
  )
  {
    $this->validator = $validator;
    $this->userPasswordEncoder = $userPasswordEncoder;
    $this->entityManager = $entityManager;
    $this->tokenManager = $tokenManager;
  }

  public function __invoke(User $data)
  {
    // $reset = new ResetPasswordAction();
    // $reset();
    // validate data
    $this->validator->validate($data);

    //
    $data->setPassword(
      $this->userPasswordEncoder->encodePassword(
        $data, $data->getNewPassword()
      )
    );

    // After changing password the old token still valid.
    $data->setPasswordChangeDate(time());

    // We have to persist store changes in DB
    // Otherwise platform will use entity that already being loaded.
    // We call flush but didn't call persist.
    // Persist called only for a newly created entities.
    // Platform will automatically find changes.
    $this->entityManager->flush();

    //Generate token programmatically for the user request.
    $token = $this->tokenManager->create($data);

    return new JsonResponse(['token' => $token]);
    
    // Validator is only called after we return the data from this action!
    // Only hear it checks for user current password, but we've just modified it!
    // Entity is persisted automatically, only if validation pass
  }
}