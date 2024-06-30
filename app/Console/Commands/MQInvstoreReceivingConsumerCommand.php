<?php

namespace App\Console\Commands;

use App\Services\ReceivingConsumerService;
use Illuminate\Console\Command;

class MQInvstoreReceivingConsumerCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mq:receiving-consumer';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Consume message from invstore-receiving queue';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $mqService = new ReceivingConsumerService();
        $mqService->consumerMessage();
    }
}
