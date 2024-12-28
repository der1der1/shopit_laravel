<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class MarqeeTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('marqee')->delete();
        
        \DB::table('marqee')->insert(array (
            0 => 
            array (
                'id' => 1,
                'texts' => '品味生活，我們與您同行。精心挑選的商品，為您呈現卓越品質與時尚風格。品味不凡，從這裡開始！',
                'created_at' => '2024-07-29 13:34:59',
                'updated_at' => '2024-07-29 13:34:59',
            ),
            1 => 
            array (
                'id' => 2,
                'texts' => '時尚與舒適的完美結合！我們的產品將為您帶來無限精彩。追求卓越，選擇我們，將讓您與眾不同！',
                'created_at' => '2024-07-29 13:34:59',
                'updated_at' => '2024-07-29 13:34:59',
            ),
            2 => 
            array (
                'id' => 3,
                'texts' => '專為您訂製的個性化體驗！品味生活，從這裡開始。我們將為您提供無與倫比的品質和服務。',
                'created_at' => '2024-07-29 13:34:59',
                'updated_at' => '2024-07-29 13:34:59',
            ),
            3 => 
            array (
                'id' => 4,
                'texts' => '追求品質生活？我們為您打造。從頂級產品到卓越服務，我們將滿足您的一切需求。盡情享受，我們為您而來！',
                'created_at' => '2024-07-29 13:34:59',
                'updated_at' => '2024-07-29 13:34:59',
            ),
            4 => 
            array (
                'id' => 5,
                'texts' => '擁有品味，從此開始。我們的產品將為您的生活增添色彩。選擇我們，讓您的每一刻都閃耀奪目！',
                'created_at' => '2024-07-29 13:34:59',
                'updated_at' => '2024-07-29 13:34:59',
            ),
            5 => 
            array (
                'id' => 6,
                'texts' => '不僅是商品，更是生活方式的象徵。我們致力於提供最好的選擇，讓您的生活充滿愉悅與精彩。',
                'created_at' => '2024-07-29 13:34:59',
                'updated_at' => '2024-07-29 13:34:59',
            ),
            6 => 
            array (
                'id' => 7,
                'texts' => '獨特風格，不凡魅力。我們的產品與您的品味相得益彰。從細節中展現您的獨特魅力，與我們一同開啟精彩之旅！',
                'created_at' => '2024-07-29 13:34:59',
                'updated_at' => '2024-07-29 13:34:59',
            ),
        ));
        
        
    }
}