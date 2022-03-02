<?php

namespace MarkdownBlog\ContentAggregator;

use MarkdownBlog\Iterator\MarkdownFileFilterIterator;
use MarkdownBlog\Entity\BlogItem;
use Mni\FrontYAML\Document;
use Mni\FrontYAML\Parser;

class ContentAggregatorFilesystem implements ContentAgregatorInterface
{
    protected Parser $fileParser;
    protected MarkdownFileFilterIterator $fileIterator;
    private array $items = [];

    public function __construct(
        MarkdownFileFilterIterator $fileIterator,
        Parser $fileParser
    ) {
        $this->$fileParser = $fileParser;
        $this->fileIterator = $fileIterator;

        $this->buildItemslist();
    }

    public function getItems(): array
    {
        return $this->items;
    }
    
    protected function buildItemslist(): void
    {
        foreach ($this->fileIterator as $file) {
            $article = $this->buildItemFromFile($file);
            if (! is_null($article)) {
                $this->items[] = $article;
            }
        }
    }

    public function findItemBySlug(string $slug): ?BlogItem
    {
        foreach ($this->items as $article) {
            if ($article->getSlug() === $slug) {
                return $article;
            }
        }
        return null;
    }

    public function buildItemFromFile(\SplFileInfo $file): ?BlogItem
    {
        $fileContent = $file->getContents($file->getPathname());
        $document = $this->$fileParser->parse($fileContent, false);

        $item = new BlogItem();
        $item->populate($this->getItemData($document));

        return $item;
    }

    public function getItemData(Document $document): array
    {
        return [
            'publishDate' => $document->getYAML()['publishDate'] ?? '',
            'slug' => $document->getYAML()['slug'] ?? '',
            'synopsis' => $document->getYAML()['synopsis'] ?? '',
            'title' => $document->getYAML()['title'] ?? '',
            'image' => $document->getYAML()['image'] ?? '',
            'categories' => $document->getYAML()['categories'] ?? [],
            'tags' => $document->getYAML()['tags'] ?? [],
            'content' => $document->getContent(),
        ];
    }
}
