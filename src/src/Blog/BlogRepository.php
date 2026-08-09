<?php

namespace App\Blog;

use League\CommonMark\Extension\Autolink\AutolinkExtension;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\FrontMatter\FrontMatterExtension;
use League\CommonMark\Extension\FrontMatter\Output\RenderedContentWithFrontMatter;
use League\CommonMark\Extension\Strikethrough\StrikethroughExtension;
use League\CommonMark\Extension\Table\TableExtension;
use League\CommonMark\Environment\Environment;
use League\CommonMark\MarkdownConverter;

/**
 * Reads posts from Markdown files on disk.
 *
 * content/blog/ lives outside public/, so the raw .md is never served; the
 * only way to a post is through BlogController.
 */
final class BlogRepository
{
    /** @var BlogPost[]|null slug => post, newest first, built once per request */
    private ?array $posts = null;

    private readonly MarkdownConverter $converter;

    public function __construct(private readonly string $blogContentDir)
    {
        $environment = new Environment([
            // Posts are committed to this repo and reviewed like any other
            // change, so raw HTML in a post is trusted and passed through.
            'html_input' => 'allow',
            'allow_unsafe_links' => false,
        ]);
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new FrontMatterExtension());
        $environment->addExtension(new TableExtension());
        $environment->addExtension(new StrikethroughExtension());
        $environment->addExtension(new AutolinkExtension());

        $this->converter = new MarkdownConverter($environment);
    }

    /**
     * @return BlogPost[] newest first
     */
    public function findAll(): array
    {
        return array_values($this->load());
    }

    public function find(string $slug): ?BlogPost
    {
        return $this->load()[$slug] ?? null;
    }

    /**
     * @return array<string, BlogPost>
     */
    private function load(): array
    {
        if (null !== $this->posts) {
            return $this->posts;
        }

        $posts = [];

        foreach (glob($this->blogContentDir . '/*.md') ?: [] as $file) {
            $post = $this->parse($file);

            if (null !== $post) {
                $posts[$post->slug] = $post;
            }
        }

        uasort($posts, static fn (BlogPost $a, BlogPost $b) => $b->date <=> $a->date);

        return $this->posts = $posts;
    }

    private function parse(string $file): ?BlogPost
    {
        $slug = basename($file, '.md');
        $rendered = $this->converter->convert((string) file_get_contents($file));

        $frontMatter = $rendered instanceof RenderedContentWithFrontMatter
            ? $rendered->getFrontMatter()
            : [];

        // A post with no title has nothing usable to show in a listing or a
        // <title>, so skip it rather than render a blank card.
        if (!\is_array($frontMatter) || empty($frontMatter['title'])) {
            return null;
        }

        $date = $this->toDate($frontMatter['date'] ?? 'now');

        // Lets a post be committed ahead of time and appear on its own date.
        if ($date > new \DateTimeImmutable('today 23:59:59')) {
            return null;
        }

        return new BlogPost(
            slug: $slug,
            title: (string) $frontMatter['title'],
            description: (string) ($frontMatter['description'] ?? ''),
            date: $date,
            updated: isset($frontMatter['updated']) ? $this->toDate($frontMatter['updated']) : null,
            author: (string) ($frontMatter['author'] ?? 'WeCreate'),
            image: isset($frontMatter['image']) ? (string) $frontMatter['image'] : null,
            tags: array_map('strval', (array) ($frontMatter['tags'] ?? [])),
            html: $rendered->getContent(),
        );
    }

    /**
     * Symfony's YAML parser hands back a plain string for an unquoted date, but
     * accept a real date object too so a quoted or unquoted value both work.
     */
    private function toDate(mixed $value): \DateTimeImmutable
    {
        return $value instanceof \DateTimeInterface
            ? \DateTimeImmutable::createFromInterface($value)
            : new \DateTimeImmutable((string) $value);
    }
}
