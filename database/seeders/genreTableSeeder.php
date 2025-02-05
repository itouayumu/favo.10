<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class genreTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $param =[
            'genre_name' => 'アイドル'
        ];
        DB::table('genre')->insert($param);
        $param =[
            'genre_name' => 'ミュージシャン'
        ];
        DB::table('genre')->insert($param);
        $param =[
            'genre_name' => 'アニメキャラ'
        ];
        DB::table('genre')->insert($param);
        $param =[
            'genre_name' => 'アーティスト'
        ];
        DB::table('genre')->insert($param);
        $param =[
            'genre_name' => 'VTuber'
        ];
        DB::table('genre')->insert($param);
    }
}
