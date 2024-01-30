<?php

namespace App\Providers;

use App\Interfaces\DepDocWisePatientCountReportInterface;
use App\Interfaces\DepWiseCollectionReportInterface;
use App\Interfaces\PatientDetailReportInterface;
use App\Interfaces\SalesBookDetailReportInterface;
use App\Interfaces\UserWiseCollectionReportInterface;
use App\Repository\DepDocWisePatientCountReportRepository;
use App\Repository\DepWiseCollectionReportRepository;
use App\Repository\PatientDetailReportRepository;
use App\Repository\SalesBookDetailReportRepository;
use App\Repository\UserWiseCollectionReportRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(PatientDetailReportInterface::class, PatientDetailReportRepository::class);
        $this->app->bind(UserWiseCollectionReportInterface::class, UserWiseCollectionReportRepository::class);
        $this->app->bind(DepWiseCollectionReportInterface::class, DepWiseCollectionReportRepository::class);
        $this->app->bind(DepDocWisePatientCountReportInterface::class, DepDocWisePatientCountReportRepository::class);
        $this->app->bind(SalesBookDetailReportInterface::class, SalesBookDetailReportRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
