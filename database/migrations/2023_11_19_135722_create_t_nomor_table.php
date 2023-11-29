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
        Schema::create('t_nomor', function (Blueprint $table) {
            $table->string('fv_document', 10);
            $table->string('fc_branch', 10);
            $table->double('fn_count3');
            $table->string('fv_prefix', 10);
            $table->string('fv_sufix', 10);
            $table->double('fn_docno');
            $table->string('fv_part', 10);
            $table->timestamps();
            $table->softDeletes();
            $table->string('created_by', 10)->nullable();
            $table->string('updated_by', 10)->nullable();
            $table->string('deleted_by', 10)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_nomor');
    }
};
