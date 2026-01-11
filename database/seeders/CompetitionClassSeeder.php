<?php

namespace Database\Seeders;

use App\Models\Cabor;
use App\Models\CompetitionClass;
use Illuminate\Database\Seeder;

class CompetitionClassSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Competition classes per cabor (based on common Indonesian sports categories)
        $competitionClasses = [
            // 1. Sepak Bola
            'Sepak Bola' => [
                ['name' => 'Senior Putra', 'code' => 'SP'],
                ['name' => 'Senior Putri', 'code' => 'SPt'],
                ['name' => 'U-23', 'code' => 'U23'],
                ['name' => 'U-20', 'code' => 'U20'],
                ['name' => 'U-17', 'code' => 'U17'],
                ['name' => 'U-15', 'code' => 'U15'],
            ],
            
            // 2. Bulu Tangkis
            'Bulu Tangkis' => [
                ['name' => 'Tunggal Putra', 'code' => 'TP'],
                ['name' => 'Tunggal Putri', 'code' => 'TPt'],
                ['name' => 'Ganda Putra', 'code' => 'GP'],
                ['name' => 'Ganda Putri', 'code' => 'GPt'],
                ['name' => 'Ganda Campuran', 'code' => 'GC'],
                ['name' => 'Beregu Putra', 'code' => 'BP'],
                ['name' => 'Beregu Putri', 'code' => 'BPt'],
            ],
            
            // 3. Tenis Meja
            'Tenis Meja' => [
                ['name' => 'Tunggal Putra', 'code' => 'TP'],
                ['name' => 'Tunggal Putri', 'code' => 'TPt'],
                ['name' => 'Ganda Putra', 'code' => 'GP'],
                ['name' => 'Ganda Putri', 'code' => 'GPt'],
                ['name' => 'Ganda Campuran', 'code' => 'GC'],
                ['name' => 'Beregu Putra', 'code' => 'BP'],
                ['name' => 'Beregu Putri', 'code' => 'BPt'],
            ],
            
            // 4. Renang
            'Renang' => [
                ['name' => '50m Gaya Bebas Putra', 'code' => '50FR-P'],
                ['name' => '50m Gaya Bebas Putri', 'code' => '50FR-Pt'],
                ['name' => '100m Gaya Bebas Putra', 'code' => '100FR-P'],
                ['name' => '100m Gaya Bebas Putri', 'code' => '100FR-Pt'],
                ['name' => '200m Gaya Bebas Putra', 'code' => '200FR-P'],
                ['name' => '200m Gaya Bebas Putri', 'code' => '200FR-Pt'],
                ['name' => '100m Gaya Punggung Putra', 'code' => '100BA-P'],
                ['name' => '100m Gaya Punggung Putri', 'code' => '100BA-Pt'],
                ['name' => '100m Gaya Dada Putra', 'code' => '100BR-P'],
                ['name' => '100m Gaya Dada Putri', 'code' => '100BR-Pt'],
                ['name' => '100m Gaya Kupu-kupu Putra', 'code' => '100BT-P'],
                ['name' => '100m Gaya Kupu-kupu Putri', 'code' => '100BT-Pt'],
            ],
            
            // 5. Atletik
            'Atletik' => [
                ['name' => '100m Putra', 'code' => '100-P'],
                ['name' => '100m Putri', 'code' => '100-Pt'],
                ['name' => '200m Putra', 'code' => '200-P'],
                ['name' => '200m Putri', 'code' => '200-Pt'],
                ['name' => '400m Putra', 'code' => '400-P'],
                ['name' => '400m Putri', 'code' => '400-Pt'],
                ['name' => '800m Putra', 'code' => '800-P'],
                ['name' => '1500m Putra', 'code' => '1500-P'],
                ['name' => 'Lompat Jauh Putra', 'code' => 'LJ-P'],
                ['name' => 'Lompat Jauh Putri', 'code' => 'LJ-Pt'],
                ['name' => 'Lompat Tinggi Putra', 'code' => 'LT-P'],
                ['name' => 'Lompat Tinggi Putri', 'code' => 'LT-Pt'],
                ['name' => 'Tolak Peluru Putra', 'code' => 'TPL-P'],
                ['name' => 'Tolak Peluru Putri', 'code' => 'TPL-Pt'],
                ['name' => 'Lempar Cakram Putra', 'code' => 'LC-P'],
                ['name' => 'Lempar Lembing Putra', 'code' => 'LL-P'],
            ],
            
            // 6. Basket
            'Basket' => [
                ['name' => 'Putra', 'code' => 'P'],
                ['name' => 'Putri', 'code' => 'Pt'],
                ['name' => '3x3 Putra', 'code' => '3P'],
                ['name' => '3x3 Putri', 'code' => '3Pt'],
            ],
            
            // 7. Voli
            'Voli' => [
                ['name' => 'Indoor Putra', 'code' => 'IP'],
                ['name' => 'Indoor Putri', 'code' => 'IPt'],
                ['name' => 'Pasir Putra', 'code' => 'PP'],
                ['name' => 'Pasir Putri', 'code' => 'PPt'],
            ],
            
            // 8. Tinju (by weight class)
            'Tinju' => [
                ['name' => '48kg Putra - Light Flyweight', 'code' => '48P'],
                ['name' => '51kg Putra - Flyweight', 'code' => '51P'],
                ['name' => '54kg Putra - Bantamweight', 'code' => '54P'],
                ['name' => '57kg Putra - Featherweight', 'code' => '57P'],
                ['name' => '60kg Putra - Lightweight', 'code' => '60P'],
                ['name' => '63.5kg Putra - Light Welterweight', 'code' => '63P'],
                ['name' => '67kg Putra - Welterweight', 'code' => '67P'],
                ['name' => '71kg Putra - Light Middleweight', 'code' => '71P'],
                ['name' => '75kg Putra - Middleweight', 'code' => '75P'],
                ['name' => '80kg Putra - Light Heavyweight', 'code' => '80P'],
                ['name' => '50kg Putri', 'code' => '50Pt'],
                ['name' => '54kg Putri', 'code' => '54Pt'],
                ['name' => '57kg Putri', 'code' => '57Pt'],
                ['name' => '60kg Putri', 'code' => '60Pt'],
            ],
            
            // 9. Taekwondo
            'Taekwondo' => [
                ['name' => '-54kg Putra', 'code' => '54P'],
                ['name' => '-58kg Putra', 'code' => '58P'],
                ['name' => '-63kg Putra', 'code' => '63P'],
                ['name' => '-68kg Putra', 'code' => '68P'],
                ['name' => '-74kg Putra', 'code' => '74P'],
                ['name' => '-80kg Putra', 'code' => '80P'],
                ['name' => '-87kg Putra', 'code' => '87P'],
                ['name' => '+87kg Putra', 'code' => '87+P'],
                ['name' => '-46kg Putri', 'code' => '46Pt'],
                ['name' => '-49kg Putri', 'code' => '49Pt'],
                ['name' => '-53kg Putri', 'code' => '53Pt'],
                ['name' => '-57kg Putri', 'code' => '57Pt'],
                ['name' => '-62kg Putri', 'code' => '62Pt'],
                ['name' => '-67kg Putri', 'code' => '67Pt'],
                ['name' => '-73kg Putri', 'code' => '73Pt'],
                ['name' => '+73kg Putri', 'code' => '73+Pt'],
                ['name' => 'Poomsae Tunggal Putra', 'code' => 'PSP'],
                ['name' => 'Poomsae Tunggal Putri', 'code' => 'PSPt'],
            ],
            
            // 10. Karate
            'Karate' => [
                ['name' => 'Kata Perorangan Putra', 'code' => 'KPP'],
                ['name' => 'Kata Perorangan Putri', 'code' => 'KPPt'],
                ['name' => 'Kata Beregu Putra', 'code' => 'KBP'],
                ['name' => 'Kata Beregu Putri', 'code' => 'KBPt'],
                ['name' => 'Kumite -55kg Putra', 'code' => 'K55P'],
                ['name' => 'Kumite -60kg Putra', 'code' => 'K60P'],
                ['name' => 'Kumite -67kg Putra', 'code' => 'K67P'],
                ['name' => 'Kumite -75kg Putra', 'code' => 'K75P'],
                ['name' => 'Kumite +75kg Putra', 'code' => 'K75+P'],
                ['name' => 'Kumite -50kg Putri', 'code' => 'K50Pt'],
                ['name' => 'Kumite -55kg Putri', 'code' => 'K55Pt'],
                ['name' => 'Kumite -61kg Putri', 'code' => 'K61Pt'],
                ['name' => 'Kumite -68kg Putri', 'code' => 'K68Pt'],
                ['name' => 'Kumite +68kg Putri', 'code' => 'K68+Pt'],
            ],
            
            // 11. Pencak Silat
            'Pencak Silat' => [
                ['name' => 'Tunggal Putra', 'code' => 'TP'],
                ['name' => 'Tunggal Putri', 'code' => 'TPt'],
                ['name' => 'Ganda Putra', 'code' => 'GP'],
                ['name' => 'Ganda Putri', 'code' => 'GPt'],
                ['name' => 'Regu Putra', 'code' => 'RP'],
                ['name' => 'Regu Putri', 'code' => 'RPt'],
                ['name' => 'Tanding Kelas A Putra (45-50kg)', 'code' => 'TAP'],
                ['name' => 'Tanding Kelas B Putra (50-55kg)', 'code' => 'TBP'],
                ['name' => 'Tanding Kelas C Putra (55-60kg)', 'code' => 'TCP'],
                ['name' => 'Tanding Kelas D Putra (60-65kg)', 'code' => 'TDP'],
                ['name' => 'Tanding Kelas E Putra (65-70kg)', 'code' => 'TEP'],
                ['name' => 'Tanding Kelas F Putra (70-75kg)', 'code' => 'TFP'],
                ['name' => 'Tanding Kelas G Putra (75-80kg)', 'code' => 'TGP'],
                ['name' => 'Tanding Kelas A Putri (45-50kg)', 'code' => 'TAPt'],
                ['name' => 'Tanding Kelas B Putri (50-55kg)', 'code' => 'TBPt'],
                ['name' => 'Tanding Kelas C Putri (55-60kg)', 'code' => 'TCPt'],
            ],
            
            // 12. Judo
            'Judo' => [
                ['name' => '-60kg Putra', 'code' => '60P'],
                ['name' => '-66kg Putra', 'code' => '66P'],
                ['name' => '-73kg Putra', 'code' => '73P'],
                ['name' => '-81kg Putra', 'code' => '81P'],
                ['name' => '-90kg Putra', 'code' => '90P'],
                ['name' => '-100kg Putra', 'code' => '100P'],
                ['name' => '+100kg Putra', 'code' => '100+P'],
                ['name' => '-48kg Putri', 'code' => '48Pt'],
                ['name' => '-52kg Putri', 'code' => '52Pt'],
                ['name' => '-57kg Putri', 'code' => '57Pt'],
                ['name' => '-63kg Putri', 'code' => '63Pt'],
                ['name' => '-70kg Putri', 'code' => '70Pt'],
                ['name' => '-78kg Putri', 'code' => '78Pt'],
                ['name' => '+78kg Putri', 'code' => '78+Pt'],
            ],
            
            // 13. Angkat Besi
            'Angkat Besi' => [
                ['name' => '55kg Putra', 'code' => '55P'],
                ['name' => '61kg Putra', 'code' => '61P'],
                ['name' => '67kg Putra', 'code' => '67P'],
                ['name' => '73kg Putra', 'code' => '73P'],
                ['name' => '81kg Putra', 'code' => '81P'],
                ['name' => '89kg Putra', 'code' => '89P'],
                ['name' => '96kg Putra', 'code' => '96P'],
                ['name' => '102kg Putra', 'code' => '102P'],
                ['name' => '+102kg Putra', 'code' => '102+P'],
                ['name' => '45kg Putri', 'code' => '45Pt'],
                ['name' => '49kg Putri', 'code' => '49Pt'],
                ['name' => '55kg Putri', 'code' => '55Pt'],
                ['name' => '59kg Putri', 'code' => '59Pt'],
                ['name' => '64kg Putri', 'code' => '64Pt'],
                ['name' => '71kg Putri', 'code' => '71Pt'],
                ['name' => '76kg Putri', 'code' => '76Pt'],
                ['name' => '+76kg Putri', 'code' => '76+Pt'],
            ],
            
            // 14. Panahan
            'Panahan' => [
                ['name' => 'Recurve Perorangan Putra', 'code' => 'RPP'],
                ['name' => 'Recurve Perorangan Putri', 'code' => 'RPPt'],
                ['name' => 'Recurve Beregu Putra', 'code' => 'RBP'],
                ['name' => 'Recurve Beregu Putri', 'code' => 'RBPt'],
                ['name' => 'Compound Perorangan Putra', 'code' => 'CPP'],
                ['name' => 'Compound Perorangan Putri', 'code' => 'CPPt'],
                ['name' => 'Compound Beregu Putra', 'code' => 'CBP'],
                ['name' => 'Compound Beregu Putri', 'code' => 'CBPt'],
            ],
            
            // 15. Menembak
            'Menembak' => [
                ['name' => '10m Air Rifle Putra', 'code' => '10ARP'],
                ['name' => '10m Air Rifle Putri', 'code' => '10ARPt'],
                ['name' => '10m Air Pistol Putra', 'code' => '10APP'],
                ['name' => '10m Air Pistol Putri', 'code' => '10APPt'],
                ['name' => '25m Rapid Fire Pistol Putra', 'code' => '25RFPP'],
                ['name' => '25m Pistol Putri', 'code' => '25PPt'],
                ['name' => '50m Rifle 3 Positions Putra', 'code' => '50R3P'],
                ['name' => '50m Rifle 3 Positions Putri', 'code' => '50R3Pt'],
            ],
            
            // 16. Sepak Takraw
            'Sepak Takraw' => [
                ['name' => 'Regu Putra', 'code' => 'RP'],
                ['name' => 'Regu Putri', 'code' => 'RPt'],
                ['name' => 'Ganda Putra', 'code' => 'GP'],
                ['name' => 'Ganda Putri', 'code' => 'GPt'],
            ],
            
            // 17. Tenis Lapangan
            'Tenis Lapangan' => [
                ['name' => 'Tunggal Putra', 'code' => 'TP'],
                ['name' => 'Tunggal Putri', 'code' => 'TPt'],
                ['name' => 'Ganda Putra', 'code' => 'GP'],
                ['name' => 'Ganda Putri', 'code' => 'GPt'],
                ['name' => 'Ganda Campuran', 'code' => 'GC'],
            ],
            
            // 18. Golf
            'Golf' => [
                ['name' => 'Perorangan Putra', 'code' => 'PP'],
                ['name' => 'Perorangan Putri', 'code' => 'PPt'],
                ['name' => 'Beregu Putra', 'code' => 'BP'],
                ['name' => 'Beregu Putri', 'code' => 'BPt'],
            ],
            
            // 19. Biliar
            'Biliar' => [
                ['name' => '9-Ball Putra', 'code' => '9BP'],
                ['name' => '9-Ball Putri', 'code' => '9BPt'],
                ['name' => '10-Ball Putra', 'code' => '10BP'],
                ['name' => 'Carom Putra', 'code' => 'CP'],
                ['name' => 'Snooker Putra', 'code' => 'SP'],
            ],
            
            // 20. Catur
            'Catur' => [
                ['name' => 'Standar Putra', 'code' => 'STP'],
                ['name' => 'Standar Putri', 'code' => 'STPt'],
                ['name' => 'Cepat Putra', 'code' => 'CP'],
                ['name' => 'Cepat Putri', 'code' => 'CPt'],
                ['name' => 'Kilat Putra', 'code' => 'KP'],
                ['name' => 'Kilat Putri', 'code' => 'KPt'],
                ['name' => 'Beregu Putra', 'code' => 'BP'],
                ['name' => 'Beregu Putri', 'code' => 'BPt'],
            ],
            
            // 21. Panjat Tebing
            'Panjat Tebing' => [
                ['name' => 'Speed Putra', 'code' => 'SPP'],
                ['name' => 'Speed Putri', 'code' => 'SPPt'],
                ['name' => 'Lead Putra', 'code' => 'LP'],
                ['name' => 'Lead Putri', 'code' => 'LPt'],
                ['name' => 'Boulder Putra', 'code' => 'BP'],
                ['name' => 'Boulder Putri', 'code' => 'BPt'],
                ['name' => 'Kombinasi Putra', 'code' => 'KP'],
                ['name' => 'Kombinasi Putri', 'code' => 'KPt'],
            ],
            
            // 22. Senam
            'Senam' => [
                ['name' => 'Artistik All-Around Putra', 'code' => 'AAP'],
                ['name' => 'Artistik All-Around Putri', 'code' => 'AAPt'],
                ['name' => 'Artistik Lantai Putra', 'code' => 'ALP'],
                ['name' => 'Artistik Lantai Putri', 'code' => 'ALPt'],
                ['name' => 'Ritmik Perorangan', 'code' => 'RP'],
                ['name' => 'Ritmik Beregu', 'code' => 'RB'],
            ],
            
            // 23. Wushu
            'Wushu' => [
                ['name' => 'Changquan Putra', 'code' => 'CQP'],
                ['name' => 'Changquan Putri', 'code' => 'CQPt'],
                ['name' => 'Nanquan Putra', 'code' => 'NQP'],
                ['name' => 'Nanquan Putri', 'code' => 'NQPt'],
                ['name' => 'Taijiquan Putra', 'code' => 'TJP'],
                ['name' => 'Taijiquan Putri', 'code' => 'TJPt'],
                ['name' => 'Sanda -56kg Putra', 'code' => 'S56P'],
                ['name' => 'Sanda -60kg Putra', 'code' => 'S60P'],
                ['name' => 'Sanda -65kg Putra', 'code' => 'S65P'],
                ['name' => 'Sanda -52kg Putri', 'code' => 'S52Pt'],
                ['name' => 'Sanda -56kg Putri', 'code' => 'S56Pt'],
            ],
            
            // 24. Anggar
            'Anggar' => [
                ['name' => 'Foil Perorangan Putra', 'code' => 'FPP'],
                ['name' => 'Foil Perorangan Putri', 'code' => 'FPPt'],
                ['name' => 'Epee Perorangan Putra', 'code' => 'EPP'],
                ['name' => 'Epee Perorangan Putri', 'code' => 'EPPt'],
                ['name' => 'Sabre Perorangan Putra', 'code' => 'SPP'],
                ['name' => 'Sabre Perorangan Putri', 'code' => 'SPPt'],
                ['name' => 'Foil Beregu Putra', 'code' => 'FBP'],
                ['name' => 'Epee Beregu Putra', 'code' => 'EBP'],
            ],
            
            // 25. Gulat
            'Gulat' => [
                ['name' => 'Gaya Bebas -57kg Putra', 'code' => 'GB57P'],
                ['name' => 'Gaya Bebas -65kg Putra', 'code' => 'GB65P'],
                ['name' => 'Gaya Bebas -74kg Putra', 'code' => 'GB74P'],
                ['name' => 'Gaya Bebas -86kg Putra', 'code' => 'GB86P'],
                ['name' => 'Gaya Bebas -97kg Putra', 'code' => 'GB97P'],
                ['name' => 'Greco Roman -60kg', 'code' => 'GR60'],
                ['name' => 'Greco Roman -67kg', 'code' => 'GR67'],
                ['name' => 'Greco Roman -77kg', 'code' => 'GR77'],
                ['name' => 'Greco Roman -87kg', 'code' => 'GR87'],
                ['name' => 'Gaya Bebas -50kg Putri', 'code' => 'GB50Pt'],
                ['name' => 'Gaya Bebas -53kg Putri', 'code' => 'GB53Pt'],
                ['name' => 'Gaya Bebas -57kg Putri', 'code' => 'GB57Pt'],
            ],
            
            // 26. Futsal
            'Futsal' => [
                ['name' => 'Putra', 'code' => 'P'],
                ['name' => 'Putri', 'code' => 'Pt'],
            ],
            
            // 27. E-Sports
            'E-Sports' => [
                ['name' => 'Mobile Legends', 'code' => 'ML'],
                ['name' => 'PUBG Mobile', 'code' => 'PUBGM'],
                ['name' => 'Free Fire', 'code' => 'FF'],
                ['name' => 'Valorant', 'code' => 'VAL'],
                ['name' => 'eFootball', 'code' => 'EF'],
                ['name' => 'Dota 2', 'code' => 'DOTA'],
            ],
            
            // 28. Balap Sepeda
            'Balap Sepeda' => [
                ['name' => 'Road Race Putra', 'code' => 'RRP'],
                ['name' => 'Road Race Putri', 'code' => 'RRPt'],
                ['name' => 'Time Trial Putra', 'code' => 'TTP'],
                ['name' => 'Time Trial Putri', 'code' => 'TTPt'],
                ['name' => 'Track Sprint Putra', 'code' => 'TSP'],
                ['name' => 'Track Sprint Putri', 'code' => 'TSPt'],
                ['name' => 'MTB Cross Country Putra', 'code' => 'MTBP'],
                ['name' => 'MTB Cross Country Putri', 'code' => 'MTBPt'],
                ['name' => 'BMX Racing Putra', 'code' => 'BMXP'],
                ['name' => 'BMX Racing Putri', 'code' => 'BMXPt'],
            ],
            
            // 29. Petanque
            'Petanque' => [
                ['name' => 'Tunggal Putra', 'code' => 'TP'],
                ['name' => 'Tunggal Putri', 'code' => 'TPt'],
                ['name' => 'Ganda Putra', 'code' => 'GP'],
                ['name' => 'Ganda Putri', 'code' => 'GPt'],
                ['name' => 'Trio Putra', 'code' => 'TRP'],
                ['name' => 'Trio Putri', 'code' => 'TRPt'],
                ['name' => 'Shooting Putra', 'code' => 'SP'],
                ['name' => 'Shooting Putri', 'code' => 'SPt'],
            ],
            
            // 30. Muaythai
            'Muaythai' => [
                ['name' => '-51kg Putra', 'code' => '51P'],
                ['name' => '-54kg Putra', 'code' => '54P'],
                ['name' => '-57kg Putra', 'code' => '57P'],
                ['name' => '-60kg Putra', 'code' => '60P'],
                ['name' => '-63.5kg Putra', 'code' => '63P'],
                ['name' => '-67kg Putra', 'code' => '67P'],
                ['name' => '-71kg Putra', 'code' => '71P'],
                ['name' => '-48kg Putri', 'code' => '48Pt'],
                ['name' => '-51kg Putri', 'code' => '51Pt'],
                ['name' => '-54kg Putri', 'code' => '54Pt'],
                ['name' => '-57kg Putri', 'code' => '57Pt'],
            ],
        ];

        // Seed competition classes
        foreach ($competitionClasses as $caborName => $classes) {
            $cabor = Cabor::where('name', $caborName)->first();
            
            if ($cabor) {
                foreach ($classes as $class) {
                    CompetitionClass::updateOrCreate(
                        [
                            'cabor_id' => $cabor->id,
                            'name' => $class['name'],
                        ],
                        [
                            'code' => $class['code'] ?? null,
                            'description' => $class['description'] ?? null,
                            'is_active' => true,
                        ]
                    );
                }
            }
        }
    }
}
