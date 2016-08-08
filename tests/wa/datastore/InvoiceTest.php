<?php

namespace WA\DataStore;

use WA\Testing\TestCase;
use Way\Tests\ModelHelpers;

class InvoiceTest extends TestCase {
    use ModelHelpers;

    // protected $useCleanDatabase = TRUE;

    public function testModelAssociations() {

        $this->assertBelongsTo('company', 'WA\DataStore\Invoice');
//
        $this->assertBelongsTo('carrier', 'WA\DataStore\Invoice');
//
        $this->assertBelongsTo('dump', 'WA\DataStore\Invoice');
   }
}
