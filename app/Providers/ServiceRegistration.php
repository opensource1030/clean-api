<?php

namespace WA\Providers;

use Illuminate\Contracts\Foundation\Application;
use WA\DataStore\Allocation\Allocation;
use WA\DataStore\Asset\Asset;
use WA\DataStore\Attribute;

use WA\DataStore\Carrier\Carrier;
use WA\DataStore\Carrier\CarrierDetail;
use WA\DataStore\Census;
use WA\DataStore\Company\Company;
use WA\DataStore\Device\Device;
use WA\DataStore\DeviceType;
use WA\DataStore\EmailNotifications;
use WA\DataStore\Employee\Employee;
use WA\DataStore\EmployeeNotifications;
use WA\DataStore\JobStatus;
use WA\DataStore\Location\Location;
use WA\DataStore\NotificationCategory;
use WA\DataStore\Page\Page;
use WA\DataStore\SyncJob;
use WA\DataStore\Udl\Udl;
use WA\DataStore\UdlValue\UdlValue;
use WA\DataStore\UdlValuePath\UdlValuePath;
use WA\DataStore\UdlValuePathEmployees\UdlValuePathEmployees;
use WA\DataStore\Role\Role;
use WA\Repositories\Asset\EloquentAsset;
use WA\Repositories\Attribute\EloquentAttribute;
use WA\Repositories\Carrier\EloquentCarrier;
use WA\Repositories\Carrier\EloquentCarrierDetail;
use WA\Repositories\Census\EloquentCensus;
use WA\Repositories\Company\EloquentCompany;
use WA\Repositories\Device\EloquentDevice;
use WA\Repositories\DeviceType\EloquentDeviceType;
use WA\Repositories\EmailNotifications\EloquentEmailNotifications;
use WA\Repositories\Employee\EloquentEmployee;
use WA\Repositories\EmployeeNotifications\EloquentEmployeeNotifications;
use WA\Repositories\JobStatus\EloquentJobStatus;
use WA\Repositories\Location\EloquentLocation;
use WA\Repositories\NotificationCategory\EloquentNotificationCategory;
use WA\Repositories\Pages\EloquentPages;
use WA\Repositories\SyncJob\EloquentSyncJob;
use WA\Repositories\Udl\EloquentUdl;
use WA\Repositories\UdlValue\EloquentUdlValue;
use WA\Repositories\UdlValuePath\EloquentUdlValuePath;
use WA\Repositories\UdlValuePathEmployees\EloquentUdlValuePathEmployees;
use WA\Repositories\Employee\EmployeeCacheDecorator;
use WA\Services\Cache\Cache;
use WA\Repositories\Role\EloquentRole;
use WA\Repositories\Permission\EloquentPermission;
use WA\Repositories\Allocation\EloquentAllocation;


/**
 * Class ServiceRegistration.
 */
trait ServiceRegistration
{
    /**
     * @param
     */
    public function registerDevice()
    {

        app()->singleton(
            'WA\Repositories\Device\DeviceInterface',
            function () {
                return new EloquentDevice(new Device(),
                    app()->make('WA\Repositories\JobStatus\JobStatusInterface')
                );
            }
        );
    }

    /**
     * @param
     */
    public function registerCensus()
    {
        app()->bind(
            'WA\Repositories\Census\CensusInterface',
            function () {
                return new EloquentCensus(new Census(), app()->make('WA\Repositories\JobStatus\JobStatusInterface'));
            }
        );
    }


    /**
     * @param
     */
    public function registerJobStatus()
    {
        app()->bind(
            'WA\Repositories\JobStatus\JobStatusInterface',
            function () {
                return new EloquentJobStatus(new JobStatus());
            }
        );
    }

    /**
     * @param
     */
    public function registerUdl()
    {
        app()->bind(
            'WA\Repositories\Udl\UdlInterface',
            function () {
                return new EloquentUdl(new Udl(), app()->make('WA\Repositories\UdlValue\UdlValueInterface'));
            }
        );
    }

    /**
     * @param
     */
    public function registerUdlValue()
    {
        app()->bind(
            'WA\Repositories\UdlValue\UdlValueInterface',
            function () {
                return new EloquentUdlValue(new UdlValue());
            }
        );
    }

    /**
     * @param
     */
    public function registerUdlValuePath()
    {

        app()->bind(
            'WA\Repositories\UdlValuePath\UdlValuePathInterface',
            function () {
                return new EloquentUdlValuePath(new UdlValuePath());
            }
        );
    }

    /**
     * @param
     */
    public function registerUdlValuePathEmployees()
    {
        app()->bind(
            'WA\Repositories\UdlValuePathEmployees\UdlValuePathEmployeesInterface',
            function () {
                return new EloquentUdlValuePathEmployees(new UdlValuePathEmployees());
            }
        );
    }

    /**
     *register all non - manual resolutions.
     *
     * @param
     */
    protected function registerAll()
    {


        app()->bind('WA\Repositories\AssetRepositoryInterface', 'WA\Repositories\AssetRepository');
        app()->bind('WA\Repositories\UserCarrierRepositoryInterface', 'WA\Repositories\UserCarrierRepository');
        app()->bind('WA\Repositories\DumpExceptionRepositoryInterface', 'WA\Repositories\DumpExceptionRepository');
        app()->bind('WA\Repositories\JobStatusRepositoryInterface', 'WA\Repositories\JobStatusRepository');
        app()->bind('WA\Repositories\CurrentChargeRepositoryInterface', 'WA\Repositories\CurrentChargeRepository');
        app()->bind('WA\Repositories\ServiceBundleRepositoryInterface', 'WA\Repositories\ServiceBundleRepository');
        app()->bind(
            'WA\Repositories\CallDetailSummaryRepositoryInterface',
            'WA\Repositories\CallDetailSummaryRepository'
        );
        app()->bind('WA\Repositories\DeviceTypeRepositoryInterface', 'WA\Repositories\DeviceTypeRepository');
        app()->bind('WA\Repositories\CarrierDeviceRepositoryInterface', 'WA\Repositories\CarrierDeviceRepository');
        app()->bind('WA\Repositories\ConsolidatedCdrRepositoryInterface', 'WA\Repositories\ConsolidatedCdrRepository');
    }

    /**
     * @param
     */
    protected function registerCompany()
    {
        app()->bind(
            'WA\Repositories\Company\CompanyInterface',
            function () {
                return new EloquentCompany(
                    new Company(),
                    app()->make('WA\Repositories\Employee\EmployeeInterface'),
                    app()->make('WA\Repositories\Census\CensusInterface'),
                    app()->make('WA\Repositories\Udl\UdlInterface'),
                    app()->make('WA\Repositories\Carrier\CarrierInterface'),
                    app()->make('WA\Repositories\Device\DeviceInterface'),
                    app()->make('WA\Repositories\Carrier\CarrierDetailInterface')
                );
            }
        );
    }

    /**
     * @param
     */
    protected function registerEmployee()
    {
        app()->bind(
            'WA\Repositories\Employee\EmployeeInterface',
            function () {
                $employee = new EloquentEmployee(
                    new Employee(),
                    app()->make('WA\Repositories\Census\CensusInterface'),
                    app()->make('WA\Repositories\UdlValue\UdlValueInterface'),
                    app()->make('WA\Repositories\Udl\UdlInterface'),
                    app()->make('WA\Services\Form\HelpDesk\EasyVista')
                );
                //return $employee
                //
                return new EmployeeCacheDecorator(
                    $employee,
                    new Cache(app()['cache'], 'employees', 10)
                );
            }
        );
    }

    /**
     * @param
     */
    protected function registerAsset()
    {
        app()->singleton(
            'WA\Repositories\Asset\AssetInterface',
            function () {
                return new EloquentAsset(new Asset(),
                    app()->make('WA\Repositories\JobStatus\JobStatusInterface'),
                    app()->make('WA\Repositories\Employee\EmployeeInterface'),
                    app()->make('WA\Services\Converter\Currency'),
                    app()->make('WA\Repositories\Carrier\CarrierInterface'));
            }
        );
    }

    /**
     * @param
     */
    protected function registerAttribute()
    {
        app()->bind(
            'WA\Repositories\Attribute\AttributeInterface',
            function () {
                return new EloquentAttribute(new Attribute());
            }
        );
    }

    /**
     * @param
     */
    protected function registerDeviceType()
    {
        app()->bind('WA\Repositories\DeviceType\DeviceTypeInterface',
            function () {
                return new EloquentDeviceType(new DeviceType());
            });
    }

    /**
     * @param
     */
    protected function registerCarrier()
    {
        app()->bind('WA\Repositories\Carrier\CarrierInterface',
            function () {
                return new EloquentCarrier(new Carrier());
            });
    }


    /**
     * @param
     */
    public function registerSyncJob()
    {
        app()->bind('WA\Repositories\SyncJob\SyncJobInterface', function ($app) {
            return new EloquentSyncJob(
                new SyncJob(),
                app()->make('WA\Repositories\JobStatus\JobStatusInterface')
            );
        });
    }


    /**
     * @param
     */
    public function registerCarrierDetail()
    {
        app()->bind('WA\Repositories\Carrier\CarrierDetailInterface', function ($app) {
            return new EloquentCarrierDetail(new CarrierDetail());
        });
    }

    /**
     * @param
     */
    public function registerLocation()
    {
        app()->bind('WA\Repositories\Location\LocationInterface', function () {
            return new EloquentLocation(new Location());
        });
    }


    /**
     * @param
     */
    protected function registerNotificationCategory()
    {
        app()->bind('WA\Repositories\NotificationCategory\NotificationCategoryInterface',
            function () {
                return new EloquentNotificationCategory(new NotificationCategory());
            });
    }

    /**
     * @param
     */
    public function registerEmployeeNotification()
    {
        app()->bind('WA\Repositories\EmployeeNotifications\EmployeeNotificationsInterface',
            function () {
                return new EloquentEmployeeNotifications(new EmployeeNotifications());
            });
    }

    public function registerEmailNotifications()
    {
        app()->bind('WA\Repositories\EmailNotifications\EmailNotificationsInterface',
            function () {
                return new EloquentEmailNotifications(new EmailNotifications());
            });
    }

    public function registerRole()
    {
        app()->bind('WA\Repositories\Role\RoleInterface',
            function () {
                return new EloquentRole(new Role());
            });
    }

    public function registerPermission()
    {
        app()->bind('WA\Repositories\Permission\PermissionsInterface',
            function () {
                return new EloquentPermission(app()->make('WA\Repositories\Role\RoleInterface'));
            });
    }

    public function registerAllocation()
    {
        app()->bind('WA\Repositories\Allocation\AllocationInterface',
            function () {
                return new EloquentAllocation(new Allocation());
            });
    }

    public function registerPages()
    {
        app()->bind('WA\Repositories\Pages\PagesInterface',
            function () {
                return new EloquentPages(new Page());
            });
    }

}
