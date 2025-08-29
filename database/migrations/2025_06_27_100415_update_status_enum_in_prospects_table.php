<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class UpdateStatusEnumInProspectsTable extends Migration
{
    public function up()
    {
        DB::statement("ALTER TABLE prospects MODIFY COLUMN status 
            ENUM('nouveau', 'en_relance', 'interesse', 'converti', 'abandonne', 'client_reservataire') 
            NOT NULL DEFAULT 'nouveau'");
    }

    public function down()
    {
        DB::statement("ALTER TABLE prospects MODIFY COLUMN status 
            ENUM('nouveau', 'en_relance', 'interesse', 'converti', 'abandonne') 
            NOT NULL DEFAULT 'nouveau'");
    }
}
