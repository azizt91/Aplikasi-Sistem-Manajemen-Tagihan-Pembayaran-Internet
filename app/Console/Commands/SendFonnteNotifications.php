<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\FonnteController;


class SendFonnteNotifications extends Command
{
    protected $signature = 'fonnte:send-notifications';
    protected $description = 'Mengirim pesan otomatis ke pelanggan melalui Fonnte';

    public function handle()
    {
        $controller = new FonnteController();
        $controller->sendNotifications();

        $this->info('Pesan otomatis telah dikirim!');
    }
}
