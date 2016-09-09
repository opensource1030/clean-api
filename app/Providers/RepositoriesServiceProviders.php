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

        $this->registerApp();
        $this->registerAsset();
        $this->registerDevice();
        $this->registerCarrier();
        $this->registerCarrierDetail();
        $this->registerDeviceCarrier();
        $this->registerCompany();
        $this->registerDeviceCompany();
        $this->registerImage();
        $this->registerDeviceImage();        
        $this->registerModification();
        $this->registerDeviceModification();
        $this->registerDevicePrice();
        $this->registerDeviceType();        
        $this->registerCensus();
        $this->registerUser();
        $this->registerJobStatus();
        $this->registerUdl();
        $this->registerUdlValue();
        $this->registerAttribute();
        $this->registerSyncJob();
        $this->registerCarrierDetail();
        $this->registerLocation();
        $this->registerUdlValuePath();
        $this->registerUdlValuePathUsers();
        $this->registerNotificationCategory();
        $this->registerUserNotification();
        $this->registerEmailNotifications();
        $this->registerRole();
        $this->registerPermission();
        $this->registerAllocation();
        $this->registerContent();
        $this->registerHelpDesk();
        $this->registerService();
        $this->registerOrder();
        $this->registerPackage();
        $this->registerRequest();
    }
}
