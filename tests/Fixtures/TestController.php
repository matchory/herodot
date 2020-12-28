<?php

declare(strict_types=1);

namespace Matchory\Herodot\Tests\Fixtures;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use JetBrains\PhpStorm\ArrayShape;
use Matchory\Herodot\Attributes\BodyParam;
use Matchory\Herodot\Attributes\Description;
use Matchory\Herodot\Attributes\Group;
use Matchory\Herodot\Attributes\Header;
use Matchory\Herodot\Attributes\QueryParam;
use Matchory\Herodot\Attributes\Title;
use Matchory\Herodot\Attributes\UrlParam;

class TestController extends Controller
{
    #[Header('X-Foo')]
    #[Header('X-Bar', '42')]
    #[QueryParam('foo', required: true, meta: ['foo' => 'bar'])]
    #[UrlParam('id', 'int', description: 'This is a test', meta: [
        'foo' => 'bar'
    ])]
    #[BodyParam('id', 'int', validationRules: ['integer', 'min:1'], meta: [
        'foo' => 'bar'
    ])]
    #[
        Group('Foo and Bar'),
        Title('Fetch all Foo'),
        Description("This is a test description\nsporting newlines!")
    ]
    public function index(): JsonResponse
    {
    }
}
