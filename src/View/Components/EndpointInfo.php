<?php

declare(strict_types=1);

namespace Matchory\Herodot\View\Components;

use Illuminate\Contracts\View\View as ViewContract;
use Illuminate\Support\Facades\View;
use League\CommonMark\MarkdownConverterInterface;
use Matchory\Herodot\Entities;
use RuntimeException;

class EndpointInfo extends AbstractHerodotComponent
{
    protected ?string $parsedContent = null;

    public function __construct(
        public Entities\Endpoint $endpoint,
        protected MarkdownConverterInterface $markdownConverter
    ) {
    }

    public function render(): ViewContract
    {
        return View::make('herodot::components.endpoint-info');
    }

    /**
     * @return string
     * @throws RuntimeException
     */
    public function description(): string
    {
        if ( ! $this->parsedContent) {
            $content = $this->endpoint->getDescription();
            $this->parsedContent = $content
                ? $this->markdownConverter->convertToHtml($content)
                : '';
        }

        return $this->parsedContent;
    }

    public function hasParameters(): bool
    {
        return (
                   count($this->endpoint->getBodyParams()) +
                   count($this->endpoint->getUrlParams()) +
                   count($this->endpoint->getQueryParams())
               ) > 0;
    }
}
