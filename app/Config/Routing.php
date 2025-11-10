<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

/**
 * Routing Configuration
 *
 * @see https://codeigniter4.github.io/userguide/incoming/routing.html
 */
class Routing extends BaseConfig
{
    public string $defaultNamespace = 'App\Controllers';
    public string $defaultController = 'Home';
    public string $defaultMethod = 'index';
    public bool   $translateURIDashes = false;
    public bool $multipleSegmentsOneParam = false;
    public bool   $autoRoute = false;
    public bool   $autoRouteImproved = true;
    public bool   $override404 = false;
    public ?string $filterNamespace = null;
    public array  $reservedRoutes = [];

    /**
     * List of files that contain route definitions.
     *
     * @var list<string>
     */
    public array $routeFiles = [];

    /**
     * Whether to prioritize previously defined routes.
     * Added in CI 4.6.x
     */
    public bool $prioritize = false;
}
