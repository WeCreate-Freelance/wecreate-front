<?php

namespace App\Controller;

use Rami\SeoBundle\Metas\MetaTagsManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractMetaController extends AbstractController
{
    public const BASE_URL = 'https://wecreate-services.com';

    public function __construct(protected readonly MetaTagsManagerInterface $metaTags)
    {
        $this->metaTags
            ->setDescription(
                'WeCreate is a platform to provide services for companies and individuals to create software, websites, and applications.'
            )
            ->setAuthor('Mark Joshua Tolentino Fajardo')
            ->setCharacterEncoding('UTF-8')
            ->setSubject('WeCreate - SaaS, Software, Website, and Application Development')
            ->setCopyright('WeCreate 2025')
            ->setRobots(['index', 'follow'])
            ->setKeywords($this->getMetaKeywords())
            ->setXUACompatible()
            ->setViewPort('width=device-width, initial-scale=1.0, maximum-scale=5.0, minimum-scale=1.0, user-scalable=yes');
    }

    /**
     * Renders a page with its own title, description and canonical URL, and hands the
     * same values to the template so the Open Graph / Twitter tags stay in sync.
     *
     * @param array<string, mixed> $parameters
     */
    protected function renderPage(
        string $template,
        string $title,
        string $description,
        string $path,
        array $parameters = []
    ): Response {
        $canonical = self::BASE_URL . $path;

        $this->metaTags
            ->setTitle($title)
            ->setDescription($description)
            ->setCanonical($canonical);

        return $this->render($template, array_merge([
            'og_title' => $title,
            'og_description' => $description,
            'og_url' => $canonical,
        ], $parameters));
    }

    protected function getMetaKeywords(): array
    {
        return [
            'wecreate-services',
            'wecreate services',
            'we create services',
            'saas',
            'software',
            'website',
            'application',
            'development',
            'wecreate',
            'web development',
            'software development',
            'application development',
            'saas development',
            'web design',
            'software design',
            'application design',
            'saas design',
            'web application',
            'software application',
            'application software',
            'saas software',
            'web services',
            'software services',
            'application services',
            'saas services',
            'web solutions',
            'software solutions',
            'application solutions',
            'saas solutions',
            'web development company',
            'software development company',
            'application development company',
            'saas development company',
            'web design company',
            'software design company',
            'application design company',
            'saas design company',
            'web application company',
            'software application company',
            'application software company',
            'saas software company',
            'web services company',
            'software services company',
            'application services company',
            'saas services company',
            'web solutions company',
            'software solutions company',
            'application solutions company',
            'saas solutions company',
        ];
    }
}