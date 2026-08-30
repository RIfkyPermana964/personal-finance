<?php

namespace Database\Seeders;

use App\Enums\CategoryType;
use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        // 1. Kategori Pemasukan
        $incomeCategories = [
            ['name' => 'Gaji Pokok NOC', 'icon' => 'banknotes', 'color' => '#10b981'],
            ['name' => 'Insentif & On-Call Shift', 'icon' => 'bolt', 'color' => '#06b6d4'],
            ['name' => 'Bonus & THR', 'icon' => 'gift', 'color' => '#f59e0b'],
            ['name' => 'Freelance & Side Project', 'icon' => 'code-bracket', 'color' => '#8b5cf6'],
            ['name' => 'Cashback & Investasi', 'icon' => 'arrow-trending-up', 'color' => '#ec4899'],
            ['name' => 'Pemasukan Lainnya', 'icon' => 'wallet', 'color' => '#64748b'],
        ];

        foreach ($incomeCategories as $item) {
            Category::create([
                'user_id' => null, // Master Global
                'type' => CategoryType::INCOME,
                'name' => $item['name'],
                'icon' => $item['icon'],
                'color' => $item['color'],
                'is_active' => true,
            ]);
        }

        // 2. Kategori Pengeluaran (Parent & Children)
        $expenseGroups = [
            [
                'parent' => ['name' => 'Kebutuhan Pokok & Makan', 'icon' => 'shopping-cart', 'color' => '#ef4444'],
                'children' => ['Makan & Minum Harian', 'Belanja Sembako / Pasar', 'Air Mineral & Galon', 'Snack & Kopi']
            ],
            [
                'parent' => ['name' => 'Tagihan & Utilitas', 'icon' => 'bolt', 'color' => '#f97316'],
                'children' => ['Listrik PLN (Token/Pascabayar)', 'Internet Rumah (IndiHome/Biznet/Oxygen)', 'Pulsa & Kuota Seluler', 'Iuran Air & Sampah']
            ],
            [
                'parent' => ['name' => 'Transportasi & Operasional', 'icon' => 'truck', 'color' => '#eab308'],
                'children' => ['Bensin & BBM', 'Parkir & Tol', 'Ojek Online (Gojek/Grab)', 'Servis Motor/Mobil']
            ],
            [
                'parent' => ['name' => 'Pekerjaan & Tools NOC', 'icon' => 'server', 'color' => '#3b82f6'],
                'children' => ['Peralatan Jaringan (Crimping/LAN/SFP)', 'Sertifikasi (MTCNA/CCNA/JNCIA)', 'Buku / Kursus Teknis', 'Langganan Server / Cloud / VPS']
            ],
            [
                'parent' => ['name' => 'Lifestyle & Hiburan', 'icon' => 'sparkles', 'color' => '#a855f7'],
                'children' => ['Nongkrong & Cafe', 'Streaming (Netflix/Spotify/YouTube)', 'Gaming & Voucher', 'Nonton Bioskop']
            ],
            [
                'parent' => ['name' => 'Belanja & Gadget', 'icon' => 'shopping-bag', 'color' => '#ec4899'],
                'children' => ['Pakaian & Sepatu', 'Aksesoris Elektronik & Gadget', 'Kebutuhan Rumah Tangga']
            ],
            [
                'parent' => ['name' => 'Kesehatan & Pribadi', 'icon' => 'heart', 'color' => '#14b8a6'],
                'children' => ['Obat & Vitamin', 'Dokter & Medis', 'Skincare & Grooming', 'Olahraga / Gym']
            ],
            [
                'parent' => ['name' => 'Sosial & Keluarga', 'icon' => 'user-group', 'color' => '#6366f1'],
                'children' => ['Uang untuk Orang Tua', 'Sedekah & Zakat', 'Kondangan & Hadiah', 'Traktir Rekan Kerja']
            ],
            [
                'parent' => ['name' => 'Pengeluaran Lainnya', 'icon' => 'archive-box', 'color' => '#64748b'],
                'children' => ['Biaya Admin Bank / Top Up', 'Pengeluaran Tak Terduga']
            ],
        ];

        foreach ($expenseGroups as $group) {
            $parent = Category::create([
                'user_id' => null,
                'type' => CategoryType::EXPENSE,
                'name' => $group['parent']['name'],
                'icon' => $group['parent']['icon'],
                'color' => $group['parent']['color'],
                'is_active' => true,
            ]);

            foreach ($group['children'] as $childName) {
                Category::create([
                    'user_id' => null,
                    'parent_id' => $parent->id,
                    'type' => CategoryType::EXPENSE,
                    'name' => $childName,
                    'icon' => 'minus',
                    'color' => $group['parent']['color'],
                    'is_active' => true,
                ]);
            }
        }
    }
}
