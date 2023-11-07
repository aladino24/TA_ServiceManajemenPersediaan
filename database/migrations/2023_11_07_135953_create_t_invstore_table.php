<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTInvstoreTable extends Migration
{
    public function up()
    {
        Schema::create('t_invstore', function (Blueprint $table) {
            $table->char('fc_divisioncode', 20)->default('')->comment('>>> Kode Holding, sementara FIX');
            $table->string('fc_branch', 6)->default('')->comment('>>> Dipilih user, menyesuaikan cabang yang menerima SO.');
            $table->string('fc_warehousecode', 50)->default('WRHS2023A001SBY0010000001');
            $table->char('fl_status', 5)->default('GD')->comment('>>> DP=Display .... dipikir keri-keri');
            $table->integer('fn_quantity')->default(0)->comment('>>> quantity barang di rack yg bersangkutan');
            $table->string('fc_barcode', 100)->default('')->comment('>>> diambil dari t_stock');
            $table->string('fc_stockcode', 20)->default('');
            $table->string('fc_batch', 50)->default('')->comment('>>> Batch barang');
            $table->date('fd_expired')->comment('>>> Expired Date dari barang');
            $table->string('fc_catnumber', 50)->default('')->comment('>>> Jika T maka barang tsb, punya CAT NUMBER');
            $table->double('fm_hpp', 20, 2)->default(0.00);
            $table->double('fm_cogs', 20, 0)->default(0)->comment('>>> HPP ');
            $table->double('fm_purchase', 20, 0)->default(0)->comment('>>> harga beli dari supplier');
            $table->timestamps();

            $table->primary(['fc_divisioncode', 'fc_branch', 'fc_warehousecode', 'fc_barcode', 'fc_stockcode', 'fc_batch', 'fd_expired']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('t_invstore');
    }
}
