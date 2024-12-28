<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UserTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('user')->delete();
        
        \DB::table('user')->insert(array (
            0 => 
            array (
                'user_id' => 1,
                'account' => 'deniel87deniel87@gmail.com',
                'password' => '8787',
                'prvilige' => 'A',
                'name' => '德一得第',
                'to_shop' => '新竹中山店',
                'to_address' => '台南市善化區小新123里',
                'bank_account' => '900',
                'shop1_addr2' => 2,
                'info0' => '訂購商品出貨嘍！吸塵器、桌子&nbsp;&nbsp;2024/07/11',
                'info1' => '訂購商品出貨嘍！大沙發、吸塵器&nbsp;&nbsp;2024/06/21',
                'info2' => '訂購商品出貨嘍！洗衣機&nbsp;&nbsp;2024/06/18',
            ),
            1 => 
            array (
                'user_id' => 2,
                'account' => 'sample@gmail.com',
                'password' => '8787',
                'prvilige' => 'B',
                'name' => '周星星',
                'to_shop' => NULL,
                'to_address' => '台北市信義區吳興街',
                'bank_account' => '9995555111',
                'shop1_addr2' => 2,
                'info0' => '',
                'info1' => '',
                'info2' => '',
            ),
            2 => 
            array (
                'user_id' => 3,
                'account' => 'example@email.com',
                'password' => '8787',
                'prvilige' => 'B',
                'name' => '吳宗憲',
                'to_shop' => '中正忠孝店',
                'to_address' => '台北市大安區復興南路一段100號',
                'bank_account' => '8817899565',
                'shop1_addr2' => NULL,
                'info0' => NULL,
                'info1' => NULL,
                'info2' => NULL,
            ),
            3 => 
            array (
                'user_id' => 4,
                'account' => 'user@email.com',
                'password' => 'user',
                'prvilige' => 'B',
                'name' => '林大同',
                'to_shop' => NULL,
                'to_address' => '新北市板橋區文化路二段50巷20號',
                'bank_account' => NULL,
                'shop1_addr2' => NULL,
                'info0' => NULL,
                'info1' => NULL,
                'info2' => NULL,
            ),
            4 => 
            array (
                'user_id' => 5,
                'account' => 'mockuser@email.com',
                'password' => 'mockuser',
                'prvilige' => 'B',
                'name' => '張小明',
                'to_shop' => '苓雅中華店',
                'to_address' => '台中市西屯區文心路三段300號',
                'bank_account' => '017794-558629',
                'shop1_addr2' => NULL,
                'info0' => NULL,
                'info1' => NULL,
                'info2' => NULL,
            ),
            5 => 
            array (
                'user_id' => 7,
                'account' => 'testadmin@gmail.com',
                'password' => 'testadmin',
                'prvilige' => 'A',
                'name' => '王小華',
                'to_shop' => NULL,
                'to_address' => '高雄市鳳山區中山路100號',
                'bank_account' => NULL,
                'shop1_addr2' => NULL,
                'info0' => NULL,
                'info1' => NULL,
                'info2' => NULL,
            ),
            6 => 
            array (
                'user_id' => 9,
                'account' => 'testadmin2@gmail.com',
                'password' => '123',
                'prvilige' => 'B',
                'name' => '吳小強',
                'to_shop' => NULL,
                'to_address' => '台南市中西區民權路二段50巷5號',
                'bank_account' => NULL,
                'shop1_addr2' => NULL,
                'info0' => NULL,
                'info1' => NULL,
                'info2' => NULL,
            ),
            7 => 
            array (
                'user_id' => 10,
                'account' => 'lookathere@gmail.com',
                'password' => 'looook',
                'prvilige' => 'B',
                'name' => NULL,
                'to_shop' => NULL,
                'to_address' => NULL,
                'bank_account' => NULL,
                'shop1_addr2' => NULL,
                'info0' => NULL,
                'info1' => NULL,
                'info2' => NULL,
            ),
            8 => 
            array (
                'user_id' => 11,
                'account' => 'deniel87@gmail.com',
                'password' => '123',
                'prvilige' => 'A',
                'name' => NULL,
                'to_shop' => NULL,
                'to_address' => NULL,
                'bank_account' => NULL,
                'shop1_addr2' => NULL,
                'info0' => NULL,
                'info1' => NULL,
                'info2' => NULL,
            ),
        ));
        
        
    }
}