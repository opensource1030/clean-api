<?php

namespace WA\Providers;

use Illuminate\Support\ServiceProvider;
use WA\Services\Form\Asset\AssetForm;
use WA\Services\Form\Asset\AssetFormValidator;
use WA\Services\Form\Company\CensusForm;
use WA\Services\Form\Company\CensusFormValidator;
use WA\Services\Form\Dashboard\DashboardForm;
use WA\Services\Form\Dashboard\DashboardFormValidator;
use WA\Services\Form\Device\DeviceForm;
use WA\Services\Form\Device\DeviceFormValidator;
use WA\Services\Form\User\UserForm;
use WA\Services\Form\HelpDesk\UserSyncForm;
use WA\Services\Form\HelpDesk\SyncForm;
use WA\Services\Form\Login\LoginForm;
use WA\Services\Form\Login\LoginFormValidator;

/**
 * Class FormServiceProvider.
 */
class FormServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     */
    public function register()
    {
        $this->registerLogin();
        $this->registerUser();
        $this->registerDashboardForm();
        $this->registerAssetForm();
        $this->registerDeviceForm();
        $this->registerSyncForm();
        $this->registerUserSyncForm();
        $this->registerCensusForm();
    }

    /**
     * @param
     */
    protected function registerLogin()
    {
        app()->bind(
            'WA\Services\Form\Login\LoginForm',
            function () {
                return new LoginForm(
                    app()->make('WA\Auth\Auth'),
                    new LoginFormValidator(app()['validator']),
                    app()->make('WA\Repositories\Allocation\AllocationInterface')

                );
            }
        );
    }

    /**
     * @param
     */
    protected function registerUser()
    {
        app()->bind(
            'WA\Services\Form\User\UserForm',
            function () {
                return new UserForm(
                    app()->make('WA\Repositories\User\UserInterface'),
//                    new UserFormValidator($app['validator']),
//                    app()->make('WA\Services\Soap\HelpDeskEasyVista'),
//                    app()->make('WA\Repositories\HelpDesk\HelpDeskInterface'),
//                    app()->make('WA\Repositories\Udl\UdlInterface'),
//                    $app['session'],
                    app()->make('WA\Repositories\Company\CompanyInterface'),
                    app()->make('WA\Repositories\Role\RoleInterface'),
                    app()->make('WA\Repositories\Allocation\AllocationInterface')
                );
            }
        );
    }

    /**
     * @param
     */
    protected function registerDashboardForm()
    {
        app()->bind('WA\Services\Form\Dashboard\DashboardForm', function () {
            return new DashboardForm(
                app()->make('WA\Repositories\Company\CompanyInterface'),
                new DashboardFormValidator(app()['validator']),
                app()->make('WA\Repositories\DumpRepositoryInterface')
            );
        });
    }

    /**
     * @param
     */
    protected function registerAssetForm()
    {
        app()->bind('WA\Service\Form\Asset\AssetForm', function () {
            return new AssetForm(
                app()->make('WA\Repositories\Asset\AssetInterface'),
                app()->make('WA\Repositories\JobStatus\JobStatusInterface'),
                new AssetFormValidator(app()['validator']));
        });
    }

    /**
     * @param
     */
    protected function registerDeviceForm()
    {
        app()->bind('WA\Service\Form\Device\DeviceForm', function () {
            return new DeviceForm(
                app()->make('WA\Repositories\Devices\DeviceInterface'),
                new DeviceFormValidator(app()['validator'])
            );
        });
    }

    /// -- SYNCS
    /**
     * @param
     */
    protected function registerSyncForm()
    {
        app()->bind('WA\Service\Form\HelpDesk\SyncForm', function () {
            return new SyncForm(app()->make('WA\DataLoader\HelpDesk\Loader'),
                app()->make('Illuminate\Queue\QueueManager'),
                app()->make('WA\Repositories\SyncJob\SyncJobInterface'));
        });
    }

    /**
     * @param
     */
    protected function registerUserSyncForm()
    {
        app()->bind('WA\Service\Form\HelpDesk\UserSyncForm', function () {
            return new UserSyncForm(app()->make('WA\DataLoader\HelpDesk\Loader\UserLoader'),
                app()->make('Illuminate\Queue\QueueManager'),
                app()->make('WA\Repositories\SyncJob\SyncJobInterface'));
        });
    }
/// --  END SYNCS

    /**
     * @param
     */
    protected function registerCensusForm()
    {
        app()->bind(
            'WA\Services\Form\Company\CensusForm',
            function () {
                return new CensusForm(
                    new CensusFormValidator(app()['validator']),
                    app()->make('WA\Repositories\Company\CompanyInterface'),
                    app()->make('WA\DataLoader\Census\Census'),
                    app()->make('WA\Parsers\Census\Census')
                );
            }
        );
    }
}
