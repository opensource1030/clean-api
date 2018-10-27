<?php

class ApiFiltersTest extends \TestCase
{
    use \Laravel\Lumen\Testing\DatabaseMigrations;

    public function testCanIncludeFiltersInMeta()
    {
        if ($this->idLogged == 1) {
            $filter = [
                '[identification]=296'
            ];
        } else {
            $filter = [
                '[devicevariations.companyId]=1',
                '[identification]=296'
            ];
        }

        $this->json('GET', '/devices?filter[identification]=296')
            ->seeJson(['filter' => $filter]);
    }

    public function testCanIncludeMultipleFiltersInMeta()
    {
        if ($this->idLogged == 1) {
            $filter = [
                '[identification]=296',
                '[id]=15'
            ];
        } else {
            $filter = [
                '[devicevariations.companyId]=1',
                '[identification]=296',
                '[id]=15'
            ];
        }

        $this->json('GET', '/devices?filter[identification]=296&filter[id]=15')
            ->seeJson(['filter' => $filter]);
    }

    public function testCanIncludeFiltersWithDelimittedCriteriaInMeta()
    {
        if ($this->idLogged == 1) {
            $filter = [
                '[id]=2,4'
            ];
        } else {
            $filter = [
                '[devicevariations.companyId]=1',
                '[id]=2,4'
            ];
        }

        $this->json('GET', '/devices?filter[id]=2,4')
            ->seeJson(['filter' => $filter]);
    }

    // Per JSONAPI, invalid criteria MUST return a 400
    public function testWorkProperlyWithIncorrectSortCriteria()
    {
        $response = $this->call('GET', '/devices?filter[blahblah]=9001');
        $this->assertEquals(400, $response->status());
    }

    public function testCanIncludeFiltersWithOrCriteriaInMeta()
    {
        if ($this->idLogged == 1) {
            $filter = [
                '[0]=[devicetypes.name]=Smartphone[or][devicetypes.name]=Tablet'
            ];
        } else {
            $filter = [
                '[devicevariations.companyId]=1',
                '[0]=[devicetypes.name]=Smartphone[or][devicetypes.name]=Tablet'
            ];
        }

        $this->json('GET', '/devices?include=devicetypes&filter[]=[devicetypes.name]=Smartphone[or][devicetypes.name]=Tablet')
            ->seeJson(['filter' => $filter]);
    }

    public function testCanIncludeFiltersWithMultipleOrCriteriaInMeta()
    {
        if ($this->idLogged == 1) {
            $filter = [
                '[0]=[devicetypes.name]=Smartphone[or][devicetypes.name]=Tablet',
                '[1]=[devicetypes.name]=Smartphone[or][devicetypes.name]=Tablet'
            ];
        } else {
            $filter = [
                '[devicevariations.companyId]=1',
                '[0]=[devicetypes.name]=Smartphone[or][devicetypes.name]=Tablet',
                '[1]=[devicetypes.name]=Smartphone[or][devicetypes.name]=Tablet'
            ];
        }

        $this->json('GET', '/devices?include=devicetypes&filter[]=[devicetypes.name]=Smartphone[or][devicetypes.name]=Tablet&filter[]=[devicetypes.name]=Smartphone[or][devicetypes.name]=Tablet')
            ->seeJson(['filter' => $filter]);
    }

    public function testFilterModelByItsOwnAttributesStringDefault() {

        $device1 = factory(\WA\DataStore\Device\Device::class)->create(['name' => 'name1']);
        $device2 = factory(\WA\DataStore\Device\Device::class)->create(['name' => 'Name1']);
        $device3 = factory(\WA\DataStore\Device\Device::class)->create(['name' => 'name122']);

        $devVar1 = factory(\WA\DataStore\DeviceVariation\DeviceVariation::class)->create(['companyId' => $this->mainCompany->id, 'deviceId' => $device1->id]);
        $devVar2 = factory(\WA\DataStore\DeviceVariation\DeviceVariation::class)->create(['companyId' => $this->mainCompany->id, 'deviceId' => $device2->id]);
        $devVar3 = factory(\WA\DataStore\DeviceVariation\DeviceVariation::class)->create(['companyId' => $this->mainCompany->id, 'deviceId' => $device3->id]);

        if ($this->idLogged == 1) {
            $filter = [
                '[name]=name1'
            ];
        } else {
            $filter = [
                '[name]=name1',
                '[devicevariations.companyId]=1'
            ];
        }

        $resArray = (array)json_decode($this->json('GET', '/devices?filter[name]=name1')->response->getContent());

        $this->assertCount(1, $resArray['data']);
        $this->assertEquals($resArray['data'][0]->id, $device1->id);
        $this->assertEquals($resArray['meta']->filter, $filter);
    }

    public function testFilterModelByItsOwnAttributesStringEqual() {

        $device1 = factory(\WA\DataStore\Device\Device::class)->create(['name' => 'name1']);
        $device2 = factory(\WA\DataStore\Device\Device::class)->create(['name' => 'Name1']);
        $device3 = factory(\WA\DataStore\Device\Device::class)->create(['name' => 'name122']);

        $devVar1 = factory(\WA\DataStore\DeviceVariation\DeviceVariation::class)->create(['companyId' => $this->mainCompany->id, 'deviceId' => $device1->id]);
        $devVar2 = factory(\WA\DataStore\DeviceVariation\DeviceVariation::class)->create(['companyId' => $this->mainCompany->id, 'deviceId' => $device2->id]);
        $devVar3 = factory(\WA\DataStore\DeviceVariation\DeviceVariation::class)->create(['companyId' => $this->mainCompany->id, 'deviceId' => $device3->id]);

        if ($this->idLogged == 1) {
            $filter = [
                '[name]=name1'
            ];
        } else {
            $filter = [
                '[name]=name1',
                '[devicevariations.companyId]=1'
            ];
        }

        $resArray = (array)json_decode($this->json('GET', '/devices?filter[name][eq]=name1')->response->getContent());

        $this->assertCount(1, $resArray['data']);
        $this->assertEquals($resArray['data'][0]->id, $device1->id);
        $this->assertEquals($resArray['meta']->filter, $filter);
    }

    public function testFilterModelByItsOwnAttributesStringLike() {

        $device1 = factory(\WA\DataStore\Device\Device::class)->create(['name' => 'name1']);
        $device2 = factory(\WA\DataStore\Device\Device::class)->create(['name' => 'Name1']);
        $device3 = factory(\WA\DataStore\Device\Device::class)->create(['name' => 'name122']);
        $device4 = factory(\WA\DataStore\Device\Device::class)->create(['name' => 'name2']);
        $device5 = factory(\WA\DataStore\Device\Device::class)->create(['name' => 'Name5']);
        $device6 = factory(\WA\DataStore\Device\Device::class)->create(['name' => 'Niame16']);
        $device7 = factory(\WA\DataStore\Device\Device::class)->create(['name' => 'niame167']);

        $devVar1 = factory(\WA\DataStore\DeviceVariation\DeviceVariation::class)->create(['companyId' => $this->mainCompany->id, 'deviceId' => $device1->id]);
        $devVar2 = factory(\WA\DataStore\DeviceVariation\DeviceVariation::class)->create(['companyId' => $this->mainCompany->id, 'deviceId' => $device2->id]);
        $devVar3 = factory(\WA\DataStore\DeviceVariation\DeviceVariation::class)->create(['companyId' => $this->mainCompany->id, 'deviceId' => $device3->id]);

        if ($this->idLogged == 1) {
            $filter = [
                '[name][like]=*name1*'
            ];
        } else {
            $filter = [
                '[name][like]=*name1*',
                '[devicevariations.companyId]=1'
            ];
        }

        $resArray = (array)json_decode($this->json('GET', '/devices?filter[name][like]=*name1*')->response->getContent());

        $this->assertCount(3, $resArray['data']);
        $this->assertEquals($resArray['data'][0]->id, $device1->id);
        $this->assertEquals($resArray['data'][1]->id, $device2->id);
        $this->assertEquals($resArray['data'][2]->id, $device3->id);
        $this->assertEquals($resArray['meta']->filter, $filter);
    }

    public function testFilterModelByItsOwnAttributesStringNotEqual() {

        $device1 = factory(\WA\DataStore\Device\Device::class)->create(['name' => 'name1']);
        $device2 = factory(\WA\DataStore\Device\Device::class)->create(['name' => 'Name1']);
        $device3 = factory(\WA\DataStore\Device\Device::class)->create(['name' => 'name122']);

        $devVar1 = factory(\WA\DataStore\DeviceVariation\DeviceVariation::class)->create(['companyId' => $this->mainCompany->id, 'deviceId' => $device1->id]);
        $devVar2 = factory(\WA\DataStore\DeviceVariation\DeviceVariation::class)->create(['companyId' => $this->mainCompany->id, 'deviceId' => $device2->id]);
        $devVar3 = factory(\WA\DataStore\DeviceVariation\DeviceVariation::class)->create(['companyId' => $this->mainCompany->id, 'deviceId' => $device3->id]);

        if ($this->idLogged == 1) {
            $filter = [
                '[name][ne]=name1'
            ];
        } else {
            $filter = [
                '[name][ne]=name1',
                '[devicevariations.companyId]=1'
            ];
        }

        $resArray = (array)json_decode($this->json('GET', '/devices?filter[name][ne]=name1')->response->getContent());

        $this->assertCount(2, $resArray['data']);
        $this->assertEquals($resArray['data'][0]->id, $device2->id);
        $this->assertEquals($resArray['data'][1]->id, $device3->id);
        $this->assertEquals($resArray['meta']->filter, $filter);
    }

    public function testFilterModelByItsOwnAttributesStringDefaultMultiple() {

        $device1 = factory(\WA\DataStore\Device\Device::class)->create(['name' => 'name1']);
        $device2 = factory(\WA\DataStore\Device\Device::class)->create(['name' => 'Name1']);
        $device3 = factory(\WA\DataStore\Device\Device::class)->create(['name' => 'name122']);

        $devVar1 = factory(\WA\DataStore\DeviceVariation\DeviceVariation::class)->create(['companyId' => $this->mainCompany->id, 'deviceId' => $device1->id]);
        $devVar2 = factory(\WA\DataStore\DeviceVariation\DeviceVariation::class)->create(['companyId' => $this->mainCompany->id, 'deviceId' => $device2->id]);
        $devVar3 = factory(\WA\DataStore\DeviceVariation\DeviceVariation::class)->create(['companyId' => $this->mainCompany->id, 'deviceId' => $device3->id]);

        if ($this->idLogged == 1) {
            $filter = [
                '[name]=name1,name122'
            ];
        } else {
            $filter = [
                '[name]=name1,name122',
                '[devicevariations.companyId]=1'
            ];
        }

        $resArray = (array)json_decode($this->json('GET', '/devices?filter[name]=name1,name122')->response->getContent());

        $this->assertCount(2, $resArray['data']);
        $this->assertEquals($resArray['data'][0]->id, $device1->id);
        $this->assertEquals($resArray['data'][1]->id, $device3->id);
        $this->assertEquals($resArray['meta']->filter, $filter);
    }

    public function testFilterModelByItsOwnAttributesStringEqualMultiple() {

        $device1 = factory(\WA\DataStore\Device\Device::class)->create(['name' => 'name1']);
        $device2 = factory(\WA\DataStore\Device\Device::class)->create(['name' => 'Name1']);
        $device3 = factory(\WA\DataStore\Device\Device::class)->create(['name' => 'name122']);

        $devVar1 = factory(\WA\DataStore\DeviceVariation\DeviceVariation::class)->create(['companyId' => $this->mainCompany->id, 'deviceId' => $device1->id]);
        $devVar2 = factory(\WA\DataStore\DeviceVariation\DeviceVariation::class)->create(['companyId' => $this->mainCompany->id, 'deviceId' => $device2->id]);
        $devVar3 = factory(\WA\DataStore\DeviceVariation\DeviceVariation::class)->create(['companyId' => $this->mainCompany->id, 'deviceId' => $device3->id]);

        if ($this->idLogged == 1) {
            $filter = [
                '[name]=name1,name122'
            ];
        } else {
            $filter = [
                '[name]=name1,name122',
                '[devicevariations.companyId]=1'
            ];
        }

        $resArray = (array)json_decode($this->json('GET', '/devices?filter[name][eq]=name1,name122')->response->getContent());

        $this->assertCount(2, $resArray['data']);
        $this->assertEquals($resArray['data'][0]->id, $device1->id);
        $this->assertEquals($resArray['data'][1]->id, $device3->id);
        $this->assertEquals($resArray['meta']->filter, $filter);
    }

    public function testFilterModelByItsOwnAttributesStringLikeMultiple() {

        $device1 = factory(\WA\DataStore\Device\Device::class)->create(['name' => 'name1']);
        $device2 = factory(\WA\DataStore\Device\Device::class)->create(['name' => 'Name1']);
        $device3 = factory(\WA\DataStore\Device\Device::class)->create(['name' => 'name122']);
        $device4 = factory(\WA\DataStore\Device\Device::class)->create(['name' => 'name2']);
        $device5 = factory(\WA\DataStore\Device\Device::class)->create(['name' => 'Name5']);
        $device6 = factory(\WA\DataStore\Device\Device::class)->create(['name' => 'Niame16']);
        $device7 = factory(\WA\DataStore\Device\Device::class)->create(['name' => 'niame167']);

        $devVar1 = factory(\WA\DataStore\DeviceVariation\DeviceVariation::class)->create(['companyId' => $this->mainCompany->id, 'deviceId' => $device1->id]);
        $devVar2 = factory(\WA\DataStore\DeviceVariation\DeviceVariation::class)->create(['companyId' => $this->mainCompany->id, 'deviceId' => $device2->id]);
        $devVar3 = factory(\WA\DataStore\DeviceVariation\DeviceVariation::class)->create(['companyId' => $this->mainCompany->id, 'deviceId' => $device3->id]);
        $devVar4 = factory(\WA\DataStore\DeviceVariation\DeviceVariation::class)->create(['companyId' => $this->mainCompany->id, 'deviceId' => $device4->id]);
        $devVar5 = factory(\WA\DataStore\DeviceVariation\DeviceVariation::class)->create(['companyId' => $this->mainCompany->id, 'deviceId' => $device5->id]);
        $devVar6 = factory(\WA\DataStore\DeviceVariation\DeviceVariation::class)->create(['companyId' => $this->mainCompany->id, 'deviceId' => $device6->id]);
        $devVar7 = factory(\WA\DataStore\DeviceVariation\DeviceVariation::class)->create(['companyId' => $this->mainCompany->id, 'deviceId' => $device7->id]);

        if ($this->idLogged == 1) {
            $filter = [
                '[name][like]=*name1*,*nia*'
            ];
        } else {
            $filter = [
                '[name][like]=*name1*,*nia*',
                '[devicevariations.companyId]=1'
            ];
        }

        $resArray = (array)json_decode($this->json('GET', '/devices?filter[name][like]=*name1*,*nia*')->response->getContent());

        $this->assertCount(5, $resArray['data']);
        $this->assertEquals($resArray['data'][0]->id, $device1->id);
        $this->assertEquals($resArray['data'][1]->id, $device2->id);
        $this->assertEquals($resArray['data'][2]->id, $device3->id);
        $this->assertEquals($resArray['data'][3]->id, $device6->id);
        $this->assertEquals($resArray['data'][4]->id, $device7->id);
        $this->assertEquals($resArray['meta']->filter, $filter);
    }

    public function testFilterModelByItsOwnAttributesStringLikeMultipleOR() {

        $device1 = factory(\WA\DataStore\Device\Device::class)->create(['name' => 'name1']);
        $device2 = factory(\WA\DataStore\Device\Device::class)->create(['name' => 'Name1']);
        $device3 = factory(\WA\DataStore\Device\Device::class)->create(['name' => 'name122']);
        $device4 = factory(\WA\DataStore\Device\Device::class)->create(['name' => 'name2']);
        $device5 = factory(\WA\DataStore\Device\Device::class)->create(['name' => 'Name5']);
        $device6 = factory(\WA\DataStore\Device\Device::class)->create(['name' => 'Niame16']);
        $device7 = factory(\WA\DataStore\Device\Device::class)->create(['name' => 'niame167']);

        $devVar1 = factory(\WA\DataStore\DeviceVariation\DeviceVariation::class)->create(['companyId' => $this->mainCompany->id, 'deviceId' => $device1->id]);
        $devVar2 = factory(\WA\DataStore\DeviceVariation\DeviceVariation::class)->create(['companyId' => $this->mainCompany->id, 'deviceId' => $device2->id]);
        $devVar3 = factory(\WA\DataStore\DeviceVariation\DeviceVariation::class)->create(['companyId' => $this->mainCompany->id, 'deviceId' => $device3->id]);
        $devVar4 = factory(\WA\DataStore\DeviceVariation\DeviceVariation::class)->create(['companyId' => $this->mainCompany->id, 'deviceId' => $device4->id]);
        $devVar5 = factory(\WA\DataStore\DeviceVariation\DeviceVariation::class)->create(['companyId' => $this->mainCompany->id, 'deviceId' => $device5->id]);
        $devVar6 = factory(\WA\DataStore\DeviceVariation\DeviceVariation::class)->create(['companyId' => $this->mainCompany->id, 'deviceId' => $device6->id]);
        $devVar7 = factory(\WA\DataStore\DeviceVariation\DeviceVariation::class)->create(['companyId' => $this->mainCompany->id, 'deviceId' => $device7->id]);

        if ($this->idLogged == 1) {
            $filter = [
                '[0]=[name][like]=*name1*[or][name][like]=*nia*'
            ];
        } else {
            $filter = [
                '[0]=[name][like]=*name1*[or][name][like]=*nia*',
                '[devicevariations.companyId]=1'
            ];
        }

        $resArray = (array)json_decode($this->json('GET', '/devices?filter[]=[name][like]=*name1*[or][name][like]=*nia*')->response->getContent());

        $this->assertCount(5, $resArray['data']);
        $this->assertEquals($resArray['data'][0]->id, $device1->id);
        $this->assertEquals($resArray['data'][1]->id, $device2->id);
        $this->assertEquals($resArray['data'][2]->id, $device3->id);
        $this->assertEquals($resArray['data'][3]->id, $device6->id);
        $this->assertEquals($resArray['data'][4]->id, $device7->id);
        $this->assertEquals($resArray['meta']->filter, $filter);
    }

    public function testFilterModelByItsOwnAttributesStringNotEqualMultiple() {

        $device1 = factory(\WA\DataStore\Device\Device::class)->create(['name' => 'name1']);
        $device2 = factory(\WA\DataStore\Device\Device::class)->create(['name' => 'Name1']);
        $device3 = factory(\WA\DataStore\Device\Device::class)->create(['name' => 'name122']);

        $devVar1 = factory(\WA\DataStore\DeviceVariation\DeviceVariation::class)->create(['companyId' => $this->mainCompany->id, 'deviceId' => $device1->id]);
        $devVar2 = factory(\WA\DataStore\DeviceVariation\DeviceVariation::class)->create(['companyId' => $this->mainCompany->id, 'deviceId' => $device2->id]);
        $devVar3 = factory(\WA\DataStore\DeviceVariation\DeviceVariation::class)->create(['companyId' => $this->mainCompany->id, 'deviceId' => $device3->id]);

        if ($this->idLogged == 1) {
            $filter = [
                '[name][ne]=name1,Name1'
            ];
        } else {
            $filter = [
                '[name][ne]=name1,Name1',
                '[devicevariations.companyId]=1'
            ];
        }

        $resArray = (array)json_decode($this->json('GET', '/devices?filter[name][ne]=name1,Name1')->response->getContent());

        $this->assertCount(1, $resArray['data']);
        $this->assertEquals($resArray['data'][0]->id, $device3->id);
        $this->assertEquals($resArray['meta']->filter, $filter);
    }

    public function testFilterModelByItsOwnAttributesNumberDefault() {

        $device1 = factory(\WA\DataStore\Device\Device::class)->create(['name' => '1']);
        $device2 = factory(\WA\DataStore\Device\Device::class)->create(['name' => '2']);
        $device3 = factory(\WA\DataStore\Device\Device::class)->create(['name' => '3']);

        $devVar1 = factory(\WA\DataStore\DeviceVariation\DeviceVariation::class)->create(['companyId' => $this->mainCompany->id, 'deviceId' => $device1->id]);
        $devVar2 = factory(\WA\DataStore\DeviceVariation\DeviceVariation::class)->create(['companyId' => $this->mainCompany->id, 'deviceId' => $device2->id]);
        $devVar3 = factory(\WA\DataStore\DeviceVariation\DeviceVariation::class)->create(['companyId' => $this->mainCompany->id, 'deviceId' => $device3->id]);

        if ($this->idLogged == 1) {
            $filter = [
                '[name]=1'
            ];
        } else {
            $filter = [
                '[name]=1',
                '[devicevariations.companyId]=1'
            ];
        }

        $resArray = (array)json_decode($this->json('GET', '/devices?filter[name]=1')->response->getContent());

        $this->assertCount(1, $resArray['data']);
        $this->assertEquals($resArray['data'][0]->id, $device1->id);
        $this->assertEquals($resArray['meta']->filter, $filter);
    }

    public function testFilterModelByItsOwnAttributesNumberEqual() {

        $device1 = factory(\WA\DataStore\Device\Device::class)->create(['name' => '1']);
        $device2 = factory(\WA\DataStore\Device\Device::class)->create(['name' => '2']);
        $device3 = factory(\WA\DataStore\Device\Device::class)->create(['name' => '3']);

        $devVar1 = factory(\WA\DataStore\DeviceVariation\DeviceVariation::class)->create(['companyId' => $this->mainCompany->id, 'deviceId' => $device1->id]);
        $devVar2 = factory(\WA\DataStore\DeviceVariation\DeviceVariation::class)->create(['companyId' => $this->mainCompany->id, 'deviceId' => $device2->id]);
        $devVar3 = factory(\WA\DataStore\DeviceVariation\DeviceVariation::class)->create(['companyId' => $this->mainCompany->id, 'deviceId' => $device3->id]);

        if ($this->idLogged == 1) {
            $filter = [
                '[name]=1'
            ];
        } else {
            $filter = [
                '[name]=1',
                '[devicevariations.companyId]=1'
            ];
        }

        $resArray = (array)json_decode($this->json('GET', '/devices?filter[name][eq]=1')->response->getContent());

        $this->assertCount(1, $resArray['data']);
        $this->assertEquals($resArray['data'][0]->id, $device1->id);
        $this->assertEquals($resArray['meta']->filter, $filter);
    }

    public function testFilterModelByItsOwnAttributesNumberLike() {

        $device1 = factory(\WA\DataStore\Device\Device::class)->create(['name' => '1']);
        $device2 = factory(\WA\DataStore\Device\Device::class)->create(['name' => '12']);
        $device3 = factory(\WA\DataStore\Device\Device::class)->create(['name' => '3']);

        $devVar1 = factory(\WA\DataStore\DeviceVariation\DeviceVariation::class)->create(['companyId' => $this->mainCompany->id, 'deviceId' => $device1->id]);
        $devVar2 = factory(\WA\DataStore\DeviceVariation\DeviceVariation::class)->create(['companyId' => $this->mainCompany->id, 'deviceId' => $device2->id]);
        $devVar3 = factory(\WA\DataStore\DeviceVariation\DeviceVariation::class)->create(['companyId' => $this->mainCompany->id, 'deviceId' => $device3->id]);

        if ($this->idLogged == 1) {
            $filter = [
                '[name][like]=*1*'
            ];
        } else {
            $filter = [
                '[name][like]=*1*',
                '[devicevariations.companyId]=1'
            ];
        }

        $resArray = (array)json_decode($this->json('GET', '/devices?filter[name][like]=*1*')->response->getContent());

        $this->assertCount(2, $resArray['data']);
        $this->assertEquals($resArray['data'][0]->id, $device1->id);
        $this->assertEquals($resArray['data'][1]->id, $device2->id);
        $this->assertEquals($resArray['meta']->filter, $filter);
    }

    public function testFilterModelByItsOwnAttributesNumberNotEqual() {

        $device1 = factory(\WA\DataStore\Device\Device::class)->create(['name' => '1']);
        $device2 = factory(\WA\DataStore\Device\Device::class)->create(['name' => '2']);
        $device3 = factory(\WA\DataStore\Device\Device::class)->create(['name' => '3']);

        $devVar1 = factory(\WA\DataStore\DeviceVariation\DeviceVariation::class)->create(['companyId' => $this->mainCompany->id, 'deviceId' => $device1->id]);
        $devVar2 = factory(\WA\DataStore\DeviceVariation\DeviceVariation::class)->create(['companyId' => $this->mainCompany->id, 'deviceId' => $device2->id]);
        $devVar3 = factory(\WA\DataStore\DeviceVariation\DeviceVariation::class)->create(['companyId' => $this->mainCompany->id, 'deviceId' => $device3->id]);

        if ($this->idLogged == 1) {
            $filter = [
                '[name][ne]=1'
            ];
        } else {
            $filter = [
                '[name][ne]=1',
                '[devicevariations.companyId]=1'
            ];
        }

        $resArray = (array)json_decode($this->json('GET', '/devices?filter[name][ne]=1')->response->getContent());

        $this->assertCount(2, $resArray['data']);
        $this->assertEquals($resArray['data'][0]->id, $device2->id);
        $this->assertEquals($resArray['data'][1]->id, $device3->id);
        $this->assertEquals($resArray['meta']->filter, $filter);
    }

//'gt', 'lt', 'ge', 'gte','lte','le','ne','eq','like':

    public function testFilterModelByItsOwnAttributesNumberGreaterThan() {

        $device1 = factory(\WA\DataStore\Device\Device::class)->create(['name' => '1']);
        $device2 = factory(\WA\DataStore\Device\Device::class)->create(['name' => '2']);
        $device3 = factory(\WA\DataStore\Device\Device::class)->create(['name' => '3']);

        $devVar1 = factory(\WA\DataStore\DeviceVariation\DeviceVariation::class)->create(['companyId' => $this->mainCompany->id, 'deviceId' => $device1->id]);
        $devVar2 = factory(\WA\DataStore\DeviceVariation\DeviceVariation::class)->create(['companyId' => $this->mainCompany->id, 'deviceId' => $device2->id]);
        $devVar3 = factory(\WA\DataStore\DeviceVariation\DeviceVariation::class)->create(['companyId' => $this->mainCompany->id, 'deviceId' => $device3->id]);

        if ($this->idLogged == 1) {
            $filter = [
                '[name][gt]=1'
            ];
        } else {
            $filter = [
                '[name][gt]=1',
                '[devicevariations.companyId]=1'
            ];
        }

        $resArray = (array)json_decode($this->json('GET', '/devices?filter[name][gt]=1')->response->getContent());

        $this->assertCount(2, $resArray['data']);
        $this->assertEquals($resArray['data'][0]->id, $device2->id);
        $this->assertEquals($resArray['data'][1]->id, $device3->id);
        $this->assertEquals($resArray['meta']->filter, $filter);
    }

    public function testFilterModelByItsOwnAttributesNumberLessThan() {

        $device1 = factory(\WA\DataStore\Device\Device::class)->create(['name' => '1']);
        $device2 = factory(\WA\DataStore\Device\Device::class)->create(['name' => '2']);
        $device3 = factory(\WA\DataStore\Device\Device::class)->create(['name' => '3']);

        $devVar1 = factory(\WA\DataStore\DeviceVariation\DeviceVariation::class)->create(['companyId' => $this->mainCompany->id, 'deviceId' => $device1->id]);
        $devVar2 = factory(\WA\DataStore\DeviceVariation\DeviceVariation::class)->create(['companyId' => $this->mainCompany->id, 'deviceId' => $device2->id]);
        $devVar3 = factory(\WA\DataStore\DeviceVariation\DeviceVariation::class)->create(['companyId' => $this->mainCompany->id, 'deviceId' => $device3->id]);

        if ($this->idLogged == 1) {
            $filter = [
                '[name][lt]=1'
            ];
        } else {
            $filter = [
                '[name][lt]=1',
                '[devicevariations.companyId]=1'
            ];
        }

        $resArray = (array)json_decode($this->json('GET', '/devices?filter[name][lt]=1')->response->getContent());

        $this->assertCount(0, $resArray['data']);
        $this->assertEquals($resArray['meta']->filter, $filter);
    }

    public function testFilterModelByItsOwnAttributesNumberGreaterThanEqual() {

        $device1 = factory(\WA\DataStore\Device\Device::class)->create(['name' => '1']);
        $device2 = factory(\WA\DataStore\Device\Device::class)->create(['name' => '2']);
        $device3 = factory(\WA\DataStore\Device\Device::class)->create(['name' => '3']);

        $devVar1 = factory(\WA\DataStore\DeviceVariation\DeviceVariation::class)->create(['companyId' => $this->mainCompany->id, 'deviceId' => $device1->id]);
        $devVar2 = factory(\WA\DataStore\DeviceVariation\DeviceVariation::class)->create(['companyId' => $this->mainCompany->id, 'deviceId' => $device2->id]);
        $devVar3 = factory(\WA\DataStore\DeviceVariation\DeviceVariation::class)->create(['companyId' => $this->mainCompany->id, 'deviceId' => $device3->id]);

        if ($this->idLogged == 1) {
            $filter = [
                '[name][gte]=1'
            ];
        } else {
            $filter = [
                '[name][gte]=1',
                '[devicevariations.companyId]=1'
            ];
        }

        $resArray = (array)json_decode($this->json('GET', '/devices?filter[name][gte]=1')->response->getContent());

        $this->assertCount(3, $resArray['data']);
        $this->assertEquals($resArray['data'][0]->id, $device1->id);
        $this->assertEquals($resArray['data'][1]->id, $device2->id);
        $this->assertEquals($resArray['data'][2]->id, $device3->id);
        $this->assertEquals($resArray['meta']->filter, $filter);
    }

    public function testFilterModelByItsOwnAttributesNumberGreaterThanEqual2() {

        $device1 = factory(\WA\DataStore\Device\Device::class)->create(['name' => '1']);
        $device2 = factory(\WA\DataStore\Device\Device::class)->create(['name' => '2']);
        $device3 = factory(\WA\DataStore\Device\Device::class)->create(['name' => '3']);

        $devVar1 = factory(\WA\DataStore\DeviceVariation\DeviceVariation::class)->create(['companyId' => $this->mainCompany->id, 'deviceId' => $device1->id]);
        $devVar2 = factory(\WA\DataStore\DeviceVariation\DeviceVariation::class)->create(['companyId' => $this->mainCompany->id, 'deviceId' => $device2->id]);
        $devVar3 = factory(\WA\DataStore\DeviceVariation\DeviceVariation::class)->create(['companyId' => $this->mainCompany->id, 'deviceId' => $device3->id]);

        if ($this->idLogged == 1) {
            $filter = [
                '[name][ge]=1'
            ];
        } else {
            $filter = [
                '[name][ge]=1',
                '[devicevariations.companyId]=1'
            ];
        }

        $resArray = (array)json_decode($this->json('GET', '/devices?filter[name][ge]=1')->response->getContent());

        $this->assertCount(3, $resArray['data']);
        $this->assertEquals($resArray['data'][0]->id, $device1->id);
        $this->assertEquals($resArray['data'][1]->id, $device2->id);
        $this->assertEquals($resArray['data'][2]->id, $device3->id);
        $this->assertEquals($resArray['meta']->filter, $filter);
    }

    public function testFilterModelByItsOwnAttributesNumberLessThanEqual() {

        $device1 = factory(\WA\DataStore\Device\Device::class)->create(['name' => '1']);
        $device2 = factory(\WA\DataStore\Device\Device::class)->create(['name' => '2']);
        $device3 = factory(\WA\DataStore\Device\Device::class)->create(['name' => '3']);

        $devVar1 = factory(\WA\DataStore\DeviceVariation\DeviceVariation::class)->create(['companyId' => $this->mainCompany->id, 'deviceId' => $device1->id]);
        $devVar2 = factory(\WA\DataStore\DeviceVariation\DeviceVariation::class)->create(['companyId' => $this->mainCompany->id, 'deviceId' => $device2->id]);
        $devVar3 = factory(\WA\DataStore\DeviceVariation\DeviceVariation::class)->create(['companyId' => $this->mainCompany->id, 'deviceId' => $device3->id]);

        if ($this->idLogged == 1) {
            $filter = [
                '[name][lte]=1'
            ];
        } else {
            $filter = [
                '[name][lte]=1',
                '[devicevariations.companyId]=1'
            ];
        }

        $resArray = (array)json_decode($this->json('GET', '/devices?filter[name][lte]=1')->response->getContent());

        $this->assertCount(1, $resArray['data']);
        $this->assertEquals($resArray['data'][0]->id, $device1->id);
        $this->assertEquals($resArray['meta']->filter, $filter);
    }

    public function testFilterModelByItsOwnAttributesNumberLessThanEqual2() {

        $device1 = factory(\WA\DataStore\Device\Device::class)->create(['name' => '1']);
        $device2 = factory(\WA\DataStore\Device\Device::class)->create(['name' => '2']);
        $device3 = factory(\WA\DataStore\Device\Device::class)->create(['name' => '3']);

        $devVar1 = factory(\WA\DataStore\DeviceVariation\DeviceVariation::class)->create(['companyId' => $this->mainCompany->id, 'deviceId' => $device1->id]);
        $devVar2 = factory(\WA\DataStore\DeviceVariation\DeviceVariation::class)->create(['companyId' => $this->mainCompany->id, 'deviceId' => $device2->id]);
        $devVar3 = factory(\WA\DataStore\DeviceVariation\DeviceVariation::class)->create(['companyId' => $this->mainCompany->id, 'deviceId' => $device3->id]);

        if ($this->idLogged == 1) {
            $filter = [
                '[name][le]=1'
            ];
        } else {
            $filter = [
                '[name][le]=1',
                '[devicevariations.companyId]=1'
            ];
        }

        $resArray = (array)json_decode($this->json('GET', '/devices?filter[name][le]=1')->response->getContent());

        $this->assertCount(1, $resArray['data']);
        $this->assertEquals($resArray['data'][0]->id, $device1->id);
        $this->assertEquals($resArray['meta']->filter, $filter);
    }

    public function testFilterModelByItsOwnAttributesNumberMultipleLEGE() {

        $device1 = factory(\WA\DataStore\Device\Device::class)->create(['name' => '1']);
        $device2 = factory(\WA\DataStore\Device\Device::class)->create(['name' => '2']);
        $device3 = factory(\WA\DataStore\Device\Device::class)->create(['name' => '3']);

        $devVar1 = factory(\WA\DataStore\DeviceVariation\DeviceVariation::class)->create(['companyId' => $this->mainCompany->id, 'deviceId' => $device1->id]);
        $devVar2 = factory(\WA\DataStore\DeviceVariation\DeviceVariation::class)->create(['companyId' => $this->mainCompany->id, 'deviceId' => $device2->id]);
        $devVar3 = factory(\WA\DataStore\DeviceVariation\DeviceVariation::class)->create(['companyId' => $this->mainCompany->id, 'deviceId' => $device3->id]);

        if ($this->idLogged == 1) {
            $filter = [
                '[0]=[name][le]=1[or][name][ge]=3'
            ];
        } else {
            $filter = [
                '[0]=[name][le]=1[or][name][ge]=3',
                '[devicevariations.companyId]=1'
            ];
        }

        $resArray = (array)json_decode($this->json('GET', '/devices?filter[]=[name][le]=1[or][name][ge]=3')->response->getContent());

        $this->assertCount(2, $resArray['data']);
        $this->assertEquals($resArray['data'][0]->id, $device1->id);
        $this->assertEquals($resArray['data'][1]->id, $device3->id);
        $this->assertEquals($resArray['meta']->filter, $filter);
    }

    public function testFilterModelByItsOwnAttributesNumberMultipleLTGT() {

        $device1 = factory(\WA\DataStore\Device\Device::class)->create(['name' => '1']);
        $device2 = factory(\WA\DataStore\Device\Device::class)->create(['name' => '2']);
        $device3 = factory(\WA\DataStore\Device\Device::class)->create(['name' => '3']);

        $devVar1 = factory(\WA\DataStore\DeviceVariation\DeviceVariation::class)->create(['companyId' => $this->mainCompany->id, 'deviceId' => $device1->id]);
        $devVar2 = factory(\WA\DataStore\DeviceVariation\DeviceVariation::class)->create(['companyId' => $this->mainCompany->id, 'deviceId' => $device2->id]);
        $devVar3 = factory(\WA\DataStore\DeviceVariation\DeviceVariation::class)->create(['companyId' => $this->mainCompany->id, 'deviceId' => $device3->id]);

        if ($this->idLogged == 1) {
            $filter = [
                '[0]=[name][lt]=1[or][name][gt]=3'
            ];
        } else {
            $filter = [
                '[0]=[name][lt]=1[or][name][gt]=3',
                '[devicevariations.companyId]=1'
            ];
        }

        $resArray = (array)json_decode($this->json('GET', '/devices?filter[]=[name][lt]=1[or][name][gt]=3')->response->getContent());

        $this->assertCount(0, $resArray['data']);
        $this->assertEquals($resArray['meta']->filter, $filter);
    }

    public function testFilterModelByItsIncludes() {

        $device1 = factory(\WA\DataStore\Device\Device::class)->create(['name' => 'device1']);
        $device2 = factory(\WA\DataStore\Device\Device::class)->create(['name' => 'device2']);
        $device3 = factory(\WA\DataStore\Device\Device::class)->create(['name' => 'device3']);

        $carrier1 = factory(\WA\DataStore\Carrier\Carrier::class)->create(['name' => 'carrier1']);
        $carrier2 = factory(\WA\DataStore\Carrier\Carrier::class)->create(['name' => 'carrier2']);

        $devvar1 = factory(\WA\DataStore\DeviceVariation\DeviceVariation::class)->create([
            'carrierId' => $carrier1->id,
            'deviceId' => $device1->id,
            'priceRetail' => 100
        ]);
        $devvar2 = factory(\WA\DataStore\DeviceVariation\DeviceVariation::class)->create([
            'carrierId' => $carrier1->id,
            'deviceId' => $device1->id,
            'priceRetail' => 200
        ]);
        $devvar3 = factory(\WA\DataStore\DeviceVariation\DeviceVariation::class)->create([
            'carrierId' => $carrier1->id,
            'deviceId' => $device2->id,
            'priceRetail' => 300
        ]);
        $devvar4 = factory(\WA\DataStore\DeviceVariation\DeviceVariation::class)->create([
            'carrierId' => $carrier1->id,
            'deviceId' => $device2->id,
            'priceRetail' => 400
        ]);
        $devvar5 = factory(\WA\DataStore\DeviceVariation\DeviceVariation::class)->create([
            'carrierId' => $carrier2->id,
            'deviceId' => $device2->id,
            'priceRetail' => 500
        ]);
        $devvar6 = factory(\WA\DataStore\DeviceVariation\DeviceVariation::class)->create([
            'carrierId' => $carrier2->id,
            'deviceId' => $device2->id,
            'priceRetail' => 600
        ]);
        $devvar7 = factory(\WA\DataStore\DeviceVariation\DeviceVariation::class)->create([
            'carrierId' => $carrier2->id,
            'deviceId' => $device3->id,
            'priceRetail' => 700
        ]);
        $devvar8 = factory(\WA\DataStore\DeviceVariation\DeviceVariation::class)->create([
            'carrierId' => $carrier2->id,
            'deviceId' => $device3->id,
            'priceRetail' => 800
        ]);

        if ($this->idLogged == 1) {
            $filter = [
                '[devicevariations.carrierId]=1'
            ];
        } else {
            $filter = [
                '[devicevariations.carrierId]=1',
                '[devicevariations.companyId]=1'
            ];
        }

        $resArray = (array)json_decode($this->json('GET', '/devices?include=devicevariations,devicevariations.carriers&filter[devicevariations.carrierId]=' . $carrier1->id)->response->getContent());

        $this->assertCount(2, $resArray['data']);
        $this->assertEquals($resArray['data'][0]->id, $device1->id);
        $this->assertEquals($resArray['data'][1]->id, $device2->id);
        $this->assertEquals($resArray['meta']->filter, $filter);
        $this->assertCount(8, $resArray['included']);
    }

    public function testFilterModelByItsIncludesOR() {

        $device1 = factory(\WA\DataStore\Device\Device::class)->create(['name' => 'device1']);
        $device2 = factory(\WA\DataStore\Device\Device::class)->create(['name' => 'device2']);
        $device3 = factory(\WA\DataStore\Device\Device::class)->create(['name' => 'device3']);
        $device4 = factory(\WA\DataStore\Device\Device::class)->create(['name' => 'device4']);
        $device5 = factory(\WA\DataStore\Device\Device::class)->create(['name' => 'device5']);

        $carrier1 = factory(\WA\DataStore\Carrier\Carrier::class)->create(['name' => 'carrier1']);
        $carrier2 = factory(\WA\DataStore\Carrier\Carrier::class)->create(['name' => 'carrier2']);
        $carrier3 = factory(\WA\DataStore\Carrier\Carrier::class)->create(['name' => 'carrier3']);

        $devvar1 = factory(\WA\DataStore\DeviceVariation\DeviceVariation::class)->create([
            'carrierId' => $carrier1->id,
            'deviceId' => $device1->id,
            'priceRetail' => 100
        ]);
        $devvar2 = factory(\WA\DataStore\DeviceVariation\DeviceVariation::class)->create([
            'carrierId' => $carrier1->id,
            'deviceId' => $device2->id,
            'priceRetail' => 200
        ]);
        $devvar3 = factory(\WA\DataStore\DeviceVariation\DeviceVariation::class)->create([
            'carrierId' => $carrier2->id,
            'deviceId' => $device3->id,
            'priceRetail' => 300
        ]);
        $devvar4 = factory(\WA\DataStore\DeviceVariation\DeviceVariation::class)->create([
            'carrierId' => $carrier2->id,
            'deviceId' => $device4->id,
            'priceRetail' => 400
        ]);
        $devvar5 = factory(\WA\DataStore\DeviceVariation\DeviceVariation::class)->create([
            'carrierId' => $carrier3->id,
            'deviceId' => $device4->id,
            'priceRetail' => 400
        ]);
        $devvar6 = factory(\WA\DataStore\DeviceVariation\DeviceVariation::class)->create([
            'carrierId' => $carrier3->id,
            'deviceId' => $device5->id,
            'priceRetail' => 500
        ]);

        if ($this->idLogged == 1) {
            $filter = [
                '[0]=[devicevariations.carrierId]=1[or][devicevariations.carrierId]=3'
            ];
        } else {
            $filter = [
                '[0]=[devicevariations.carrierId]=1[or][devicevariations.carrierId]=3',
                '[devicevariations.companyId]=1'
            ];
        }

        $resArray = (array)json_decode($this->json('GET', '/devices?include=devicevariations,devicevariations.carriers&filter[]=[devicevariations.carrierId]=' . $carrier1->id . '[or][devicevariations.carrierId]=' . $carrier3->id)->response->getContent());

        $this->assertCount(4, $resArray['data']);
        $this->assertEquals($resArray['data'][0]->id, $device1->id);
        $this->assertEquals($resArray['data'][1]->id, $device2->id);
        $this->assertEquals($resArray['data'][2]->id, $device4->id);
        $this->assertEquals($resArray['data'][3]->id, $device5->id);
        $this->assertEquals($resArray['meta']->filter, $filter);
        $this->assertCount(8, $resArray['included']);
    }

    public function testFilterModelByItsIncludesORMoreThanTwo() {

        $device1 = factory(\WA\DataStore\Device\Device::class)->create(['name' => 'device1']);
        $device2 = factory(\WA\DataStore\Device\Device::class)->create(['name' => 'device2']);
        $device3 = factory(\WA\DataStore\Device\Device::class)->create(['name' => 'device3']);
        $device4 = factory(\WA\DataStore\Device\Device::class)->create(['name' => 'device4']);
        $device5 = factory(\WA\DataStore\Device\Device::class)->create(['name' => 'device5']);

        $carrier1 = factory(\WA\DataStore\Carrier\Carrier::class)->create(['name' => 'carrier1']);
        $carrier2 = factory(\WA\DataStore\Carrier\Carrier::class)->create(['name' => 'carrier2']);
        $carrier3 = factory(\WA\DataStore\Carrier\Carrier::class)->create(['name' => 'carrier3']);
        $carrier4 = factory(\WA\DataStore\Carrier\Carrier::class)->create(['name' => 'carrier4']);
        $carrier5 = factory(\WA\DataStore\Carrier\Carrier::class)->create(['name' => 'carrier5']);

        $devvar1 = factory(\WA\DataStore\DeviceVariation\DeviceVariation::class)->create([
            'carrierId' => $carrier1->id,
            'deviceId' => $device1->id,
            'priceRetail' => 100
        ]);
        $devvar2 = factory(\WA\DataStore\DeviceVariation\DeviceVariation::class)->create([
            'carrierId' => $carrier2->id,
            'deviceId' => $device2->id,
            'priceRetail' => 200
        ]);
        $devvar3 = factory(\WA\DataStore\DeviceVariation\DeviceVariation::class)->create([
            'carrierId' => $carrier3->id,
            'deviceId' => $device3->id,
            'priceRetail' => 300
        ]);
        $devvar4 = factory(\WA\DataStore\DeviceVariation\DeviceVariation::class)->create([
            'carrierId' => $carrier4->id,
            'deviceId' => $device4->id,
            'priceRetail' => 400
        ]);
        $devvar5 = factory(\WA\DataStore\DeviceVariation\DeviceVariation::class)->create([
            'carrierId' => $carrier5->id,
            'deviceId' => $device5->id,
            'priceRetail' => 500
        ]);

        if ($this->idLogged == 1) {
            $filter = [
                '[0]=[devicevariations.carrierId]=1[or][devicevariations.carrierId]=3[or][devicevariations.carrierId]=5'
            ];
        } else {
            $filter = [
                '[0]=[devicevariations.carrierId]=1[or][devicevariations.carrierId]=3[or][devicevariations.carrierId]=5',
                '[devicevariations.companyId]=1'
            ];
        }

        $resArray = (array)json_decode($this->json('GET', '/devices?include=devicevariations,devicevariations.carriers&filter[]=[devicevariations.carrierId]=' . $carrier1->id . '[or][devicevariations.carrierId]=' . $carrier3->id . '[or][devicevariations.carrierId]=' . $carrier5->id)->response->getContent());

        $this->assertCount(3, $resArray['data']);
        $this->assertEquals($resArray['data'][0]->id, $device1->id);
        $this->assertEquals($resArray['data'][1]->id, $device3->id);
        $this->assertEquals($resArray['data'][2]->id, $device5->id);
        $this->assertEquals($resArray['meta']->filter, $filter);
        $this->assertCount(6, $resArray['included']);
    }

    public function testFilterModelByItsIncludesGrandchildren() {

        $device1 = factory(\WA\DataStore\Device\Device::class)->create(['name' => 'device1']);
        $device2 = factory(\WA\DataStore\Device\Device::class)->create(['name' => 'device2']);
        $device3 = factory(\WA\DataStore\Device\Device::class)->create(['name' => 'device3']);
        $device4 = factory(\WA\DataStore\Device\Device::class)->create(['name' => 'device4']);
        $device5 = factory(\WA\DataStore\Device\Device::class)->create(['name' => 'device5']);

        $carrier1 = factory(\WA\DataStore\Carrier\Carrier::class)->create(['name' => 'carrier1']);
        $carrier2 = factory(\WA\DataStore\Carrier\Carrier::class)->create(['name' => 'carrier2']);
        $carrier3 = factory(\WA\DataStore\Carrier\Carrier::class)->create(['name' => 'carrier3']);
        $carrier4 = factory(\WA\DataStore\Carrier\Carrier::class)->create(['name' => 'carrier4']);
        $carrier5 = factory(\WA\DataStore\Carrier\Carrier::class)->create(['name' => 'carrier5']);


        $devvar1 = factory(\WA\DataStore\DeviceVariation\DeviceVariation::class)->create([
            'carrierId' => $carrier1->id,
            'deviceId' => $device1->id,
            'priceRetail' => 100
        ]);
        $devvar2 = factory(\WA\DataStore\DeviceVariation\DeviceVariation::class)->create([
            'carrierId' => $carrier2->id,
            'deviceId' => $device2->id,
            'priceRetail' => 200
        ]);
        $devvar3 = factory(\WA\DataStore\DeviceVariation\DeviceVariation::class)->create([
            'carrierId' => $carrier1->id,
            'deviceId' => $device3->id,
            'priceRetail' => 300
        ]);
        $devvar4 = factory(\WA\DataStore\DeviceVariation\DeviceVariation::class)->create([
            'carrierId' => $carrier4->id,
            'deviceId' => $device4->id,
            'priceRetail' => 400
        ]);
        $devvar5 = factory(\WA\DataStore\DeviceVariation\DeviceVariation::class)->create([
            'carrierId' => $carrier1->id,
            'deviceId' => $device5->id,
            'priceRetail' => 500
        ]);

        if ($this->idLogged == 1) {
            $filter = [
                '[devicevariations.carriers.name]=' . $carrier1->name
            ];
        } else {
            $filter = [
                '[devicevariations.carriers.name]=' . $carrier1->name,
                '[devicevariations.companyId]=1'
            ];
        }

        $resArray = (array)json_decode($this->json('GET', '/devices?include=devicevariations,devicevariations.carriers&filter[devicevariations.carriers.name]=' . $carrier1->name)->response->getContent());

        $this->assertCount(3, $resArray['data']);
        $this->assertEquals($resArray['data'][0]->id, $device1->id);
        $this->assertEquals($resArray['data'][1]->id, $device3->id);
        $this->assertEquals($resArray['data'][2]->id, $device5->id);
        $this->assertEquals($resArray['meta']->filter, $filter);
        $this->assertCount(4, $resArray['included']);
    }

    public function testFilterModelByItsIncludesANDGrandchildren() {

        $device1 = factory(\WA\DataStore\Device\Device::class)->create(['name' => 'device1']);
        $device2 = factory(\WA\DataStore\Device\Device::class)->create(['name' => 'device2']);
        $device3 = factory(\WA\DataStore\Device\Device::class)->create(['name' => 'device3']);
        $device4 = factory(\WA\DataStore\Device\Device::class)->create(['name' => 'device4']);
        $device5 = factory(\WA\DataStore\Device\Device::class)->create(['name' => 'device5']);

        $carrier1 = factory(\WA\DataStore\Carrier\Carrier::class)->create(['name' => 'carrier1']);
        $carrier2 = factory(\WA\DataStore\Carrier\Carrier::class)->create(['name' => 'carrier2']);
        $carrier3 = factory(\WA\DataStore\Carrier\Carrier::class)->create(['name' => 'carrier3']);
        $carrier4 = factory(\WA\DataStore\Carrier\Carrier::class)->create(['name' => 'carrier4']);
        $carrier5 = factory(\WA\DataStore\Carrier\Carrier::class)->create(['name' => 'carrier5']);


        $devvar1 = factory(\WA\DataStore\DeviceVariation\DeviceVariation::class)->create([
            'carrierId' => $carrier1->id,
            'deviceId' => $device1->id,
            'priceRetail' => 100
        ]);
        $devvar2 = factory(\WA\DataStore\DeviceVariation\DeviceVariation::class)->create([
            'carrierId' => $carrier2->id,
            'deviceId' => $device2->id,
            'priceRetail' => 200
        ]);
        $devvar3 = factory(\WA\DataStore\DeviceVariation\DeviceVariation::class)->create([
            'carrierId' => $carrier3->id,
            'deviceId' => $device3->id,
            'priceRetail' => 300
        ]);
        $devvar4 = factory(\WA\DataStore\DeviceVariation\DeviceVariation::class)->create([
            'carrierId' => $carrier4->id,
            'deviceId' => $device4->id,
            'priceRetail' => 400
        ]);
        $devvar5 = factory(\WA\DataStore\DeviceVariation\DeviceVariation::class)->create([
            'carrierId' => $carrier5->id,
            'deviceId' => $device5->id,
            'priceRetail' => 500
        ]);

        if ($this->idLogged == 1) {
            $filter = [
                '[devicevariations.carriers.name]=' . $carrier1->name . ',' . $carrier2->name
            ];
        } else {
            $filter = [
                '[devicevariations.carriers.name]=' . $carrier1->name . ',' . $carrier2->name,
                '[devicevariations.companyId]=1'
            ];
        }

        $resArray = (array)json_decode($this->json('GET', '/devices?include=devicevariations,devicevariations.carriers&filter[devicevariations.carriers.name]=' . $carrier1->name . ',' . $carrier2->name)->response->getContent());

        $this->assertCount(2, $resArray['data']);
        $this->assertEquals($resArray['data'][0]->id, $device1->id);
        $this->assertEquals($resArray['data'][1]->id, $device2->id);
        $this->assertEquals($resArray['meta']->filter, $filter);
        $this->assertCount(4, $resArray['included']);
    }

    public function testFilterModelByItsIncludesORGrandchildren() {

        $device1 = factory(\WA\DataStore\Device\Device::class)->create(['name' => 'device1']);
        $device2 = factory(\WA\DataStore\Device\Device::class)->create(['name' => 'device2']);
        $device3 = factory(\WA\DataStore\Device\Device::class)->create(['name' => 'device3']);
        $device4 = factory(\WA\DataStore\Device\Device::class)->create(['name' => 'device4']);
        $device5 = factory(\WA\DataStore\Device\Device::class)->create(['name' => 'device5']);

        $carrier1 = factory(\WA\DataStore\Carrier\Carrier::class)->create(['name' => 'carrier1']);
        $carrier2 = factory(\WA\DataStore\Carrier\Carrier::class)->create(['name' => 'carrier2']);
        $carrier3 = factory(\WA\DataStore\Carrier\Carrier::class)->create(['name' => 'carrier3']);
        $carrier4 = factory(\WA\DataStore\Carrier\Carrier::class)->create(['name' => 'carrier4']);
        $carrier5 = factory(\WA\DataStore\Carrier\Carrier::class)->create(['name' => 'carrier5']);


        $devvar1 = factory(\WA\DataStore\DeviceVariation\DeviceVariation::class)->create([
            'carrierId' => $carrier1->id,
            'deviceId' => $device1->id,
            'priceRetail' => 100
        ]);
        $devvar2 = factory(\WA\DataStore\DeviceVariation\DeviceVariation::class)->create([
            'carrierId' => $carrier2->id,
            'deviceId' => $device2->id,
            'priceRetail' => 200
        ]);
        $devvar3 = factory(\WA\DataStore\DeviceVariation\DeviceVariation::class)->create([
            'carrierId' => $carrier3->id,
            'deviceId' => $device3->id,
            'priceRetail' => 300
        ]);
        $devvar4 = factory(\WA\DataStore\DeviceVariation\DeviceVariation::class)->create([
            'carrierId' => $carrier1->id,
            'deviceId' => $device4->id,
            'priceRetail' => 400
        ]);
        $devvar5 = factory(\WA\DataStore\DeviceVariation\DeviceVariation::class)->create([
            'carrierId' => $carrier5->id,
            'deviceId' => $device5->id,
            'priceRetail' => 500
        ]);

        if ($this->idLogged == 1) {
            $filter = [
                '[0]=[devicevariations.carriers.name]=' . $carrier1->name . '[or][devicevariations.carriers.name]=' . $carrier2->name
            ];
        } else {
            $filter = [
                '[0]=[devicevariations.carriers.name]=' . $carrier1->name . '[or][devicevariations.carriers.name]=' . $carrier2->name,
                '[devicevariations.companyId]=1'
            ];
        }

        $resArray = (array)json_decode($this->json('GET', '/devices?include=devicevariations,devicevariations.carriers&filter[]=[devicevariations.carriers.name]=' . $carrier1->name . '[or][devicevariations.carriers.name]=' . $carrier2->name)->response->getContent());

        $this->assertCount(3, $resArray['data']);
        $this->assertEquals($resArray['data'][0]->id, $device1->id);
        $this->assertEquals($resArray['data'][1]->id, $device2->id);
        $this->assertEquals($resArray['data'][2]->id, $device4->id);
        $this->assertEquals($resArray['meta']->filter, $filter);
        $this->assertCount(5, $resArray['included']);
    }
}
