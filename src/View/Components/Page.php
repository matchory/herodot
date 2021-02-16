<?php

declare(strict_types=1);

namespace Matchory\Herodot\View\Components;

use Illuminate\Contracts\View\View as ViewContract;
use Illuminate\Support\Facades\View;
use League\CommonMark\MarkdownConverterInterface;
use Matchory\Herodot\Entities\Page as PageEntity;
use RuntimeException;

class Page extends AbstractHerodotComponent
{
    protected ?string $parsedContent = null;

    public function __construct(
        protected MarkdownConverterInterface $markdownConverter,
        public PageEntity $page
    ) {
    }

    public function render(): ViewContract
    {
        return View::make('herodot::components.page');
    }

    /**
     * @return string
     * @throws RuntimeException
     */
    public function content(): string
    {
        if ( ! $this->parsedContent) {
            $this->parsedContent = $this->markdownConverter->convertToHtml(
                $this->page->content()
            );
        }

        return $this->parsedContent;
    }

    public function slug(): string
    {
        return $this->page->slug();
    }

    public function title(): string
    {
        return $this->page->title();
    }
}
