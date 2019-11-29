<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

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
 *      }
 *    }
 * )
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity("username")
 * @UniqueEntity("email")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"get","get-comment-with-author"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"get","post","get-comment-with-author"})
     * @Assert\NotBlank()
     * @Assert\Length(min=6, max=255)
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"post"})
     * @Assert\NotBlank()
     */
    private $password;

    /**
    * @Assert\NotBlank()
    * @Groups({"post"})
    * @Assert\Expression(
    *      "this.getPassword() === this.getRetypedPassword()",
    *       message="Password does not match"
    *   )
    */
    private $retypedPassword;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"get","post","put","get-comment-with-author"})
     * @Assert\NotBlank()
     * @Assert\Length(min=5, max=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"post","put"})
     * @Assert\NotBlank()
     * @Assert\Email()
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

    public function __construct()
    {
      $this->posts = new ArrayCollection();
      $this->comments = new ArrayCollection();
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
   *
   *     public function getRoles()
   *     {
   *         return ['ROLE_USER'];
   *     }
   *
   * Alternatively, the roles might be stored on a ``roles`` property,
   * and populated in any number of different ways when the user object
   * is created.
   *
   * @return (Role|string)[] The user roles
   */
  public function getRoles()
  {
    return ['ROLE_USER'];
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


}
