<?php

declare(strict_types=1);

namespace Matchory\Herodot\Extracting;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use InvalidArgumentException;
use JetBrains\PhpStorm\Pure;
use Matchory\Herodot\Contracts\Endpoint;
use Matchory\Herodot\Contracts\ExtractionStrategy;
use Matchory\Herodot\Interfaces\ResolvedRouteInterface;
use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\DocBlock\Tag;
use phpDocumentor\Reflection\DocBlock\Tags\Deprecated as DeprecatedTag;
use phpDocumentor\Reflection\DocBlock\Tags\Generic;
use phpDocumentor\Reflection\DocBlock\Tags\Return_ as ReturnTag;
use phpDocumentor\Reflection\DocBlockFactory;
use phpDocumentor\Reflection\Types\ContextFactory;
use ReflectionClass;
use ReflectionException;
use UnexpectedValueException;

use function array_shift;
use function class_exists;
use function is_a;
use function str_starts_with;

use const PREG_SPLIT_NO_EMPTY;

class DocBlockStrategy implements ExtractionStrategy
{
    public const TAG_AUTHENTICATED = 'authenticated';

    public const TAG_BODY_PARAM = 'bodyParam';

    public const TAG_DEPRECATED = 'deprecated';

    public const TAG_DESCRIPTION = 'description';

    public const TAG_GROUP = 'group';

    public const TAG_HEADER = 'header';

    public const TAG_HIDDEN = 'hidden';

    public const TAG_HIDE_FROM_API_DOCUMENTATION = 'hideFromAPIDocumentation';

    public const TAG_QUERY_PARAM = 'queryParam';

    public const TAG_RETURN = 'return';

    public const TAG_RETURNS = 'returns';

    public const TAG_TITLE = 'title';

    public const TAG_UNAUTHENTICATED = 'unauthenticated';

    public const TAG_URL_PARAM = 'urlParam';

    protected DocBlockFactory $docBlockFactory;

    public function __construct(DocBlockFactory $docBlockFactory)
    {
        $this->docBlockFactory = $docBlockFactory;
    }

    #[Pure] public function getDependencies(): ?array
    {
        return null;
    }

    #[Pure] public function getPriority(): int
    {
        return 30;
    }

    /**
     * @inheritdoc
     * @throws InvalidArgumentException
     * @throws UnexpectedValueException
     * @throws ReflectionException
     */
    public function handle(
        ResolvedRouteInterface $route,
        Endpoint $endpoint,
    ): Endpoint {
        $handlerReflector = $route->getHandlerReflector();
        $docComment = $handlerReflector->getDocComment();

        // If the handler doesn't have a docblock, there's nothing we can do.
        if ( ! $docComment) {
            return $endpoint;
        }

        // Creating a context is mandatory for correct types, see
        // https://github.com/phpDocumentor/ReflectionDocBlock/issues/158
        $contextFactory = new ContextFactory();
        $context = $contextFactory->createFromReflector(
            $handlerReflector
        );

        // Here, we create an instance of the docBlock parser. The library is
        // quite versatile, so make sure to check out its documentation if you
        // have trouble here:
        // https://github.com/phpDocumentor/ReflectionDocBlock
        $docBlock = $this->docBlockFactory->create(
            $docComment,
            $context
        );

        $this->applyTitle($docBlock, $endpoint);
        $this->applyDescription($docBlock, $endpoint);

        $tags = new Collection($docBlock->getTags());

        // A while loop is quite helpful here, as we shift tags off the comment,
        // one by one, until there are no more left. That way, we can be sure to
        // have processed all tags and still catch any generic tags to be added
        // as meta data.
        while ($tags->count() > 0) {
            /**
             * One day, big T will hopefully acknowledge the benefit of template
             * annotations on the collection class...
             *
             * @var Tag $tag
             */
            $tag = $tags->shift();

            switch ($tag->getName()) {
                case self::TAG_DEPRECATED:
                    // This check is mostly useless, but guarantees type safety
                    if ( ! ($tag instanceof DeprecatedTag)) {
                        break;
                    }

                    $reason = $tag->getDescription();
                    $sinceVersion = $tag->getVersion();

                    $endpoint->setDeprecated(
                        $reason ? $reason->render() : null,
                        $sinceVersion
                    );

                    break;

                case self::TAG_TITLE:
                    $title = (string)$tag;

                    if ($title) {
                        $endpoint->setTitle($title);
                    }
                    break;

                case self::TAG_DESCRIPTION:
                    $description = (string)$tag;

                    if ($description) {
                        $endpoint->setDescription($description);
                    }
                    break;

                case self::TAG_GROUP:
                    $group = (string)$tag;

                    if ($group) {
                        $endpoint->setGroup($group);
                    }
                    break;

                case self::TAG_AUTHENTICATED:
                    $endpoint->setRequiresAuthentication(
                        true
                    );
                    break;

                case self::TAG_UNAUTHENTICATED:
                    $endpoint->setRequiresAuthentication(
                        false
                    );
                    break;

                case self::TAG_HEADER:
                    $parts = preg_split(
                        '/\s+/',
                        (string)$tag,
                        -1,
                        PREG_SPLIT_NO_EMPTY
                    );
                    $name = array_shift($parts);
                    $example = array_shift($parts);

                    if ($name) {
                        $endpoint->addHeader($name, $example);
                    }
                    break;

                case self::TAG_RETURN:
                case self::TAG_RETURNS:
                    if ( ! $tag instanceof ReturnTag) {
                        break;
                    }

                    /** @var class-string|null $type */
                    $type = (string)$tag->getType();

                    if ( ! $type) {
                        break;
                    }

                    if ( ! Str::startsWith($type, '\\')) {
                        break;
                    }

                    if ( ! class_exists($type)) {
                        break;
                    }

                    if (is_a(
                        $type,
                        JsonResponse::class,
                        true
                    )) {
                        $endpoint->addResponse(
                            [],
                            'application/json'
                        );

                        break;
                    }

                    if (is_a(
                        $type,
                        RedirectResponse::class,
                        true
                    )) {
                        // TODO: We need more info to create a proper response
                        //       here. The status might very well be different!
                        $endpoint->addResponse('', status: 301);
                    }

                    if (is_a(
                        $type,
                        JsonResource::class,
                        true
                    )) {
                        $resourceReflection = new ReflectionClass(
                            $type
                        );
                        # $shape = $this->resolveShape($resourceReflection);

                        $endpoint->addResponse(
                            null,#$shape,
                            'application/json'
                        );
                    }

                    // TODO: We can't handle the type, so more reflection would
                    //       be in order. Maybe a kind of deferral indication?
                    break;

                case self::TAG_HIDDEN:
                case self::TAG_HIDE_FROM_API_DOCUMENTATION:
                    $endpoint->setHidden(true, (string)$tag);
                    break;

                // Add all unknown tags to the endpoint meta data. This allows
                // to instantly accommodate users familiar with doc blocks to
                // add their custom metadata right on every method.
                default:
                    if ( ! $tag instanceof Generic) {
                        break;
                    }

                    $this->applyGenericMeta($tag, $endpoint);
            }
        }

        return $endpoint;
    }

    /**
     * @param Generic  $tag
     * @param Endpoint $endpoint
     */
    protected function applyGenericMeta(
        Generic $tag,
        Endpoint $endpoint
    ): void {
        $key = $tag->getName();
        $value = (string)$tag ?: null;

        // Psalm tags refer to the code itself, not the endpoint
        if (str_starts_with($key, 'psalm-')) {
            return;
        }

        // PHPStan tags refer to the code itself, not the endpoint
        if (str_starts_with($key, 'phpstan-')) {
            return;
        }

        // NoInspection tags suppress errors in JetBrains IDEs
        if ($key === 'noinspection') {
            return;
        }

        $endpoint->addMeta($key, $value);
    }

    /**
     * Extracts and parses the title from a documentation block and applies it
     * to an endpoint
     *
     * @param DocBlock $docBlock
     * @param Endpoint $endpoint
     */
    protected function applyTitle(DocBlock $docBlock, Endpoint $endpoint): void
    {
        $summary = $docBlock->getSummary();

        if ( ! $summary) {
            return;
        }

        $endpoint->setTitle($summary);
    }

    /**
     * Extracts and parses the description from a documentation block and
     * applies it to an endpoint
     *
     * @param DocBlock $docBlock
     * @param Endpoint $endpoint
     */
    protected function applyDescription(
        DocBlock $docBlock,
        Endpoint $endpoint
    ): void {
        $descriptionText = Str

            // Fetch the text from the description.
            // TODO: This would be an excellent opportunity to render links
            //       between output pages, by creating a similar structure to
            //       the PHPDoc description format used here.
            ::of($docBlock->getDescription()->render())

            // TODO: These stay disabled for now, as they mess with the Markdown
            //       parser. Proper space handling should be revisited later.

            // Ensure we remove all lone line breaks, but preserve any that
            // occur in sequence. This prevents description text aligned to
            // a maximum line length having seemingly random breaks, but
            // still allows to separate text into paragraphs.
            // ->replaceMatches('/\n([^\s])/', ' $1')

            // Replace double spaces (Markdown) or break tags (HTML) with
            // actual line breaks. While we're probably going to output
            // either later on, we can standardize it this way.
            // ->replace(['  ', '<br>'], "\n")
        ;

        if ($descriptionText->isNotEmpty()) {
            $endpoint->setDescription((string)$descriptionText);
        }
    }

    protected function applyDeprecation(
        DocBlock $docBlock,
        Endpoint $endpoint
    ): void {
    }
}
