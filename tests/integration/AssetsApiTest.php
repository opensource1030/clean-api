<?php



class AssetsApiTest extends \TestCase
{
    use \Laravel\Lumen\Testing\DatabaseMigrations;

    public function testGetAssets()
    {
        factory(\WA\DataStore\Asset\Asset::class, 40)->create();

        $this->json('GET', 'assets')
            ->seeJsonStructure([
                'data' => [
                    0 => [
                        'type',
                        'id',
                        'attributes' => [
                            'identification',
                            'active',
                        ],
                        'links' => [
                            'self',
                        ],
                    ],
                ],
            ]);
    }

    public function testGetAssetById()
    {
        $asset = factory(\WA\DataStore\Asset\Asset::class)->create(['userId' => $this->mainUser->id]);

        $this->json('GET', 'assets/'.$asset->id)
            ->seeJson([
                'type' => 'assets',
                'id' => "$asset->id",
                'identification' => $asset->identification,
                'active' => "$asset->active",
            ]);
    }
}
