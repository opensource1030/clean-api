<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use WA\Database\Command\TablesRelationsAndIndexes;

class CreateEmployeesTable extends Migration
{

    use TablesRelationsAndIndexes;

    protected $tableName = 'employees';

    protected $foreignColumns = [
        'companyId' => 'nullable',
        'syncId' => 'nullable',
        'supervisorId' => 'nullable',
        'externalId' => 'nullable',
        'approverId' => 'nullable',
        'defaultLocationId' => 'nullable'
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            $this->tableName,
            function (Blueprint $table) {
                $table->increments('id');

                $table->string('uuid');
                $table->string('identification')->unique();

                $table->string('email')->nullable();
                $table->string('alternateEmail')->nullable();

                $table->string('password');
                $table->string('username');
                $table->string('confirmation_code')->nullable();
                $table->string('remember_token')->nullable();
                $table->boolean('confirmed')->default(false);

                $table->string('firstName')->nullable();
                $table->string('lastName')->nullable();

                $table->string('alternateFirstName')->nullable();
                $table->string('supervisorEmail')->nullable();
                $table->string('companyEmployeeIdentifier')->nullable();
                $table->boolean('isSupervisor')->default(0);
                $table->boolean('isValidator')->default(0);
                $table->boolean('isActive')->default(1);

                $table->integer('rgt')->nullable();
                $table->integer('lft')->nullable();
                $table->integer('hierarchy')->nullable();

                $table->string('defaultLang');
                $table->text('notes')->nullable();
                $table->integer('level')->default(0);
                $table->boolean('notify');


                $this->includeForeign($table, $this->foreignColumns);


                $table->softDeletes();
                $table->nullableTimestamps();
            }
        );
        // Creates password reminders table
        Schema::create('password_reminders', function ($table) {
            $table->string('email');
            $table->string('token');
            $table->timestamp('created_at');
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->dropForeignKeys($this->tableName, $this->foreignColumns);
        $this->forceDropTable($this->tableName);
        Schema::drop('password_reminders');

    }

}
