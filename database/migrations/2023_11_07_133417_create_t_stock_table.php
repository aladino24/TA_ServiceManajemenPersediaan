<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTStockTable extends Migration
{
    public function up()
    {
        Schema::create('t_stock', function (Blueprint $table) {
            $table->char('fc_divisioncode', 20)->default('SBY001')->comment('>>> Kode Holding, sementara FIX');
            $table->string('fc_branch', 6)->default('')->comment('>>> Kode branch/cabang akan Yudha siapkan di t_trxtype');
            $table->string('fc_stockcode', 20)->default('')->comment('>>> Kode Barang dari catalog supplier');
            $table->string('fc_barcode', 100)->default('')->comment('>>> Kode Barang Internal DEXA');
            $table->string('fc_nameshort', 100)->default('')->comment('>>> Nama pendek / yang mudah dipanggil tapi harus tetap unique');
            $table->string('fc_namelong', 100)->default('')->comment('>>> Nama panjang / complete dan juga harus unique');
            $table->char('fl_batch', 1)->default('F')->comment('>>> Jika T maka barang tsb, mempunyai BATCH');
            $table->char('fl_expired', 1)->default('F')->comment('>>> Jika T maka barang tsb, ada ED nya. Biasanya jika BATCH pasti ED.');
            $table->char('fl_serialnumber', 1)->default('F')->comment('>>> Jika T maka barang tsb, punya SERIAL NUMBER');
            $table->char('fl_catnumber', 1)->default('F')->comment('>>> Jika T maka barang tsb, punya CAT NUMBER');
            $table->string('fc_catnumber', 20)->default('');
            $table->char('fl_blacklist', 1)->default('F')->comment('>>> Jika T maka sudah gak bisa dipakai transaksi');
            $table->char('fl_taxtype', 1)->default('F')->comment('>>> Jika T maka dampaknya waktu penjualan tidak bisa campur dengan barang biasa. karena harus kena pajak');
            $table->char('fl_repsupplier', 1)->default('F')->comment('>>> Jika T maka ada kewajiban menginformasikan perihal penjualan/pendistribusian ke supplier');
            $table->char('fc_hold', 1)->default('F');
            $table->string('fc_brand', 20)->default('')->comment('>>> LookUp dari table t_brand. t_brand nya ling juga ke t_supplier');
            $table->string('fc_group', 20)->default('')->comment('>>> LookUp dari table t_brand');
            $table->string('fc_subgroup', 50)->default('')->comment('>>> LookUp dari table t_brand');
            $table->string('fc_typestock1', 30)->default(null)->comment('>>> Type 1; liquid/Padat/Gas,dll lihat dari t_trxtype');
            $table->string('fc_typestock2', 30)->default(null)->comment('>>> Type 2; Obat/Reagen/Consumable/ElectTools/ElectNonTools,dll lihat dari t_trxtype');
            $table->string('fc_namepack', 30)->default(null)->comment('>>> Satuan; kit/pack/dus/vial/pcs/botol,dll lihat dari t_trxtype');
            $table->double('fn_reorderlevel')->notNull()->default(0)->comment('>>> reminder jika barang tinggal dikit');
            $table->double('fn_maxonhand')->notNull()->default(0)->comment('>>> reminder jika barang kebanyakan');
            $table->double('fm_cogs')->notNull()->default(0)->comment('>>> HPP ');
            $table->double('fm_purchase')->notNull()->default(0)->comment('>>> harga beli dari supplier');
            $table->double('fm_salesprice')->notNull()->default(0)->comment('>>> harga jual non discount');
            $table->char('fl_disc_date', 1)->default('F')->comment('>>> jika T penerapan discount per-Tanggal jalan. Tapi jika F tidak jalan meskipun ada tanggalnya');
            $table->date('fd_disc_begin')->default(null)->comment('>>> Tanggal Periode Awal Discount');
            $table->date('fd_disc_end')->default(null)->comment('>>> Tanggal Periode Akhir Discount');
            $table->double('fm_disc_rp')->notNull()->default(0)->comment('>>> Nilai Disc. Rp per tanggal');
            $table->double('fm_disc_pr')->notNull()->default(0)->comment('>>> Nilai Disc % per tanggal');
            $table->char('fl_disc_time', 1)->notNull()->default('F')->comment('>>> jika T penerapan discount per-Jam jalan. Tapi jika F tidak jalan meskipun ada jamnya');
            $table->time('ft_disc_begin')->default(null)->comment('>>> Jam awal discount HH:NN');
            $table->time('ft_disc_end')->default(null)->comment('>>> Jam akhir discount HH:NN');
            $table->double('fm_time_disc_rp')->notNull()->default(0)->comment('>>> Nilai Disc. Rp untuk discount per-jam');
            $table->double('fm_time_disc_pr')->notNull()->default(0)->comment('>>> Nilai Disc. % untuk discount per-jam');
            $table->double('fm_price_default')->notNull()->default(0)->comment('>>> harga standard yang harus terisi');
            $table->double('fm_price_distributor')->notNull()->default(0)->comment('>>> harga standard distributor (selama tidak ada pengkhusus`an per customer)');
            $table->double('fm_price_project')->notNull()->default(0)->comment('>>> harga standard project (selama tidak ada pengkhusus`an per customer)');
            $table->double('fm_price_dealer')->notNull()->default(0)->comment('>>> harga standard dealer (selama tidak ada pengkhusus`an per customer)');
            $table->double('fm_price_enduser')->notNull()->default(0)->comment('>>> harga standard enduser (selama tidak ada pengkhusus`an per customer)');
            $table->text('fv_stockdescription')->default(null);

            $table->timestamps(); // Kolom created_at dan updated_at
            $table->softDeletes(); // Kolom deleted_at

            $table->primary(['fc_divisioncode', 'fc_branch', 'fc_stockcode', 'fc_barcode']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('t_stock');
    }
};
