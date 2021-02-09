<?php

declare(strict_types=1);

namespace Matchory\Herodot\Http\Controllers;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use JsonException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

use function json_decode;

use const JSON_THROW_ON_ERROR;

class HerodotController extends Controller
{
    /**
     * @return string
     */
    public function webPage(): string
    {
        $path = Config::get('herodot.blade.output_file');

        return Storage::get($path);
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
