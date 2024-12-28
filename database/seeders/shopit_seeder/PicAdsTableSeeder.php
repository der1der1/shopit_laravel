<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class PicAdsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('pic_ads')->delete();
        
        \DB::table('pic_ads')->insert(array (
            0 => 
            array (
                'id' => 11,
                'name' => 'ad1',
                'img_dir' => './pictureTarget/ad1.png',
            ),
            1 => 
            array (
                'id' => 12,
                'name' => 'ad2',
                'img_dir' => './pictureTarget/ad2.png',
            ),
            2 => 
            array (
                'id' => 13,
                'name' => 'ad3',
                'img_dir' => './pictureTarget/ad3.png',
            ),
            3 => 
            array (
                'id' => 14,
                'name' => 'ad4',
                'img_dir' => './pictureTarget/ad4.png',
            ),
        ));
        
        
    }
}