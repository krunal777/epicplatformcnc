<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Composer\InstalledVersions;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ApiVersion
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        if ($response instanceof BinaryFileResponse) {
            $response->headers->set('x-api-version', $this->getApiVersion());
        } else {
            $response->header('x-api-version', $this->getApiVersion());
        }

        return $response;
    }

    private function getApiVersion(): ?string
    {
        if (App::isProduction()) {
            return InstalledVersions::getPrettyVersion('futureecom/futureecom');
        }

        return sprintf(
            '%s@%s',
            InstalledVersions::getPrettyVersion('futureecom/futureecom'),
            InstalledVersions::getReference('futureecom/futureecom'),
        );
    }
}
