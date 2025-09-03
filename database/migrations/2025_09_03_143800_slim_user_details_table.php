<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations: remove extra columns, keep only phone, secondary_phone, address_line plus required keys.
     */
    public function up(): void
    {
        Schema::table('user_details', function (Blueprint $table) {
            // Drop columns if they exist
            $columnsToDrop = [
                'gender','birth_date','birth_place','rt_rw','village','district','city','province','postal_code',
                'nationality','marital_status','religion','occupation','extra'
            ];
            foreach($columnsToDrop as $col){
                if (Schema::hasColumn('user_details', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }

    /**
     * Reverse the migrations: re-add removed columns.
     */
    public function down(): void
    {
        Schema::table('user_details', function (Blueprint $table) {
            if(!Schema::hasColumn('user_details','gender')) $table->string('gender',20)->nullable();
            if(!Schema::hasColumn('user_details','birth_date')) $table->date('birth_date')->nullable();
            if(!Schema::hasColumn('user_details','birth_place')) $table->string('birth_place',120)->nullable();
            if(!Schema::hasColumn('user_details','rt_rw')) $table->string('rt_rw',30)->nullable();
            if(!Schema::hasColumn('user_details','village')) $table->string('village',120)->nullable();
            if(!Schema::hasColumn('user_details','district')) $table->string('district',120)->nullable();
            if(!Schema::hasColumn('user_details','city')) $table->string('city',120)->nullable();
            if(!Schema::hasColumn('user_details','province')) $table->string('province',120)->nullable();
            if(!Schema::hasColumn('user_details','postal_code')) $table->string('postal_code',15)->nullable();
            if(!Schema::hasColumn('user_details','nationality')) $table->string('nationality',80)->nullable();
            if(!Schema::hasColumn('user_details','marital_status')) $table->string('marital_status',40)->nullable();
            if(!Schema::hasColumn('user_details','religion')) $table->string('religion',40)->nullable();
            if(!Schema::hasColumn('user_details','occupation')) $table->string('occupation',120)->nullable();
            if(!Schema::hasColumn('user_details','extra')) $table->json('extra')->nullable();
        });
    }
};
