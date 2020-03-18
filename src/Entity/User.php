<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use App\Controller\ResetPasswordAction;

/**
 * Enable/disable API resources,
 * adding a group can enable/disable API resource to send some data in response
 * @ApiResource(
 *   itemOperations={
 *      "get"={
 *         "access_control"="is_granted('IS_AUTHENTICATED_FULLY')",
 *      "normalization_context"={
 *            "groups"={"get"}
 *        }
 *      },
 *      "put"={
 *        "access_control"="is_granted('IS_AUTHENTIICATED_FULLY') and object == user",
 *        "denormalization_context"={
 *            "groups"={"put"}
 *        },
 *        "normalization_context"={
 *            "groups"={"get"}
 *        },
 *      },
 *      "put-reset-password"={
 *        "access_control"="is_granted('IS_AUTHENTIICATED_FULLY') and object == user",
 *        "method"="PUT",
 *        "path"="/users/{id}/reset-password",
 *        "controller"=ResetPasswordAction::class,
 *        "denormalization_context"={
 *            "groups"={"put-reset-password"}
 *        },
 *        "validation_groups"={"put-reset-password"}
 *      }
 *    },
 *   collectionOperations={
 *      "post"={
          "denormalization_context"={
 *            "groups"={"post"}
 *        },
 *        "normalization_context"={
 *            "groups"={"get"}
 *        },
 *        "validation_groups"={"post"}
 *      }
 *    }
 * )
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity("username")
 * @UniqueEntity("email")
 */
class User implements UserInterface
{
    const ROLE_COMMENTATOR = 'ROLE_COMMENTATOR';
    const ROLE_WRITER = 'ROLE_WRITER';
    const ROLE_EDITOR = 'ROLE_EDITOR';
    const ROLE_ADMIN = 'ROLE_ADMIN';
    const ROLE_SUPERADMIN = 'ROLE_SUPERADMIN';

    const DEFAULT_ROLES = [self::ROLE_COMMENTATOR];

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"get","get-comment-with-author"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"get","post","get-comment-with-author","get-blog-post-with-author"})
     * @Assert\NotBlank()
     * @Assert\Length(min=6, max=255, groups={"post"})
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"post"})
     * @Assert\NotBlank(groups={"post"})
     */
    private $password;

    /**
    * @Assert\NotBlank(groups={"post"})
    * @Groups({"post"})
    * @Assert\Expression(
    *      "this.getPassword() === this.getRetypedPassword()",
    *       message="Password does not match",
    *       groups={"post"}
    *   )
    */
    private $retypedPassword;

  /**
   * @Groups({"put-reset-password"})
   * @Assert\NotBlank(groups={"put-reset-password"})
   */
    private $newPassword;

  /**
   * @Groups({"put-reset-password"})
   * @Assert\NotBlank(groups={"put-reset-password"})
   * @Assert\Expression(
   *      "this.getNewPassword() === this.getNewRetypedPassword()",
   *       message="New password does not match",
   *       groups={"put-reset-password"}
   *   )
   */
    private $newRetypedPassword;

  /**
   * @Groups({"put-reset-password"})
   * @Assert\NotBlank(groups={"put-reset-password"})
   * @UserPassword(groups={"put-reset-password"})
   */
    private $oldPassword;
    
    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"get","post","put","get-comment-with-author", "get-blog-post-with-author"})
     * @Assert\NotBlank(groups={"post", "put"})
     * @Assert\Length(min=5, max=255, groups={"post", "put"})
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"post", "put", "get-admin", "get-owner"})
     * @Assert\NotBlank(groups={"post"})
     * @Assert\Email(groups={"post"})
     * @Assert\Length(min=5, max=255, groups={"post", "put"})
     */
    private $email;

    /**
     * Make a relation to BlogPost Entity.
     * @ORM\OneToMany(targetEntity="App\Entity\BlogPost", mappedBy="author")
     * @Groups({"get"})
     */
    private $posts;

    /**
     * Make a relation to Comment Entity
     * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="author")
     * @Groups({"get"})
     */
    private $comments;

    /**
     * @ORM\Column(type="simple_array", length=200)
     * @Groups({"get-admin", "get-owner"})
     */
    private $roles;

    /**
    * @ORM\Column(type="integer", nullable=true)
    */
    private $passwordChangeDate;

  /**
   * @ORM\Column(type="boolean")
   */
    private $enabled;

  /**
   * @ORM\Column(type="string", length=40, nullable=true)
   */
    private $confirmationToken;

    public function __construct()
    {
      $this->posts = new ArrayCollection();
      $this->comments = new ArrayCollection();
      $this->roles = self::DEFAULT_ROLES;
      $this->enabled = false;
      $this->confirmationToken = null;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getPosts(): Collection
    {
        return $this->posts;
    }

    /**
     * @return Collection
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

  /**
   * Returns the roles granted to the user.
   * Alternatively, the roles might be stored on a `roles` property,
   * and populated in any number of different ways when the user object
   * is created.
   * @return array
   */
  public function getRoles(): array
  {
    return $this->roles;
  }

  /**
   * @param array $roles
   *
   * @return $this
   */
  public function setRoles(array $roles): self
  {
    $this->roles = $roles;
    return $this;
  }

  /**
   * Returns the salt that was originally used to encode the password.
   *
   * This can return null if the password was not encoded using a salt.
   *
   * @return string|null The salt
   */
  public function getSalt()
  {
     return null;
  }

  /**
   * Removes sensitive data from the user.
   *
   * This is important if, at any given point, sensitive information like
   * the plain-text password is stored on this object.
   */
  public function eraseCredentials()
  {

  }
  
  public function getRetypedPassword()
  {
    return $this->retypedPassword;
  }

  public function setRetypedPassword($retypedPassword): self
  {
    $this->retypedPassword = $retypedPassword;
    return $this;
  }

  public function getNewPassword(): ?string
  {
    return $this->newPassword;
  }

  public function setNewPassword($newPassword): void
  {
    $this->newPassword = $newPassword;
  }

  public function getNewRetypedPassword(): ?string
  {
    return $this->newRetypedPassword;
  }

  public function setNewRetypedPassword($newRetypedPassword): void
  {
    $this->newRetypedPassword = $newRetypedPassword;
  }

  public function getOldPassword(): ?string
  {
    return $this->oldPassword;
  }

  public function setOldPassword($oldPassword): void
  {
    $this->oldPassword = $oldPassword;
  }

  /**
   * @return mixed
   */
  public function getPasswordChangeDate()
  {
    return $this->passwordChangeDate;
  }

  /**
   * @param $passwordChangeDate
   */
  public function setPasswordChangeDate($passwordChangeDate): void
  {
    $this->passwordChangeDate = $passwordChangeDate;
  }

  public function getEnabled(): bool
  {
    return $this->enabled;
  }

  public function setEnabled(bool $enabled): void
  {
    $this->enabled = $enabled;
  }
  
  public function getConfirmationToken()
  {
    return $this->confirmationToken;
  }

  public function setConfirmationToken($confirmationToken): void
  {
    $this->confirmationToken = $confirmationToken;
  }
}
