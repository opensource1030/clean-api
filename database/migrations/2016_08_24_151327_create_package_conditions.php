<?php

use Illuminate\Database\Migrations\Migration;

class CreatePackageConditions extends Migration
{
    use \WA\Database\Command\TablesRelationsAndIndexes;

    protected $tableName = 'package_conditions';
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            $this->tableName,
            function ( $table) {
                $table->increments('id');
                $table->integer('profileNameCondition');
                $table->integer('profileNameValue');
                $table->integer('profileEmailCondition');
                $table->integer('profileEmailValue');
                $table->integer('profilePositionCondition');
                $table->integer('profilePositionValue');
                $table->integer('profileLevelCondition');
                $table->integer('profileLevelValue');
                $table->integer('profileDivisionCondition');
                $table->integer('profileDivisionValue');
                $table->integer('profileCostCenterCondition');
                $table->integer('profileCostCenterValue');
                $table->integer('profileBudgetCondition');
                $table->integer('profileBudgetValue');
                $table->integer('locationItemsCountryACondition');
                $table->integer('locationItemsCountryAValue');
                $table->integer('locationItemsCountryBCondition');
                $table->integer('locationItemsCountryBValue');
                $table->integer('locationItemsCityCondition');
                $table->integer('locationItemsCityValue');
                $table->integer('locationItemsAdressCondition');
                $table->integer('locationItemsAdressValue');
            }
        );

        Schema::table(
            'packages', 
            function($table) {
                $table->foreign('conditionsId')->references('id')->on('package_conditions');
            }
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table(
            $this->tableName, 
            function($table) {
                //$table->dropForeign('conditionsId');
        });

        $this->forceDropTable($this->tableName);
    }
}

/* EXAMPLE

{
  "id": 1,
  "profile": {
    "name": {
      "condition": "is_any",
      "value": ""
    },
    "email": {
      "condition": "is_any",
      "value": ""
    },
    "position": {
      "condition": "contains",
      "value": "Engineer"
    },
    "level": {
      "condition": "is_greater_than",
      "value": "3"
    },
    "division": {
      "condition": "contains",
      "value": "Sales"
    },
    "costcenter": {
      "condition": "is_any",
      "value": ""
    },
    "budget": {
      "condition": "is_less_than",
      "value": "600"
    }
  },
  "location": {
    "items": {
      "countryA": {
        "condition": "contains",
        "value": "USA"
      },
      "countryB": {
        "condition": "contains",
        "value": "Canada"
      },
      "city": {
        "condition": "is_any",
        "value": ""
      },
      "address": {
        "condition": "is_any",
        "value": ""
      }
    },
    "links": {
      "self": {
        "href": "condition/1"
      }
    }
  }
}
*/
