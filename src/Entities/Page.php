<?php

declare(strict_types=1);

namespace Matchory\Herodot\Entities;

use DateTimeImmutable;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Str;
use JetBrains\PhpStorm\Pure;
use JsonSerializable;
use Matchory\Herodot\Interfaces\StructureInterface;
use Spatie\YamlFrontMatter\Document;
use Spatie\YamlFrontMatter\Document as FrontMatter;
use Spatie\YamlFrontMatter\YamlFrontMatter;
use SplFileInfo;

use function array_merge;
use function file_get_contents;

class Page implements StructureInterface, JsonSerializable, Arrayable
{
    public const FRONT_MATTER_TITLE = 'title';

    protected ?string $title = null;

    protected ?Document $document = null;

    protected ?string $content = null;

    final public function __construct(protected SplFileInfo $fileInfo)
    {
    }

    public static function __set_state(array $array): static
    {
        return new static($array['fileInfo']);
    }

    #[Pure] public function file(): SplFileInfo
    {
        return $this->fileInfo;
    }

    public function extension(): string
    {
        return $this->fileInfo->getExtension();
    }

    public function path(): string
    {
        return $this->fileInfo->getPathname();
    }

    public function filename(): string
    {
        return $this->fileInfo->getFilename();
    }

    public function directory(): string
    {
        return $this->fileInfo->getPath();
    }

    public function fileSize(): int
    {
        return $this->fileInfo->getSize();
    }

    public function modified(): ?DateTimeImmutable
    {
        $timestamp = $this->fileInfo->getMTime();
        $modified = new DateTimeImmutable();

        return $modified->setTimestamp($timestamp) ?: null;
    }

    public function created(): ?DateTimeImmutable
    {
        $timestamp = $this->fileInfo->getCTime();
        $modified = new DateTimeImmutable();

        return $modified->setTimestamp($timestamp) ?: null;
    }

    public function title(): string
    {
        if ( ! $this->title) {
            $this->title = $this->inferTitle();
        }

        return $this->title;
    }

    public function slug(): string
    {
        return Str::slug($this->title());
    }

    public function raw(): string
    {
        if ( ! $this->content) {
            $this->content = $this->readFileContents();
        }

        return $this->content;
    }

    public function content(): string
    {
        return $this->document()->body();
    }

    public function document(): FrontMatter
    {
        if ( ! $this->document) {
            $this->document = $this->load();
        }

        return $this->document;
    }

    public function frontMatter(): array
    {
        return $this->document()->matter();
    }

    public function get(string $key): mixed
    {
        return $this->document()->matter($key);
    }

    public function __get(string|int $name): mixed
    {
        return $this->get((string)$name);
    }

    public function __set(string $name, mixed $value): void
    {
        // no-op
    }

    public function __isset(string $name): bool
    {
        return $this->get($name) !== null;
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    public function toArray(): array
    {
        return array_merge($this->frontMatter(), [
            'file' => $this->path(),
            'content' => $this->content(),
        ]);
    }

    protected function load(): FrontMatter
    {
        return YamlFrontMatter::parse($this->raw());
    }

    protected function readFileContents(): string
    {
        return file_get_contents($this->fileInfo->getPathname());
    }

    protected function inferTitle(): string
    {
        if ($title = $this->get(self::FRONT_MATTER_TITLE)) {
            return $title;
        }

        // TODO: Check for h1 in content

        return $this->inferTitleFromFileName();
    }

    protected function inferTitleFromFileName(): string
    {
        $extension = $this->fileInfo->getExtension();
        $basename = $this->fileInfo->getBasename('.' . $extension);

        return (string)Str
            ::of($basename)
            ->kebab()
            ->replace(['-', '_'], ' ')
            ->title()
            ->words();
    }
}
