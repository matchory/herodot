<?php

declare(strict_types=1);

namespace Matchory\Herodot\Http\Controllers;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View as ViewFacade;
use JsonException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

use function json_decode;

use const JSON_THROW_ON_ERROR;

class HerodotController extends Controller
{
    /**
     * @return View
     */
    public function webPage(): View
    {
        return ViewFacade::make('herodot::index');
    }

    /**
     * @return JsonResponse
     * @throws FileNotFoundException
     * @throws JsonException
     *
     */
    public function postman(): JsonResponse
    {
        return Response::json(json_decode(
            $this->disk()->get('herodot/postman.json'),
            true,
            512,
            JSON_THROW_ON_ERROR
        ));
    }

    /**
     * @return BinaryFileResponse
     * @throws FileNotFoundException
     */
    public function openapi(): BinaryFileResponse
    {
        return Response::file($this->disk()->get(
            'herodot/openapi.yaml'
        ));
    }

    /**
     * @return Filesystem
     */
    protected function disk(): Filesystem
    {
        return Storage::disk('local');
    }
}
