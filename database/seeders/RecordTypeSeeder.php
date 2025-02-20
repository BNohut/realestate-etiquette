<?php

namespace Database\Seeders;

use App\Models\RecordType;
use App\Models\Transactions;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RecordTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $recordTypes = [
            ["name" => "F.S.B.O."],
            ["name" => "Çağrı"],
            ["name" => "Yer Gösterme"],
            ["name" => "Alıcı Müşteri"],
            ["name" => "Pazarlama"],
            ["name" => "Satış Kapama"],
            ["name" => "Tapu Satış-Kiralama İşlemleri"],
            ["name" => "Portföy"]
        ];

        foreach ($recordTypes as $recordType) {
            RecordType::create($recordType);
        }
    }
}
