<?php

namespace App\Controller;

use App\Blog\BlogRepository;
use Rami\SeoBundle\Metas\MetaTagsManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BlogController extends AbstractMetaController
{
    public function __construct(
        MetaTagsManagerInterface $metaTags,
        private readonly BlogRepository $posts,
    ) {
        parent::__construct($metaTags);
    }

    #[Route('/blog', name: 'blog')]
    public function index(): Response
    {
        return $this->renderPage(
            'front/blog.html.twig',
            'Blog - WeCreate',
            'Notes from the WeCreate team on the software, websites, and mobile apps we build, and on the tools we reach for along the way.',
            '/blog',
            ['posts' => $this->posts->findAll()]
        );
    }

    #[Route('/blog/{slug}', name: 'blog_post', requirements: ['slug' => '[a-z0-9-]+'])]
    public function post(string $slug): Response
    {
        $post = $this->posts->find($slug);

        if (null === $post) {
            throw $this->createNotFoundException(sprintf('No blog post named "%s".', $slug));
        }

        $parameters = [
            'post' => $post,
            'recent' => array_slice(
                array_filter($this->posts->findAll(), static fn ($p) => $p->slug !== $slug),
                0,
                3
            ),
        ];

        // Left unset when the post has no cover of its own, so base.html.twig
        // falls back to the site-wide social image.
        if (null !== $post->image) {
            $parameters['og_image'] = self::BASE_URL . '/' . ltrim($post->image, '/');
        }

        return $this->renderPage(
            'front/blog-post.html.twig',
            $post->title . ' - WeCreate',
            $post->description,
            $post->url(),
            $parameters
        );
    }
}
