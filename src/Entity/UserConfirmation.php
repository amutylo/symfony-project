<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * @ApiResource(
 *   collectionOperations={
 *      "post"={
 *         "path"="user/confirm"
 *      }
 *   },
 *   itemOperations={}
 * )
 * itemOperations is empty object in order to disable confirmation operation over individual item.
 */
class UserConfirmation
{

  /**
   * @Assert\NotBlank()
   * @Assert\Length(min=30, max=30)
   */
  public $confirmationToken;
}