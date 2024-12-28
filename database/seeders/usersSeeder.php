<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class usersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->delete();
        
        DB::table('users')->insert(array (
            0 => 
            array (
            'user_id'     => '1',
            'account'     => 'deniel87deniel87@gmail.com',
            'password'    => Hash::make('8787'),
            'prvilige'    => 'A',
            'name'        => '德一得第1',
            'remenber_token'=>'0',
            'to_shop'     => '新竹中山店',
            'to_address'  => '台南市善化區小新123里',
            'bank_account'=> '900',
            'shop1_addr2' => '2',
            'info0'       => '訂購商品出貨嘍！吸塵器、桌子&nbsp;&nbsp;2024/07/11',
            'info1'       => '訂購商品出貨嘍！大沙發、吸塵器&nbsp;&nbsp;2024/06/21',
            'info2'       => '訂購商品出貨嘍！洗衣機&nbsp;&nbsp;2024/06/18',
            'created_at' => now(),
            'updated_at' => now(),
            ),
            1 => 
            array (
            'user_id'     => '2',
            'account'     => 'test@gmail.com',
            'password'    => Hash::make('8787'),
            'prvilige'    => 'B',
            'name'        => '周星星',
            'remenber_token'=>'0',
            'to_shop'     => '0',
            'to_address'  => '台北市信義區吳興街',
            'bank_account'=> '9555111',
            'shop1_addr2' => '2',
            'info0'       => '0',
            'info1'       => '0',
            'info2'       => '0',
            'created_at' => now(),
            'updated_at' => now(),
            ),
            2 => 
            array (
            'user_id'     => '3',
            'account'     => 'user@email.com',
            'password'    => Hash::make('8787'),
            'prvilige'    => 'B',
            'name'        => '吳小強',
            'remenber_token'=>'0',
            'to_shop'     => '中正忠孝店',
            'to_address'  => '台北市大安區復興南路一段100號',
            'bank_account'=> '8817565',
            'shop1_addr2' => '1',
            'info0'       => '0',
            'info1'       => '0',
            'info2'       => '0',
            'created_at' => now(),
            'updated_at' => now(),
            ),
        ));
    }
}
