<?php

namespace WA\Repositories\HelpDesk;

/**
 * Interface HelpDeskInterface.
 */
interface HelpDeskInterface
{
    /**
   * Get the count of asset
   * by default, it gets all once that do not exists in CLEAN.
   *
   * @return int count of assets
   */
  public function getAssetCount();

  /**
   * Get all the devices.
   *
   * by default, it gets all once that do not exists in CLEAN
   *
   * @return int
   */
  public function getDeviceCount();

  /**
   * Get all assets
   * by default, it gets all once that do not exists in CLEAN.
   *
   * @return array of assets
   */
  public function getAssets();

  /**
   * Get all devices.
   *
   * @return array devices
   */
  public function getDevices();

  /**
   * Get all the devices linked to Assets,
   * by default it gets only un-synced links.
   *
   * @param bool $pending
   *
   * @return array of linked assets to devices
   */
  public function getLinkedAssetDevices($pending = true);

  /**
   * Get asset by identification.
   *
   * @param string $identification
   */
  public function assetByIdentification($identification);

  /**
   * Get the asset changelog.
   *
   * @param bool $pending
   *
   * @return array of change logs
   */
  public function assetChangeLog($pending = true);

  /**
   * Get device by identification.
   *
   * @param string $identification
   */
  public function deviceByIdentification($identification);

  /**
   * Get an employee by name.
   *
   * @param string $name
   *
   * @return array name information
   */
  public function employeeByName($name);

  /**
   * Get all validators by their companyID.
   *
   * @param int $companyId
   *
   * @return Object object of validators
   */
  public function getValidators($companyId);

  /**
   * Get all supervisors but their companyId (Sups?).
   *
   * @oaram int $companyId
   *
   * @return Object object of supervisors/managers
   */
  public function getSupervisors($companyId);

  /**
   * Get an employee by the identification.
   *
   * @param string $identification
   *
   * @return string of identification
   */
  public function employeeByIdentification($identification);

  /**
   * Get the departmental path.
   *
   * @param string $path
   *
   * @return int id of the path
   */
  public function getDepartmentId($path);

  /**
   * Get EasyVista Id based on CLEAN ID
   *
   * @param $id
   * @return int $id
   */
  public function getEmployeeExternalId($id);

  /**
   * Get the count of department paths that are not currently synced with CLEAN from Easy Vista.
   *
   */
  public function getDeptPathCount();

  /**
   * Get department paths that are not currently synced with CLEAN from Easy Vista.
   *
   */
  public function getDeptPath();


}
