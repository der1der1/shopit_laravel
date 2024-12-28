<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class WordAds2TableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('word_ads_2')->delete();
        
        \DB::table('word_ads_2')->insert(array (
            0 => 
            array (
                'id' => 6,
                'words' => '不僅是商品，更是生活方式的象徵。我們致力於提供最好的選擇，讓您的生活充滿愉悅與精彩。',
            ),
            1 => 
            array (
                'id' => 1,
                'words' => '品味生活，我們與您同行。精心挑選的商品，為您呈現卓越品質與時尚風格。品味不凡，從這裡開始！',
            ),
            2 => 
            array (
                'id' => 3,
                'words' => '專為您訂製的個性化體驗！品味生活，從這裡開始。我們將為您提供無與倫比的品質和服務。',
            ),
            3 => 
            array (
                'id' => 5,
                'words' => '擁有品味，從此開始。我們的產品將為您的生活增添色彩。選擇我們，讓您的每一刻都閃耀奪目！',
            ),
            4 => 
            array (
                'id' => 2,
                'words' => '時尚與舒適的完美結合！我們的產品將為您帶來無限精彩。追求卓越，選擇我們，將讓您與眾不同！',
            ),
            5 => 
            array (
                'id' => 7,
                'words' => '獨特風格，不凡魅力。我們的產品與您的品味相得益彰。從細節中展現您的獨特魅力，與我們一同開啟精彩之旅！',
            ),
            6 => 
            array (
                'id' => 4,
                'words' => '追求品質生活？我們為您打造。從頂級產品到卓越服務，我們將滿足您的一切需求。盡情享受，我們為您而來！',
            ),
        ));
        
        
    }
}