<?php

namespace App\Controller;

use App\Blog\BlogRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * The sitemap used to be a static file, which meant every new blog post had to
 * be remembered and hand-added. Generating it keeps it honest.
 */
class SitemapController extends AbstractController
{
    /**
     * Bump lastmod when a page's content actually changes; Google discounts a
     * lastmod it catches lying. Blog URLs are appended automatically.
     *
     * @var array<string, array{lastmod: string, changefreq: string, priority: string}>
     */
    private const STATIC_PAGES = [
        '/' => ['lastmod' => '2026-08-09', 'changefreq' => 'weekly', 'priority' => '1.0'],
        '/about-us' => ['lastmod' => '2026-04-29', 'changefreq' => 'monthly', 'priority' => '0.8'],
        '/contact' => ['lastmod' => '2026-04-29', 'changefreq' => 'monthly', 'priority' => '0.8'],
        '/privacy' => ['lastmod' => '2026-04-29', 'changefreq' => 'yearly', 'priority' => '0.5'],
        '/terms' => ['lastmod' => '2026-04-29', 'changefreq' => 'yearly', 'priority' => '0.5'],
    ];

    #[Route('/sitemap.xml', name: 'sitemap', defaults: ['_format' => 'xml'])]
    public function index(BlogRepository $posts): Response
    {
        $all = $posts->findAll();
        $urls = [];

        foreach (self::STATIC_PAGES as $path => $meta) {
            $urls[] = ['loc' => AbstractMetaController::BASE_URL . $path] + $meta;
        }

        // The listing is only worth crawling once there is something on it, and
        // it changes when a post is published, not on a fixed schedule.
        if ([] !== $all) {
            $urls[] = [
                'loc' => AbstractMetaController::BASE_URL . '/blog',
                'lastmod' => $all[0]->date->format('Y-m-d'),
                'changefreq' => 'weekly',
                'priority' => '0.8',
            ];
        }

        foreach ($all as $post) {
            $urls[] = [
                'loc' => AbstractMetaController::BASE_URL . $post->url(),
                'lastmod' => $post->date->format('Y-m-d'),
                'changefreq' => 'monthly',
                'priority' => '0.7',
            ];
        }

        $response = $this->render('sitemap.xml.twig', ['urls' => $urls]);
        $response->headers->set('Content-Type', 'application/xml; charset=UTF-8');

        return $response;
    }
}
