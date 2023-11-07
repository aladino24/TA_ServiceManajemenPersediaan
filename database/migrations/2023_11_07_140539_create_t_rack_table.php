<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTRackTable extends Migration
{
    public function up()
    {
        Schema::create('t_rack', function (Blueprint $table) {
            $table->char('fc_divisioncode', 20)->default('')->comment('>>> Kode Holding, sementara FIX');
            $table->string('fc_branch', 6)->default('')->comment('>>> Dipilih user, menyesuaikan cabang yang menerima SO.');
            $table->string('fc_rackcode', 20)->default('')->comment('>>> ');
            $table->char('fl_status', 5)->default('GD')->comment('>>> ada dua type rack; Display (DP) dan Gudang (GD). mengapa harus dibedakan ? karena nanti waktu kita bikin penjualan RETAIL ( tanpa bikin SO, DO ) maka barang akan diambil dari DISPLAY (DP) ');
            $table->string('fc_rackname', 30)->default('')->comment('>>> nama rack; ');
            $table->integer('fn_capacity')->default(0)->comment('>>> kapasitas rack nya');
            $table->primary(['fc_rackcode', 'fl_status']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('t_rack');
    }
}
