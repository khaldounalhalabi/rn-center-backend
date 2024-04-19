<?php

namespace App\Providers;

use App\Models\Service;
use App\Models\ServiceCategory;
use App\Repositories\ServiceCategoryRepository;
use App\Repositories\ServiceRepository;
use App\Services\Service\IServiceService;
use App\Services\Service\ServiceService;
use App\Services\ServiceCategory\IServiceCategoryService;
use App\Services\ServiceCategory\ServiceCategoryService;
use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;

class CubetaStarterServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->bindAllRepositoriesAndServices();

        $this->app->bind(ServiceCategoryRepository::class, function ($app) {
            return new (ServiceCategoryRepository::class)(
                $app->make(ServiceCategory::class)
            );
        });

        $this->app->bind(
            IServiceCategoryService::class,
            ServiceCategoryService::class
        );

        $this->app->bind(ServiceRepository::class, function ($app) {
            return new (ServiceRepository::class)(
                $app->make(Service::class)
            );
        });

        $this->app->bind(
            IServiceService::class,
            ServiceService::class
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {

    }

    /**
     * Loop through all the repositories and the services and bind them
     */
    private function bindAllRepositoriesAndServices(): void
    {
        if (file_exists(app_path() . '/Repositories')) {
            $repositoryFiles = File::allFiles(app_path() . '/Repositories');
            foreach ($repositoryFiles as $repositoryFile) {
                $repositoryFileName = $repositoryFile->getBasename();
                $repository = str_replace('.php', '', $repositoryFileName);
                $model = str_replace('Repository', '', $repository);
                $path = str_replace('/', DIRECTORY_SEPARATOR, app_path() . '/Models/' . $model . '.php');
                if (file_exists($path)) {
                    $this->app->bind('App\Repositories\\' . $repository, function ($app) use ($repository, $model) {
                        return new ('App\Repositories\\' . $repository)(
                            $app->make('\App\Models\\' . $model)
                        );
                    });
                }
            }
        }

        if (file_exists(app_path() . '/Services')) {
            $serviceFiles = File::allFiles(app_path() . '/Services');
            $services = [];
            foreach ($serviceFiles as $serviceFile) {
                $serviceFileName = $serviceFile->getBasename();
                $service = str_replace('.php', '', $serviceFileName);
                $iService = 'I' . $service;
                $modelName = str_replace('Service', '', $service);

                $path = str_replace('/', DIRECTORY_SEPARATOR, app_path() . '/Services/' . $modelName . '/' . $iService . '.php');
                if (in_array($service, $services)) {
                    continue;
                }

                if (file_exists($path)) {
                    $this->app->bind(
                        'App\Services\\' . $modelName . '\\' . $iService,
                        'App\Services\\' . $modelName . '\\' . $service
                    );

                    $services[] = $service;
                }
            }
        }
    }
}
