<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Place;

class PlaceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Place::truncate();
        setlocale(LC_CTYPE, "ja.UTF8");
        $sjis = file_get_contents('database/seeders/csv/x-ken-all.csv');
        $utf8 = mb_convert_encoding($sjis, 'UTF-8', 'SJIS-win');
        file_put_contents('database/seeders/csv/x-ken-all_utf8.csv', $utf8);

        $fp = new \SplFileObject('database/seeders/csv/x-ken-all_utf8.csv', 'rb');
        $fp->setFlags(
            \SplFileObject::READ_CSV | //CSV列として行読み込み
            \SplFileObject::READ_AHEAD | //先読み/巻き戻し
            \SplFileObject::SKIP_EMPTY | //空行読み飛ばし
            \SplFileObject::DROP_NEW_LINE //行末の改行読み飛ばし
        );

        foreach ($fp as $line) {
            if ($fp->key() > 0 && !$fp->eof()) {
                // Place::create([
                //     'postal_code' => str_replace('-', '', $line[4]),
                //     'prefecture' => $line[7],
                //     'city' => $line[9],
                //     'street' => $line[11],
                //     'block' => $line[14]. $line[15],
                // ]);
                Place::create([
                    'postal_code' => $line[2],
                    'prefecture' => $line[6],
                    'city' => $line[7],
                    'street' => $line[8],
                ]);
            }
        }
    }
}
