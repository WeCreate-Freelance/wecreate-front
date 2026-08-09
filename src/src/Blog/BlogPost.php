<?php

namespace App\Blog;

/**
 * A single post, read from a Markdown file in content/blog/.
 *
 * There is no database behind this: the file is the record, and the slug is
 * the filename without its .md extension.
 */
final class BlogPost
{
    /**
     * @param string[] $tags
     */
    public function __construct(
        public readonly string $slug,
        public readonly string $title,
        public readonly string $description,
        public readonly \DateTimeImmutable $date,
        public readonly ?\DateTimeImmutable $updated,
        public readonly string $author,
        public readonly ?string $image,
        public readonly array $tags,
        public readonly string $html,
    ) {
    }

    /**
     * What search engines should treat as the article's freshness date: the
     * revision date when there has been one, otherwise publication.
     */
    public function lastModified(): \DateTimeImmutable
    {
        return $this->updated ?? $this->date;
    }

    public function url(): string
    {
        return '/blog/' . $this->slug;
    }

    /**
     * Rough reading time, at the ~200 words per minute usually quoted for
     * screen reading. Always at least a minute, so nothing reads "0 min".
     */
    public function readingMinutes(): int
    {
        $words = str_word_count(strip_tags($this->html));

        return max(1, (int) ceil($words / 200));
    }
}
