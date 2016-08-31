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
                $table->string('profileNameCondition');
                $table->string('profileNameValue');
                $table->string('profileEmailCondition');
                $table->string('profileEmailValue');
                $table->string('profilePositionCondition');
                $table->string('profilePositionValue');
                $table->string('profileLevelCondition');
                $table->string('profileLevelValue');
                $table->string('profileDivisionCondition');
                $table->string('profileDivisionValue');
                $table->string('profileCostCenterCondition');
                $table->string('profileCostCenterValue');
                $table->string('profileBudgetCondition');
                $table->string('profileBudgetValue');
                $table->string('locationItemsCountryACondition');
                $table->string('locationItemsCountryAValue');
                $table->string('locationItemsCountryBCondition');
                $table->string('locationItemsCountryBValue');
                $table->string('locationItemsCityCondition');
                $table->string('locationItemsCityValue');
                $table->string('locationItemsAdressCondition');
                $table->string('locationItemsAdressValue');

                $table->nullableTimestamps();
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
