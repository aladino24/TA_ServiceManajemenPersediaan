<?php

namespace App\Services;

use App\Models\Invstore;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use PhpAmqpLib\Connection\AMQPStreamConnection;

class ReceivingConsumerService
{
    protected $connection;
    public function __construct()
    {
        $this->connection = new AMQPStreamConnection(
            env('MQ_HOST'), 
            env('MQ_PORT'), 
            env('MQ_USER'), 
            env('MQ_PASS'), 
            env('MQ_VHOST')
        );
    }

    public function consumerMessage(){
        $channel = $this->connection->channel();
        $channel->queue_declare('invstore_from_receiving', false, true, false, false);
    
        echo ' [*] Waiting for messages. To exit press CTRL+C', "\n";
    
        $callback = function($msg) {
            echo ' [x] Received ', $msg->body, "\n";
            
            $data = json_decode($msg->body, true);
    
            try {
                DB::transaction(function () use ($data) {
                    $user = Session::get('user');
                    $branch = 'A001';
                    $divisioncode = 'SBY001';
                    $fc_membercode = $data['somst']['fc_membercode'];
                    
                    foreach ($data['dodtl'] as $detail) {
                        // $barcode = $detail['fc_barcode'] . $detail['fc_batch'] . date('dmY', strtotime($detail['fd_expired']));
                        $invstore = Invstore::where('fc_barcode', $detail['fc_barcode'])
                            ->where('fc_branch', $branch)
                            ->where('fc_membercode', $fc_membercode)
                            // ->where('fc_warehousecode', $data['fc_warehousecode'])
                            ->first();
    
                        if ($invstore) {
                            $invstore->update([
                                'fn_quantity' => $invstore->fn_quantity + $detail['fn_qty']
                            ]);
                        } else {
                            Invstore::create([
                                'fc_divisioncode' => $divisioncode,
                                'fc_branch' => $branch,
                                'fc_membercode' => $fc_membercode,
                                'fc_barcode' => $detail['fc_barcode'],
                                'fc_stockcode' => $detail['fc_stockcode'],
                                'fc_batch' => $detail['fc_batch'],
                                'fd_expired' => $detail['fd_expired'],
                                'fn_quantity' => $detail['fn_qty'],
                                'fm_hpp' => $detail['invstore']['fm_hpp'], // atau sesuai dengan field yang dibutuhkan
                                'fm_cogs' => $detail['invstore']['fm_hpp'], // atau sesuai dengan field yang dibutuhkan
                                'fm_purchase' => $detail['fm_price'],
                                'created_at' => now(),
                                'updated_at' => now()
                            ]);
                            echo " [x] Inserted into Invstore ", "\n";
                        }
                    }
                });
            } catch (\Exception $e) {
                // Jika terjadi error, lempar exception agar transaksi di-rollback
                echo ' [!] Error: ', $e->getMessage(), "\n";
                // Pesan tidak akan di-acknowledge sehingga tetap di antrian
                $msg->delivery_info['channel']->basic_nack($msg->delivery_info['delivery_tag'], false, true);
                return;
            }
    
            // Pesan di-acknowledge jika berhasil diproses
            $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
        };
    
        $channel->basic_consume('invstore_from_receiving', '', false, false, false, false, $callback);
    
        while (count($channel->callbacks)) {
            $channel->wait();
        }
    
        $channel->close();
        $this->connection->close();
    }
    

}