<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('t_trxtype', function (Blueprint $table) {
            $table->id();
            $table->string('fc_trx', 20)->default('');
            $table->char('fc_kode', 15)->default('');
            $table->string('fv_description', 100)->nullable();
            $table->string('fc_action', 20)->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->string('created_by', 50)->nullable();
            $table->string('updated_by', 50)->nullable();
            $table->string('deleted_by', 50)->nullable();

            $table->unique(['fc_trx', 'fc_kode']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_trxtype');
    }
};

