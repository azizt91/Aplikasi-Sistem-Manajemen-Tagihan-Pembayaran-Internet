<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\TagihanController;

class GenerateMonthlyBills extends Command
{

    protected $signature = 'tagihan:generate';
    protected $description = 'Generate monthly bills for active customers';

    public function handle()
    {
        (new TagihanController)->generateMonthlyBills();
        $this->info('✅ Tagihan otomatis berhasil dibuat!');
    }
}
