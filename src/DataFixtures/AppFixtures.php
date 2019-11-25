<?php

namespace App\DataFixtures;

use App\Entity\BlogPost;
use App\Entity\Comment;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
      $this->loadUsers($manager);
      $this->loadBlogPosts($manager);
    }

    public function loadBlogPosts(ObjectManager $manager)
    {
      $user = $this->getReference('user_admin');

      $blogPost = new BlogPost();
      $blogPost->setTitle("A first title");
      $blogPost->setPublished(new \DateTime('2019-07-01 12:00:00'));
      $blogPost->setContent('Post text!');
      $blogPost->setAuthor($user);
      $blogPost->setSlug('a-first-title');

      $manager->persist($blogPost);
      $blogPost = new BlogPost();
      $blogPost->setTitle("the second title");
      $blogPost->setPublished(new \DateTime('2019-07-01 12:10:00'));
      $blogPost->setContent('Post second text!');
      $blogPost->setAuthor($user);
      $blogPost->setSlug('the-second-title');
      $manager->persist($blogPost);

      $manager->flush();
    }

    public function loadComments()
    {
      
    }

    public function loadUsers(ObjectManager $manager)
    {
       $user = new User();
       $user->setUserName('admin');
       $user->setEmail('amutylo@gmail.com');
       $user->setName('Andrii Mutylo');
       $user->setPassword('secret123#');
       $this->addReference('user_admin', $user);

       $manager->persist($user);
       $manager->flush();
    }
}
