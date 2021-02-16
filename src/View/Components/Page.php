<?php

declare(strict_types=1);

namespace Matchory\Herodot\View\Components;

use Illuminate\Contracts\View\View as ViewContract;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use Illuminate\View\Component;
use Illuminate\View\ComponentAttributeBag;
use League\CommonMark\MarkdownConverterInterface;
use RuntimeException;

class Page extends Component
{
    /**
     * @var string|null
     */
    public $componentName = null;

    /**
     * @var ComponentAttributeBag|null
     */
    public $attributes = null;

    protected string $title;

    protected string $content;

    protected MarkdownConverterInterface $markdownConverter;

    public function __construct(
        MarkdownConverterInterface $markdownConverter,
        string $title,
        string $content
    ) {
        $this->title = $title;
        $this->content = $content;
        $this->markdownConverter = $markdownConverter;
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
        return $this->markdownConverter->convertToHtml(
            $this
                ->content
        );
    }

    public function slug(): string
    {
        return Str::slug($this->title);
    }

    public function title(): string
    {
        return $this->title;
    }
}
