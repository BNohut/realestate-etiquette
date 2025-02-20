<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProvincesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $provinces = [
            ['id' => '1', 'name' => 'Adana', 'plate_number' => '1', 'phone_code' => '322', 'tax_offices' => 'Adana İhtisas, 5 Ocak, Yüreğir, Seyhan, Ziyapaşa, Çukurova, Ceyhan, Kozan, Karataş, Feke, Karaisalı, Pozantı, Saimbeyli, Tufanbeyli, Yumurtalık, Aladağ, İmamoğlu'],
            ['id' => '2', 'name' => 'Adıyaman', 'plate_number' => '2', 'phone_code' => '416', 'tax_offices' => 'Adıyaman, Kahta, Besni, Çelikhan, Gerger, Gölbaşı, Samsat, Sincik, Tut'],
            ['id' => '3', 'name' => 'Afyon', 'plate_number' => '3', 'phone_code' => '272', 'tax_offices' => 'Tınaztepe, Kocatepe, Dinar, Bolvadin, Emirdağ, Sandıklı, Çay, Dazkırı, İhsaniye, Sinanpaşa, Sultandağı, Şuhut, Başmakçı, Bayat, İscehisar, Çobanlar, Evciler, Hocalar, Kızılören'],
            ['id' => '4', 'name' => 'Ağrı', 'plate_number' => '4', 'phone_code' => '472', 'tax_offices' => 'Ağrı, Doğubeyazıt, Patnos, Diyadin, Eleşkirt, Hamur, Taşlıçay, Tutak'],
            ['id' => '5', 'name' => 'Amasya', 'plate_number' => '5', 'phone_code' => '358', 'tax_offices' => 'Amasya, Merzifon, Gümüşhacıköy, Taşova, Suluova, Göynücek, Hamamözü'],
            ['id' => '6', 'name' => 'Ankara', 'plate_number' => '6', 'phone_code' => '312', 'tax_offices' => 'Anadolu İhtisas, Ankara İhtisas, Kavaklıdere, Hitit, Ostim, Veraset ve Harçlar, Maltepe, Yenimahalle, Çankaya, Kızılbey, Mithatpaşa, Ulus, Yıldırım Beyazıt, Seğmenler, Dikimevi, Doğanbey, Yeğenbey, Yahya Galip, Muhammet Karagüzel, Gölbaşı, Sincan, Dışkapı, Etimesgut, Başkent, Cumhuriyet, Keçiören, Kahramankazan, Polatlı, Şereflikoçhisar, Beypazarı, Çubuk, Haymana, Elmadağ, Ayaş, Balâ, Çamlıdere, Güdül, Kalecik, Kızılcahamam, Nallıhan, Akyurt, Evren'],
            ['id' => '7', 'name' => 'Antalya', 'plate_number' => '7', 'phone_code' => '242', 'tax_offices' => 'Antalya Kurumlar, Antalya İhtisas, Üçkapılar, Kalekapı, Muratpaşa, Düden, Alanya, Serik, Manavgat, Elmalı, Kemer, Kumluca, Finike, Akseki, Gazipaşa, Gündoğmuş, Kaş, Korkuteli, Demre, İbradı'],
            ['id' => '8', 'name' => 'Artvin', 'plate_number' => '8', 'phone_code' => '466', 'tax_offices' => 'Artvin, Hopa, Arhavi, Ardanuç, Borçka, Şavşat, Yusufeli, Murgul'],
            ['id' => '9', 'name' => 'Aydın', 'plate_number' => '9', 'phone_code' => '256', 'tax_offices' => 'Efeler, Güzelhisar, Nazilli, Söke, Çine, Germencik, Kuşadası, Didim, Bozdoğan, Karacasu, Koçarlı, Kuyucak, Sultanhisar, Yenipazar, Buharkent, İncirliova, Karpuzlu, Köşk'],
            ['id' => '10', 'name' => 'Balıkesir', 'plate_number' => '10', 'phone_code' => '266', 'tax_offices' => 'Karesi, Kurtdereli, Ayvalık, Bandırma, Burhaniye, Edremit, Gönen, Susurluk, Erdek, Bigadiç, Sındırgı, Dursunbey, Balya, Havran, İvrindi, Kepsut, Manyas, Savaştepe, Marmara, Gömeç'],
            ['id' => '11', 'name' => 'Bilecik', 'plate_number' => '11', 'phone_code' => '228', 'tax_offices' => 'Bilecik, Bozüyük, Gölpazarı, Osmaneli, Pazaryeri, Söğüt, Yenipazar, İnhisar'],
            ['id' => '12', 'name' => 'Bingöl', 'plate_number' => '12', 'phone_code' => '426', 'tax_offices' => 'Bingöl, Genç, Karlıova, Kiğı, Solhan, Adaklı, Yayladere, Yedisu'],
            ['id' => '13', 'name' => 'Bitlis', 'plate_number' => '13', 'phone_code' => '434', 'tax_offices' => 'Bitlis, Tatvan, Adilcevaz, Ahlat, Hizan, Mutki, Güroymak'],
            ['id' => '14', 'name' => 'Bolu', 'plate_number' => '14', 'phone_code' => '374', 'tax_offices' => 'Bolu, Gerede, Göynük, Kıbrıscık, Mengen, Mudurnu, Seben, Dörtdivan, Yeniçağa'],
            ['id' => '15', 'name' => 'Burdur', 'plate_number' => '15', 'phone_code' => '248', 'tax_offices' => 'Burdur, Bucak, Ağlasun, Gölhisar, Tefenni, Yeşilova, Karamanlı, Kemer, Altınyayla, Çavdır, Çeltikçi'],
            ['id' => '16', 'name' => 'Bursa', 'plate_number' => '16', 'phone_code' => '224', 'tax_offices' => 'Bursa İhtisas, Osmangazi, Yıldırım, Çekirge, Setbaşı, Uludağ, Yeşil, Nilüfer, Ertuğrulgazi, Gökdere, Gemlik, İnegöl, Karacabey, Mustafakemalpaşa, Mudanya, Orhangazi, İznik, Yenişehir, Keles, Orhaneli, Harmancık, Büyükorhan'],
            ['id' => '17', 'name' => 'Çanakkale', 'plate_number' => '17', 'phone_code' => '286', 'tax_offices' => 'Çanakkale, Biga, Çan, Gelibolu, Ayvacık, Bayramiç, Bozcaada, Eceabat, Ezine, Gökçeada, Lapseki, Yenice'],
            ['id' => '18', 'name' => 'Çankırı', 'plate_number' => '18', 'phone_code' => '376', 'tax_offices' => 'Çankırı, Çerkeş, Eldivan, Ilgaz, Kurşunlu, Orta, Şabanözü, Yapraklı, Atkaracalar, Kızılırmak, Bayramören, Korgun'],
            ['id' => '19', 'name' => 'Çorum', 'plate_number' => '19', 'phone_code' => '364', 'tax_offices' => 'Çorum, Sungurlu, Alaca, Bayat, İskilip, Kargı, Mecitözü, Ortaköy, Osmancık, Boğazkale, Uğurludağ, Dodurga, Oğuzlar, Laçin'],
            ['id' => '20', 'name' => 'Denizli', 'plate_number' => '20', 'phone_code' => '258', 'tax_offices' => 'Çınar, Gökpınar, Saraylar, Denizli İhtisas, Pamukkale, Sarayköy, Acıpayam, Tavas, Buldan, Çal, Çivril, Çameli, Çardak, Güney, Kale, Babadağ, Bekilli, Honaz, Serinhisar, Akköy, Baklan, Beyağaç, Bozkurt'],
            ['id' => '21', 'name' => 'Diyarbakır', 'plate_number' => '21', 'phone_code' => '412', 'tax_offices' => 'Gökalp, Süleyman Nazif, Cahit Sıtkı Tarancı, Bismil, Çermik, Çınar, Çüngüş, Dicle, Ergani, Hani, Hazro, Kulp, Lice, Silvan, Eğil, Kocaköy'],
            ['id' => '22', 'name' => 'Edirne', 'plate_number' => '22', 'phone_code' => '284', 'tax_offices' => 'Arda, Kırkpınar, Keşan, Uzunköprü, Havsa, İpsala, Enez, Lalapaşa, Meriç, Süloğlu'],
            ['id' => '23', 'name' => 'Elazığ', 'plate_number' => '23', 'phone_code' => '424', 'tax_offices' => 'Harput, Hazar, Ağın, Baskil, Karakoçan, Keban, Maden, Palu, Sivrice, Arıcak, Kovancılar, Alacakaya'],
            ['id' => '24', 'name' => 'Erzincan', 'plate_number' => '24', 'phone_code' => '446', 'tax_offices' => 'Fevzipaşa, Çayırlı, İliç, Kemah, Kemaliye, Refahiye, Tercan, Üzümlü, Otlukbeli'],
            ['id' => '25', 'name' => 'Erzurum', 'plate_number' => '25', 'phone_code' => '442', 'tax_offices' => 'Aziziye, Kazımkarabekir, Aşkale, Çat, Hınıs, Horasan, İspir, Karayazı, Narman, Oltu, Olur, Pasinler, Şenkaya, Tekman, Tortum, Karaçoban, Uzundere, Pazaryolu, Aziziye (Ilıca), Köprüköy'],
            ['id' => '26', 'name' => 'Eskişehir', 'plate_number' => '26', 'phone_code' => '222', 'tax_offices' => 'Eskişehir, Mahmudiye, Mihalıççık, Sarıcakaya, Seyitgazi, Alpu, Beylikova, İnönü, Günyüzü, Han, Mihalgazi'],
            ['id' => '27', 'name' => 'Gaziantep', 'plate_number' => '27', 'phone_code' => '342', 'tax_offices' => 'Gaziantep İhtisas, Suburcu, Şehitkâmil, Şahinbey, Gazikent, Kozanlı, Nizip, İslahiye, Araban, Oğuzeli, Yavuzeli, Karkamış, Nurdağı'],
            ['id' => '28', 'name' => 'Giresun', 'plate_number' => '28', 'phone_code' => '454', 'tax_offices' => 'Giresun, Bulancak, Alucra, Dereli, Espiye, Eynesil, Görele, Keşap, Şebinkarahisar, Tirebolu, Piraziz, Yağlıdere, Çanakçı, Güce, Çamoluk, Doğankent'],
            ['id' => '29', 'name' => 'Gümüşhane', 'plate_number' => '29', 'phone_code' => '456', 'tax_offices' => 'Gümüşhane, Kelkit, Şiran, Torul, Köse, Kürtün'],
            ['id' => '30', 'name' => 'Hakkari', 'plate_number' => '30', 'phone_code' => '438', 'tax_offices' => 'Hakkari, Yüksekova, Çukurca, Şemdinli'],
            ['id' => '31', 'name' => 'Hatay', 'plate_number' => '31', 'phone_code' => '326', 'tax_offices' => '23 Temmuz, Antakya, Şükrükanatlı, Sahil, Akdeniz, Asım Gündüz, Dörtyol, Kırıkhan, Reyhanlı, Samandağ, Altınözü, Hassa, Yayladağı, Erzin, Belen, Kumlu'],
            ['id' => '32', 'name' => 'Isparta', 'plate_number' => '32', 'phone_code' => '246', 'tax_offices' => 'Davraz, Kaymakkapı, Eğirdir, Yalvaç, Atabey, Gelendost, Keçiborlu, Senirkent, Sütçüler, Şarkikaraağaç, Uluborlu, Aksu, Gönen, Yenişarbademli'],
            ['id' => '33', 'name' => 'Mersin (İçel)', 'plate_number' => '33', 'phone_code' => '324', 'tax_offices' => 'İstiklâl, Uray, Liman, Toros, Mersin İhtisas, Erdemli, Silifke, Anamur, Kızılmurat, Şehitkerim, Gülnar, Mut, Aydıncık, Bozyazı, Çamlıyayla'],
            ['id' => '34', 'name' => 'İstanbul Avrupa', 'plate_number' => '34', 'phone_code' => '212', 'tax_offices' => 'Bakırköy, Beşiktaş, Beyoğlu, Esenyurt, Fatih, Gaziosmanpaşa, Kağıthane, Küçükçekmece, Sarıyer, Sultangazi, Şişli, Zeytinburnu, Eyüpsultan, Güngören, Avcılar, Bağcılar, Bahçelievler, Başakşehir, Bayrampaşa, Beylikdüzü, Büyükçekmece, Esenler'],
            ['id' => '35', 'name' => 'İstanbul Anadolu', 'plate_number' => '34', 'phone_code' => '216', 'tax_offices' => 'Ataşehir, Ümraniye, Kadıköy, Maltepe, Kartal, Pendik, Tuzla, Şile, Üsküdar, Sancaktepe, Sultanbeyli, Beykoz, Çekmeköy'],
            ['id' => '36', 'name' => 'İzmir', 'plate_number' => '35', 'phone_code' => '232', 'tax_offices' => 'Bornova, Çakabey, Kordon, Hasan Tahsin, İzmir İhtisas, 9 Eylül, Yamanlar, Karşıyaka, Kemeraltı, Konak, Şirinyer, Kadifekale, Taşıtlar, Belkahve, Balçova, Gaziemir, Ege, Çiğli, Bayındır, Bergama, Menemen, Ödemiş, Tire, Torbalı, Kemalpaşa, Urla, Selçuk, Kınık, Kiraz, Çeşme, Aliağa, Menderes, Dikili, Foça, Karaburun, Seferihisar, Beydağ'],
            ['id' => '37', 'name' => 'Kars', 'plate_number' => '36', 'phone_code' => '474', 'tax_offices' => 'Kars, Arpaçay, Digor, Kağızman, Sarıkamış, Selim, Susuz, Akyaka'],
            ['id' => '38', 'name' => 'Kastamonu', 'plate_number' => '37', 'phone_code' => '366', 'tax_offices' => 'Kastamonu, Tosya, Taşköprü, Araç, Azdavay, Bozkurt, Cide, Çatalzeytin, Daday, Devrekani, İnebolu, Küre, Abana, İhsangazi, Pınarbaşı, Şenpazar, Ağlı, Doğanyurt, Hanönü, Seydiler'],
            ['id' => '39', 'name' => 'Kayseri', 'plate_number' => '38', 'phone_code' => '352', 'tax_offices' => 'Kayseri İhtisas, Mimar Sinan, Erciyes, Kaleönü, Gevher Nesibe, Develi, Pınarbaşı, Bünyan, Felahiye, İncesu, Sarıoğlan, Sarız, Tomarza, Yahyalı, Yeşilhisar, Akkışla, Hacılar, Özvatan'],
            ['id' => '40', 'name' => 'Kırklareli', 'plate_number' => '39', 'phone_code' => '288', 'tax_offices' => 'Kırklareli, Lüleburgaz, Babaeski, Demirköy, Kofçaz, Pehlivanköy, Pınarhisar, Vize'],
            ['id' => '41', 'name' => 'Kırşehir', 'plate_number' => '40', 'phone_code' => '386', 'tax_offices' => 'Kırşehir, Kaman, Çiçekdağı, Mucur, Akpınar, Akçakent, Boztepe'],
            ['id' => '42', 'name' => 'Kocaeli', 'plate_number' => '41', 'phone_code' => '262', 'tax_offices' => 'Kocaeli İhtisas, Tepecik, Alemdar, Gebze İhtisas, Acısu, Uluçınar, İlyasbey, Gölcük, Karamürsel, Körfez, Derince, Kandıra'],
            ['id' => '43', 'name' => 'Konya', 'plate_number' => '42', 'phone_code' => '332', 'tax_offices' => 'Konya İhtisas, Selçuk, Mevlana, Meram, Alaaddin, Akşehir, Ereğli, Beyşehir, Cihanbeyli, Çumra, Seydişehir, Ilgın, Kulu, Karapınar, Bozkır, Doğanhisar, Hadim, Kadınhanı, Sarayönü, Yunak, Akören, Altınekin, Derebucak, Hüyük, Taşkent, Emirgazi, Güneysınır, Halkapınar, Tuzlukçu, Ahırlı, Çeltik, Derbent, Yalıhüyük'],
            ['id' => '44', 'name' => 'Kütahya', 'plate_number' => '43', 'phone_code' => '274', 'tax_offices' => '30 Ağustos, Çinili, Gediz, Simav, Tavşanlı, Emet, Altıntaş, Domaniç, Aslanapa, Dumlupınar, Hisarcık, Şaphane, Çavdarhisar, Pazarlar'],
            ['id' => '45', 'name' => 'Malatya', 'plate_number' => '44', 'phone_code' => '422', 'tax_offices' => 'Fırat, Beydağı, Akçadağ, Arapgir, Arguvan, Darende, Doğanşehir, Hekimhan, Pütürge, Yeşilyurt, Battalgazi, Doğanyol, Kale, Kuluncak, Yazıhan'],
            ['id' => '46', 'name' => 'Manisa', 'plate_number' => '45', 'phone_code' => '236', 'tax_offices' => 'Manisa İhtisas, Alaybey, Mesir, Akhisar, Alaşehir, Demirci, Kırkağaç, Salihli Adil Oral, Sarıgöl, Saruhanlı, Soma, Turgutlu, Gördes, Kula, Selendi, Ahmetli, Gölmarmara, Köprübaşı'],
            ['id' => '47', 'name' => 'Kahramanmaraş', 'plate_number' => '46', 'phone_code' => '344', 'tax_offices' => 'Aslanbey, Aksu, Elbistan, Afşin, Pazarcık, Andırın, Göksun, Türkoğlu, Çağlayancerit, Ekinözü, Nurhak'],
            ['id' => '48', 'name' => 'Mardin', 'plate_number' => '47', 'phone_code' => '482', 'tax_offices' => 'Mardin, Kızıltepe, Nusaybin, Derik, Mazıdağı, Midyat, Ömerli, Savur, Dargeçit, Yeşilli'],
            ['id' => '49', 'name' => 'Muğla', 'plate_number' => '48', 'phone_code' => '252', 'tax_offices' => 'Muğla, Bodrum, Fethiye, Köyceğiz, Milas, Marmaris, Yatağan, Datça, Ula, Dalaman, Ortaca, Kavaklıdere, Seydikemer'],
            ['id' => '50', 'name' => 'Muş', 'plate_number' => '49', 'phone_code' => '436', 'tax_offices' => 'Muş, Bulanık, Malazgirt, Varto, Hasköy, Korkut'],
            ['id' => '51', 'name' => 'Nevşehir', 'plate_number' => '50', 'phone_code' => '384', 'tax_offices' => 'Nevşehir, Avanos, Derinkuyu, Gülşehir, Hacıbektaş, Kozaklı, Ürgüp, Acıgöl'],
            ['id' => '52', 'name' => 'Niğde', 'plate_number' => '51', 'phone_code' => '388', 'tax_offices' => 'Niğde, Bor, Çamardı, Ulukışla, Altunhisar, Çiftlik'],
            ['id' => '53', 'name' => 'Ordu', 'plate_number' => '52', 'phone_code' => '452', 'tax_offices' => 'Boztepe, Köprübaşı, Fatsa, Ünye, Akkuş, Aybastı, Gölköy, Korgan, Kumru, Mesudiye, Perşembe, Ulubey, Gülyalı, Gürgentepe, Çamaş, Çatalpınar, Çaybaşı, İkizce, Kabadüz, Kabataş'],
            ['id' => '54', 'name' => 'Rize', 'plate_number' => '53', 'phone_code' => '464', 'tax_offices' => 'Kaçkar, Yeşilçay, Çayeli, Pazar, Ardeşen, Çamlıhemşin, Fındıklı, İkizdere, Kalkandere, Güneysu, Derepazarı, Hemşin, İyidere'],
            ['id' => '55', 'name' => 'Sakarya', 'plate_number' => '54', 'phone_code' => '264', 'tax_offices' => 'Gümrükönü, Ali Fuat Cebesoy, Sapanca, Akyazı, Geyve, Hendek, Karasu, Kaynarca, Kocaali, Pamukova, Taraklı, Karapürçek'],
            ['id' => '56', 'name' => 'Samsun', 'plate_number' => '55', 'phone_code' => '362', 'tax_offices' => '19 Mayıs, Gaziler, Zafer, Bafra, Çarşamba, Terme, Havza, Alaçam, Kavak, Ladik, Vezirköprü, Asarcık, Ondokuz Mayıs, Salıpazarı, Tekkeköy, Ayvacık, Yakakent'],
            ['id' => '57', 'name' => 'Siirt', 'plate_number' => '56', 'phone_code' => '484', 'tax_offices' => 'Siirt, Baykan, Eruh, Kurtalan, Pervari, Şirvan'],
            ['id' => '58', 'name' => 'Sinop', 'plate_number' => '57', 'phone_code' => '368', 'tax_offices' => 'Sinop, Boyabat, Ayancık, Durağan, Erfelek, Gerze, Türkeli, Dikmen, Saraydüzü'],
            ['id' => '59', 'name' => 'Sivas', 'plate_number' => '58', 'phone_code' => '346', 'tax_offices' => 'Kale, Site, Şarkışla, Divriği, Gemerek, Gürün, Hafik, İmranlı, Kangal, Koyulhisar, Suşehri, Yıldızeli, Zara, Akıncılar, Altınyayla, Doğanşar, Gölova, Ulaş'],
            ['id' => '60', 'name' => 'Tekirdağ', 'plate_number' => '59', 'phone_code' => '282', 'tax_offices' => 'Süleymanpaşa, Namık Kemal, Çerkezköy, Çorlu, Hayrabolu, Malkara, Muratlı, Saray, Şarköy, Marmara Ereğlisi'],
            ['id' => '61', 'name' => 'Tokat', 'plate_number' => '60', 'phone_code' => '356', 'tax_offices' => 'Tokat, Erbaa, Niksar, Turhal, Zile, Almus, Artova, Reşadiye, Pazar, Yeşilyurt, Başçiftlik, Sulusaray'],
            ['id' => '62', 'name' => 'Trabzon', 'plate_number' => '61', 'phone_code' => '462', 'tax_offices' => 'Hızırbey, Karadeniz, Akçaabat, Of, Vakfıkebir, Araklı, Arsin, Çaykara, Maçka, Sürmene, Tonya, Yomra, Beşikdüzü, Şalpazarı, Çarşıbaşı, Dernekpazarı, Düzköy, Hayrat, Köprübaşı'],
            ['id' => '63', 'name' => 'Tunceli', 'plate_number' => '62', 'phone_code' => '428', 'tax_offices' => 'Tunceli, Çemişgezek, Hozat, Mazgirt, Nazimiye, Ovacık, Pertek, Pülümür'],
            ['id' => '64', 'name' => 'Şanlıurfa', 'plate_number' => '63', 'phone_code' => '414', 'tax_offices' => 'Şehitlik, Topçu Meydanı, Siverek, Viranşehir, Birecik, Akçakale, Bozova, Halfeti, Hilvan, Suruç, Ceylanpınar, Harran'],
            ['id' => '65', 'name' => 'Uşak', 'plate_number' => '64', 'phone_code' => '276', 'tax_offices' => 'Uşak, Banaz, Eşme, Karahallı, Ulubey, Sivaslı'],
            ['id' => '66', 'name' => 'Van', 'plate_number' => '65', 'phone_code' => '432', 'tax_offices' => 'Van, Erciş, Başkale, Çatak, Gevaş, Gürpınar, Muradiye, Özalp, Bahçesaray, Çaldıran, Edremit, Saray'],
            ['id' => '67', 'name' => 'Yozgat', 'plate_number' => '66', 'phone_code' => '354', 'tax_offices' => 'Yozgat, Boğazlıyan, Sorgun, Yerköy, Akdağmadeni, Çayıralan, Çekerek, Sarıkaya, Şefaatli, Aydıncık, Çandır, Kadışehri, Saraykent, Yenifakılı'],
            ['id' => '68', 'name' => 'Zonguldak', 'plate_number' => '67', 'phone_code' => '372', 'tax_offices' => 'Uzunmehmet, Kara Elmas, Ereğli, Çaycuma, Devrek, Alaplı, Gökçebey'],
            ['id' => '69', 'name' => 'Aksaray', 'plate_number' => '68', 'phone_code' => '382', 'tax_offices' => 'Aksaray, Ağaçören, Güzelyurt, Ortaköy, Sarıyahşi, Eskil, Gülağaç'],
            ['id' => '70', 'name' => 'Bayburt', 'plate_number' => '69', 'phone_code' => '458', 'tax_offices' => 'Bayburt, Aydıntepe, Demirözü'],
            ['id' => '71', 'name' => 'Karaman', 'plate_number' => '70', 'phone_code' => '338', 'tax_offices' => 'Karaman, Ayrancı, Ermenek, Kazım Karabekir, Başyayla, Sarıveliler'],
            ['id' => '72', 'name' => 'Kırıkkale', 'plate_number' => '71', 'phone_code' => '318', 'tax_offices' => 'Irmak, Kaletepe, Delice, Keskin, Sulakyurt, Balışeyh, Çelebi, Karakeçili'],
            ['id' => '73', 'name' => 'Batman', 'plate_number' => '72', 'phone_code' => '488', 'tax_offices' => 'Batman, Beşiri, Gercüş, Hasankeyf, Kozluk, Sason'],
            ['id' => '74', 'name' => 'Şırnak', 'plate_number' => '73', 'phone_code' => '486', 'tax_offices' => 'Şırnak, Cizre, Silopi, Beytüşşebap, Güçlükonak, İdil, Uludere'],
            ['id' => '75', 'name' => 'Bartın', 'plate_number' => '74', 'phone_code' => '378', 'tax_offices' => 'Bartın, Amasra, Kurucaşile, Ulus'],
            ['id' => '76', 'name' => 'Ardahan', 'plate_number' => '75', 'phone_code' => '478', 'tax_offices' => 'Ardahan, Çıldır, Damal, Göle, Hanak, Posof'],
            ['id' => '77', 'name' => 'Iğdır', 'plate_number' => '76', 'phone_code' => '476', 'tax_offices' => 'Iğdır, Aralık, Karakoyunlu, Tuzluca'],
            ['id' => '78', 'name' => 'Yalova', 'plate_number' => '77', 'phone_code' => '226', 'tax_offices' => 'Yalova, Altınova, Armutlu, Çınarcık'],
            ['id' => '79', 'name' => 'Karabük', 'plate_number' => '78', 'phone_code' => '372', 'tax_offices' => 'Karabük, Safranbolu, Eflani, Eskipazar, Ovacık, Yenice'],
            ['id' => '80', 'name' => 'Kilis', 'plate_number' => '79', 'phone_code' => '348', 'tax_offices' => 'Kilis'],
            ['id' => '81', 'name' => 'Osmaniye', 'plate_number' => '80', 'phone_code' => '322', 'tax_offices' => 'Osmaniye, Kadirli, Bahçe, Düziçi, Hasanbeyli, Sumbas, Toprakkale'],
            ['id' => '82', 'name' => 'Düzce', 'plate_number' => '81', 'phone_code' => '380', 'tax_offices' => 'Düzce, Akçakoca, Yığılca, Cumayeri, Gölyaka, Çilimli, Gümüşova, Kaynaşlı']
        ];
        foreach ($provinces as $province) {
            $province['created_at'] = now();
            $province['updated_at'] = now();
        }
        DB::table('provinces')->insert($provinces);
    }
}