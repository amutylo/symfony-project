<?php
/**
 * 
 */

namespace App\Controller;

use App\Entity\BlogPost;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Serializer;

/**
 * @Route("/blog")
 */
class BlogController extends AbstractController
{
  /**
   * @Route("/{page}", name="blog_list", defaults={"page":1}, requirements={"page"="\d+"})
   *
   */
  public function list(Request $request, $page = 1)
  {
    // Using dependency ingected Request
    $limit = $request->get('limit', 10);
    $repository = $this->getDoctrine()->getRepository(BlogPost::class);
    $items = $repository->findAll();

    // use json method from ControllerTrait of AbstractController
    return $this->json(
      [
        'page' => $page,
        'limit' => $limit,
        'data'=> array_map(function (BlogPost $item) {
          return $this->generateUrl('blog_by_slug', ['slug' => $item->getSlug()]);
        }, $items)
      ]   
    );
  }

  /**
   * Get blog post by an blog id.
   * We specify requirements that id must be a number, for controller to differentiate
   * it with the postBySlug method as parameters the same (we specify number for post index)
   * d+ means match a number appeared once or more
   * @Route("/post/{id}", name="blog_by_id", requirements={"id"="\d+"}, methods={"GET"})
   * @ParamConverter("post", class="App:BlogPost")
   *
   */
  public function post($post)
  {
    //It is the same as doing find($id) on repository
    return $this->json(
        $post
      );
  }

  /**
   * Blog by slug.
   * 
   * @Route("/post/{slug}", name="blog_by_slug", methods={"GET"})
   * The below annotation is not required when $post is typehinted with BlogPost
   * and route parameter name matches any field on the BlogPost entity
   * First param of mapping is the param from url "slug" the second is the field from entity "slug" (can be author too)
   * @ParamConverter("post", class="App:BlogPost", options={"mapping": {"slug": "slug"}})
   *
   */
  public function postBySlug(BlogPost $blogPost)
  {
    //It is the same as doing findOneBy(['slug' => $slug])
    return $this->json(
      $blogPost
    );
  }

  /**
   * @Route("/add", name="blog_add")
   */
  public function add(Request $request)
  {
    /** @var Serializer $serializer */
    $serializer = $this->get('serializer');

    $blogPost = $serializer->deserialize($request->getContent(), BlogPost::class, 'json');

    $em = $this->getDoctrine()->getManager();
    $em->persist($blogPost);
    $em->flush();

    return $this->json($blogPost);
  }

  /**
   * @Route("/delete/{id}", name="blog_delete", methods={"DELETE"})
   */
  public function delete(BlogPost $post) 
  {
    $em = $this->getDoctrine()->getManager();
    // remove method won't actually physically remove record, the flush does it.
    $em->remove($post);
    $em->flush();

    return new JsonResponse(null, Response::HTTP_NO_CONTENT);
  }
}