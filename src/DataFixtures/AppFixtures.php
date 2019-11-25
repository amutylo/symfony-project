<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\BlogPost;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        // $product = new Product();
        // $manager->persist($product);
        $blogPost = new BlogPost();
        $blogPost->setTitle("A first title");
        $blogPost->setPublished(new \DateTime('2019-07-01 12:00:00'));
        $blogPost->setContent('Post text!');
        $blogPost->setAuthor('Andrii Mutylo');
        $blogPost->setSlug('a-first-title');

        $manager->persist($blogPost);
        $blogPost = new BlogPost();
        $blogPost->setTitle("the second title");
        $blogPost->setPublished(new \DateTime('2019-07-01 12:10:00'));
        $blogPost->setContent('Post second text!');
        $blogPost->setAuthor('Andrii Mutylo');
        $blogPost->setSlug('the-second-title');
        $manager->persist($blogPost);

        $manager->flush();
    }
}
