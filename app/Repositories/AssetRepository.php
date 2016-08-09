<?php


namespace WA\Repositories;

use WA\DataStore\Asset\Asset;
use WA\DataStore\User\User;

/**
 * Class AssetRepository.
 */
class AssetRepository extends BaseRepository implements AssetRepositoryInterface
{
    protected $cache = 5;

    /**
     * @param Asset $dataStore
     */
    public function __construct(Asset $dataStore)
    {
        parent::__construct($dataStore);
    }

    /**
     * Find of create a non-existing asset and assign it to a user (if it doe not exist).
     *
     * @param string $identification
     *
     * @return Asset $asset
     */
    public function matchIdentificationToUser($identification)
    {
        /* @var $asset Asset */
        $asset = $this->findWhere('identification', $identification)->first() ?: $this->dataStore->create(['identification' => $identification]);

        // if ($asset->users()->first() === null) {
//            $asset = $this->generateUserAndAssignToAsset($asset, $identification);
//        }

        return $asset;
    }

    /**
     * Generates a random user (useful when we don't have the user already in the User table).
     */
    private function generateUserAndAssignToAsset(Asset $asset, $prefix = null)
    {
        //        $prefix = $prefix ? : substr(md5(rand()), 0, 2) . mt_rand();
//        $username = 'user-' . $prefix;
        $email = $prefix.'@wirelessanalytics.com';

        try {
            $user = new User();
            $user->email = $email;
            $user->password = explode($email, '@')[0];
            $asset->users()->save($user);
        } catch (\PDOException $e) {
            \Log::error('Could not insert the asset to the user: '.$e->getMessage());
        }

        return $asset;
    }
}
