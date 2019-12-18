<?php

namespace App\Security;

use App\Entity\User;
use Symfony\Component\Security\Core\Exception\AccountStatusException;
use Symfony\Component\Security\Core\Exception\DisabledException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserEnabledChecker implements UserCheckerInterface
{

  /**
   * Checks User account before authentication.
   *
   * @param UserInterface $user
   * @throws AccountStatusException
   * @return void
   */
  public function checkPreAuth(UserInterface $user)
  {
      if (!$user instanceof User) {
        /** Not an instance of User */
        return;
      }

      if (!$user->getEnabled()) {
        /** Disabled User */
        throw new DisabledException();
      }

  }

  /**
   * Checks User account after authorisation.
   *
   * @param UserInterface $user
   * @throws AccountStatusException
   * @return void
   */
  public function checkPostAuth(UserInterface $user)
  {
    
  }

}