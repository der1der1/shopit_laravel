<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ProductsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('products')->delete();
        
        \DB::table('products')->insert(array (
            0 => 
            array (
                'id' => 1,
                'pic_name' => '',
                'pic_dir' => 'img/pictureTarget/soap.jpeg',
                'product_name' => '肥皂',
                'description' => '化工肥皂，味道最經典!',
                'price' => '27',
                'ori_price' => '50',
                'category' => '肥皂',
                'selected' => '',
                'created_at' => '2024-07-29 13:34:59',
                'updated_at' => '2024-07-29 13:34:59',
            ),
            1 => 
            array (
                'id' => 2,
                'pic_name' => '',
                'pic_dir' => 'img/pictureTarget/sofa.png',
                'product_name' => '大沙發',
                'description' => '醉在大沙發',
                'price' => '25000',
                'ori_price' => '40000',
                'category' => '沙發',
                'selected' => '',
                'created_at' => '2024-07-29 13:34:59',
                'updated_at' => '2024-07-29 13:34:59',
            ),
            2 => 
            array (
                'id' => 3,
                'pic_name' => '',
                'pic_dir' => 'img/pictureTarget/travelBox.png',
                'product_name' => '行李箱',
                'description' => '骨董行李箱招標',
                'price' => '3500',
                'ori_price' => '3700',
                'category' => '行李箱',
                'selected' => '',
                'created_at' => '2024-07-29 13:34:59',
                'updated_at' => '2024-07-29 13:34:59',
            ),
            3 => 
            array (
                'id' => 4,
                'pic_name' => '',
                'pic_dir' => 'img/pictureTarget/rug.png',
                'product_name' => '地毯',
                'description' => '每人每戶家中都該有地毯吧',
                'price' => '12000',
                'ori_price' => '22000',
                'category' => '地毯',
                'selected' => '',
                'created_at' => '2024-07-29 13:34:59',
                'updated_at' => '2024-07-29 13:34:59',
            ),
            4 => 
            array (
                'id' => 5,
                'pic_name' => 'NB',
                'pic_dir' => 'img/pictureTarget/pc.png',
                'product_name' => '筆電',
                'description' => '筆記型電腦，四海皆為辦公室。',
                'price' => '29999',
                'ori_price' => '35000',
                'category' => '筆電',
                'selected' => '',
                'created_at' => '2024-07-29 13:34:59',
                'updated_at' => '2024-07-29 13:34:59',
            ),
            5 => 
            array (
                'id' => 6,
                'pic_name' => '吸塵器',
                'pic_dir' => 'img/pictureTarget/vaccum2.jpg',
                'product_name' => '高級吸塵器',
                'description' => '解決家庭紛爭神器',
                'price' => '18999',
                'ori_price' => '30000',
                'category' => '吸塵器',
                'selected' => '',
                'created_at' => '2024-07-29 13:34:59',
                'updated_at' => '2024-07-29 13:34:59',
            ),
            6 => 
            array (
                'id' => 7,
                'pic_name' => '洗衣機',
                'pic_dir' => 'img/pictureTarget/washing-machine.jpg',
                'product_name' => '洗衣機',
                'description' => '只要家裡有冰箱，食物都能放香香',
                'price' => '27900',
                'ori_price' => '58099',
                'category' => '洗衣機',
                'selected' => '',
                'created_at' => '2024-07-29 13:34:59',
                'updated_at' => '2024-07-29 13:34:59',
            ),
        ));
        
        
    }
}