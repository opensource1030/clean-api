<?php

namespace WA\Http\Controllers\Admin;

use Redirect;
use View;
use WA\Http\Controllers\Auth\AuthorizedController;
use WA\Services\Form\HelpDesk\SyncForm;

/**
 * Class HelpDeskSyncController.
 */
class HelpDeskSyncController extends AuthorizedController
{
    /**
     * @var SyncForm
     */
    protected $syncForm;

    /**
     * @param SyncForm $syncForm
     */
    public function __construct(SyncForm $syncForm)
    {
        $this->syncForm = $syncForm;

        parent::__construct();
    }

    public function index()
    {
        $amount = 10;
        $syncs = $this->syncForm->getSyncs($amount);
        $lastSyncTime = $this->syncForm->getLastSync();

        $data = [
            'helpDesk' => 'EasyVista',
            'isReady' => $this->syncForm->isReady(),
            'isComplete' => $this->syncForm->isComplete(),
            'syncs' => $syncs,
            'amount' => $amount,
            'lastSyncTime' => $lastSyncTime,
        ];

        return View::make('admin.sync.index', $data);
    }

    public function update()
    {
        if (!$this->syncForm->runLoader()) {
            return Redirect::back();
        };

        return Redirect::route('hd.sync.index');
    }

    public function report()
    {
        return View::make('admin.sync.report');
    }
}
