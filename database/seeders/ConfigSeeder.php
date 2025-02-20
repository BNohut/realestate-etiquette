<?php

namespace Database\Seeders;

use App\Models\RecordType;
use App\Models\Setting;
use Illuminate\Database\Seeder;

class ConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $config = [
            "portfolio_resources" => [
                "shbndn" => "Sahibinden",
                "afis" => "Branda-Afiş",
                "hepsi" => "Hepsiemlak",
                "ilan" => "Haberiniz Var Mı? - El İlanı",
                "zng" => "Zingat",
                "rmx" => "Remax - Remaxİlyada",
                "etki" => "Etki Çevresi",
                "ofs" => "Ofis-Marka Müşterisi",
                "refer" => "Remax Referral",
                "uzmn" => "Uzmanlık Bölgesi",
                "ins" => "Instagram",
                "face" => "Facebook",
                "tik" => "TikTok",
                "link" => "Linked-In",
                "tube" => "Youtube",
                "dgr" => "Diğer Sosyal Medya"
            ],
            "contact_resources" => [
                "shbndn" => "Sahibinden",
                "afis" => "Branda-Afiş",
                "hepsi" => "Hepsiemlak",
                "ilan" => "Haberiniz Var Mı? - El İlanı",
                "zng" => "Zingat",
                "rmx" => "Remax - Remaxİlyada",
                "etki" => "Etki Çevresi",
                "ofs" => "Ofis-Marka Müşterisi",
                "refer" => "Remax Referral",
                "uzmn" => "Uzmanlık Bölgesi",
                "ins" => "Instagram",
                "face" => "Facebook",
                "tik" => "TikTok",
                "link" => "Linked-In",
                "tube" => "Youtube",
                "dgr" => "Diğer Sosyal Medya"
            ],
            "interview_results" => [
                "gele" => "Ciddi Müşteri - Görmeye Gelecek",
                "bnzr" => "Ciddi Müşteri - Benzer Mülk Arıyor",
                "bilgi" => "İsteksiz Müşteri - Bilgi Aldı",
                "uyg" => "Uygun Teklif Verdi",
                "paz" => "Düşük Teklif Verdi - Pazarlık Yapılacak",
                "muldus" => "Düşük Fiyatlı Mülk Arıyor",
                "cidde" => "Ciddi Alıcı Değil",
                "src" => "Satış Süreci Görüşmesi",
                "but" => "Yetersiz Bütçe"
            ],
            "record_levels" => [
                "dsk" => "Düşük",
                "ort" => "Orta",
                "yuk" => "Yüksek"
            ],
            "portfolio_types" => [
                "kir" => "Kiralık",
                "sat" => "Satılık"
            ],
            "portfolio_groups" => [
                "Apartman Dairesi" => [
                    "brbr" => "1+1 Daire",
                    "ikbr" => "2+1 Daire",
                    "ucbr" => "3+1 Daire",
                    "drtbr" => "4+1 Daire",
                    "bsbr" => "5+1 Daire",
                    "drtki" => "4+2 Daire",
                    "bski" => "5+2 Daire"
                ],
                "Yazlık" => [
                    "brbr" => "1+1 Daire",
                    "ikbr" => "2+1 Daire",
                    "ucbr" => "3+1 Daire",
                    "drtbr" => "4+1 Daire",
                    "bsbr" => "5+1 Daire",
                    "drtki" => "4+2 Daire",
                    "bski" => "5+2 Daire"
                ],
                "Arazi" => [
                    "kntimr" => "Konut İmarlı Arsa",
                    "trl" => "Tarla",
                    "zytn" => "Zeytinlik",
                    "bos" => "Boş Arazi",
                    "mer" => "Mera",
                    "paltar" => "Palamutlu Tarla",
                    "tur" => "Turizm İmarlı",
                    "tic" => "Ticari İmarlı",
                    "tarz" => "Zeytinli Tarla"
                ],
                "Ticari" => [
                    "dkkn" => "Dükkan",
                    "ofs" => "Ofis",
                    "dep" => "Depo",
                    "ote" => "Otel",
                    "pan" => "Pansiyon",
                    "butot" => "Butik Otel"
                ],
                "Müstakil Villa" => [
                    "ikbir" => "2+1 Müstakil Villa",
                    "ucbr" => "3+1 Müstakil Villa",
                    "drtbr" => "4+1 Müstakil Villa",
                    "bsbr" => "5+1 Müstakil Villa",
                    "drtki" => "4+2 Müstakil Villa",
                    "bski" => "5+2 Müstakil Villa",
                    "altki" => "6+2 Müstakil Villa"
                ]
            ],
            "fsbo_results" => [
                "rpa" => "RPA Hazırlanacak - Sunum Yapılacak",
                "snmit" => "Sunum Yapıldı - İtiraz Karşılama",
                "yetal" => "Yetki Belgesi Alındı",
                "ybel" => "Yetki Belgesi Alınamadı",
                "ara" => "Tekrar Aranacak",
                "emlk" => "Emlakçılarla Çalışıyor",
                "self" => "Kendisi Satmayı Deneyecek",
                "cidde" => "Ciddi Satıcı Değil"
            ],
            "demonstration_results" => [
                "uyg" => "Beğendi, Uygun Teklif Verdi",
                "dsk" => "Beğendi, Düşük Teklif Verdi",
                "bask" => "Beğenmedi, Başka Yer Görülecek",
                "bgnm" => "Beğenmedi",
                "tkrr" => "Tekrar Görmeye Gelecek",
                "tur" => "Gayrimenkul Turisti"
            ],
            "deed_statuses" => [
                "katmul" => "Kat Mülkiyeti",
                "kirt" => "Kat İrtifakı",
                "ars" => "Arsa Payı Tapu",
                "his" => "Hisseli Tapu",
                "must" => "Müstakil Tapu",
                "koop" => "Kooperatif Tapu"
            ],
            "activity_types" => [
                "rem" => "Remax İlanı",
                "shbndn" => "Sahibinden İlanı",
                "hepsi" => "Hepsiemlak İlanı",
                "zng" => "Zingat İlanı",
                "jet" => "EmlakJet İlanı",
                "face" => "Facebook Tanıtımı",
                "ins" => "Instagram Tanıtımı",
                "tktk" => "TikTok Tanıtımı",
                "saf" => "Safari Sunumu",
                "ilan" => "Haberiniz Var Mı? Broşürü",
            ]
        ];

        $setting = new Setting();
        $setting->config = json_encode($config);
        $setting->save();
    }
}
