<?php


namespace WA\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * Register all the service providers for the DataStore Repositories
 * which are independent of the underlying persistent layer.
 *
 * Class RepositoriesServiceProviders
 */
class RepositoriesServiceProviders extends ServiceProvider
{
    use ServiceRegistration;

    /**
     * Register all Providers.
     */
    public function register()
    {
        
        $this->registerAll();

        $this->registerAsset();
        $this->registerDevice();
        $this->registerDeviceType();
        $this->registerEmployee();
        $this->registerCompany();
        $this->registerCensus();
        $this->registerJobStatus();
        $this->registerUdl();
        $this->registerUdlValue();
        $this->registerAttribute();
        $this->registerCarrier();
        $this->registerSyncJob();
        $this->registerCarrierDetail();
        $this->registerLocation();
        $this->registerUdlValuePath();
        $this->registerUdlValuePathEmployees();
        $this->registerNotificationCategory();
        $this->registerEmployeeNotification();
        $this->registerEmailNotifications();
        $this->registerRole();
        $this->registerPermission();
        $this->registerAllocation();
        $this->registerPages();
    }
}
