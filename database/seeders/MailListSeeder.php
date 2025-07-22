<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class MailListSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('mail_list')->insert([
            'name' => '所有客人',
            'title' => '啟',
            'email' => 'null', // email 欄位為 NULL
            'onoff' => 1,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }
}
