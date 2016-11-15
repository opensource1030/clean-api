<?php

class UserAssetsTableSeeder extends BaseTableSeeder
{
    protected $table = 'user_assets';

    public function run()
    {
        $this->deleteTable();

        $data = [
            [
                'userId' => 1,
                'assetId' => 1
            ],
            [
                'userId' => 1,
                'assetId' => 2
            ],
            [
                'userId' => 1,
                'assetId' => 3
            ],
            [
                'userId' => 1,
                'assetId' => 4
            ],
            [
                'userId' => 2,
                'assetId' => 1
            ],
            [
                'userId' => 2,
                'assetId' => 3
            ],
            [
                'userId' => 2,
                'assetId' => 5
            ],
            [
                'userId' => 2,
                'assetId' => 6
            ],
            [
                'userId' => 3,
                'assetId' => 1
            ],
            [
                'userId' => 3,
                'assetId' => 4
            ],
            [
                'userId' => 3,
                'assetId' => 6
            ],
            [
                'userId' => 3,
                'assetId' => 7
            ],
        ];

        $this->loadTable($data);
    }
}
