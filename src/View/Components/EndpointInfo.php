<?php

declare(strict_types=1);

namespace Matchory\Herodot\View\Components;

use Illuminate\Contracts\View\View as ViewContract;
use Illuminate\Support\Facades\View;
use Illuminate\View\Component;
use Illuminate\View\ComponentAttributeBag;
use League\CommonMark\MarkdownConverterInterface;
use Matchory\Herodot\Entities;
use RuntimeException;

class EndpointInfo extends Component
{
    /**
     * @var string|null
     */
    public $componentName = null;

    /**
     * @var ComponentAttributeBag|null
     */
    public $attributes = null;

    public Entities\Endpoint $endpoint;

    protected MarkdownConverterInterface $markdownConverter;

    public function __construct(
        MarkdownConverterInterface $markdownConverter,
        Entities\Endpoint $endpoint
    ) {
        $this->endpoint = $endpoint;
        $this->markdownConverter = $markdownConverter;
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
        $description = $this->endpoint->getDescription();

        if ( ! $description) {
            return '';
        }

        return $this->markdownConverter->convertToHtml($description);
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
