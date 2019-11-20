<?php
/**
 * 
 */

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/blog")
 */
class BlogController extends AbstractController
{

private const POSTS = [
  [
    'id' => 1,
    'slug' => 'hello-world',
    'title' => 'Hello World'
  ],
  [
    'id' => 2,
    'slug' => 'another-post',
    'title' => 'This is another post.'
  ],
  [
    'id' => 3,
    'slug' => 'last-example',
    'title' => 'This is the last example.'
  ],
];

  /**
   * @Route("/{page}", name="blog_list", defaults={"page":1})
   *
   */
  public function list(Request $request, $page = 1)
  {
    // Using dependency ingected Request
    $limit = $request->get('limit', 10);
    $data = array_map(function ($item) {
      // for id use
      return $this->generateUrl('blog_by_id', ['id' => $item['id']]);
      // for slug change to
      // return $this->generateUrl('blog_by_slug', ['slug' => $item['slug']]);
    },self::POSTS);
    // use json method from ControllerTrait of AbstractController
    return $this->json(
      [
        'page' => $page,
        'limit' => $limit,
        'data'=> $data
      ]     
    );
  }

  /**
   * Get blog post by an blog id.
   * We specify requirements that id must be a number, for controller to differentiate
   * it with the postBySlug method as parameters the same (we specify number for post index)
   * @Route("/post/{id}", name="blog_by_id", requirements={"id"="\d+"})
   * d+ means match a number appeared once or more
   *
   */
  public function post($id)
  {
    $post_id = array_search($id, array_column(self::POSTS, 'id'));
    return $this->json(
      self::POSTS[$post_id]
      );
  }

  /**
   * Blog by slug.
   * 
   * @Route("/post/{slug}", name="blog_by_slug")
   *
   */
  public function postBySlug($slug)
  {
    $post_id = array_search($slug, array_column(self::POSTS, 'slug'));
    return $this->json(
      self::POSTS[$post_id]
    );
  }
}