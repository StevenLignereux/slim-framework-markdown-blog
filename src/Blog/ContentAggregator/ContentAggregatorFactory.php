<?php

declare(strict_types=1);

namespace MarkdownBlog\ContentAggregator;

use MarkdownBlog\Iterator\MarkdownFileFilterIterator;

class ContentAggregatorFactory
{
    public function __invoke(array $config): ContentAggregatorInterface
    {
        $iterator = new MarkdownFileFilterIterator(
            new \DirectoryIterator($config['path'])
        );
        return new ContentAggregatorFilesystem($iterator, $config['parser']);
    }
}
