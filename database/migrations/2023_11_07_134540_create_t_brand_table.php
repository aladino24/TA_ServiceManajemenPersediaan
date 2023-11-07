<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTBrandTable extends Migration
{
    public function up()
    {
        Schema::create('t_brand', function (Blueprint $table) {
            $table->char('fc_divisioncode', 20)->default('SBY001')->comment('>>> Kode Holding, sementara FIX');
            $table->string('fc_branch', 6)->default('A001')->comment('>>> Kode branch/cabang akan Yudha siapkan di t_trxtype');
            $table->string('fc_brand', 20)->default('')->comment('>>> Contoh Ilustrasi : Makanan,Minuman,dll');
            $table->string('fc_group', 20)->default('')->comment('>>> Contoh Ilustrasi : Makanan->Single, Makanan->Kelompok ');
            $table->string('fc_subgroup', 20)->default('')->comment('>>> Contoh Ilustrasi : Makanan->Single->Rawon, Makanan-Kelompok-Tumpeng,dll');
            $table->timestamps(); // Kolom created_at dan updated_at
            $table->softDeletes(); // Kolom deleted_at
            $table->string('created_by', 50)->nullable();
            $table->string('updated_by', 50)->nullable();
            $table->string('deleted_by', 50)->nullable();

            $table->primary(['fc_divisioncode', 'fc_branch', 'fc_brand', 'fc_group', 'fc_subgroup']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('t_brand');
    }
}
