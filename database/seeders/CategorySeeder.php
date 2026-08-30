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
            ['name' => 'Gaji Bulanan', 'icon' => 'banknotes', 'color' => '#10b981'],
            ['name' => 'Bonus & THR', 'icon' => 'gift', 'color' => '#f59e0b'],
            ['name' => 'Freelance & Side Job', 'icon' => 'code-bracket', 'color' => '#8b5cf6'],
            ['name' => 'Investasi & Bunga', 'icon' => 'arrow-trending-up', 'color' => '#06b6d4'],
            ['name' => 'Pemasukan Lainnya', 'icon' => 'wallet', 'color' => '#64748b'],
        ];

        foreach ($incomeCategories as $item) {
            Category::create([
                'user_id' => null,
                'type' => CategoryType::INCOME,
                'name' => $item['name'],
                'icon' => $item['icon'],
                'color' => $item['color'],
                'is_active' => true,
            ]);
        }

        // 2. Kategori Pengeluaran (Parent & Subkategori)
        $expenseGroups = [
            [
                'parent' => ['name' => 'Kebutuhan Pokok & Makan', 'icon' => 'shopping-cart', 'color' => '#ef4444'],
                'children' => ['Makan & Minum Harian', 'Belanja Sembako / Pasar', 'Air Galon & Gas', 'Snack & Kopi']
            ],
            [
                'parent' => ['name' => 'Tagihan & Rumah Tangga', 'icon' => 'bolt', 'color' => '#f97316'],
                'children' => ['Sewa Kos / Kontrakan', 'Listrik PLN', 'Internet & WiFi', 'Pulsa & Kuota', 'Iuran Kebersihan / Air']
            ],
            [
                'parent' => ['name' => 'Transportasi', 'icon' => 'truck', 'color' => '#eab308'],
                'children' => ['Bensin / BBM', 'Parkir & Tol', 'Ojek Online / Angkutan', 'Servis Kendaraan']
            ],
            [
                'parent' => ['name' => 'Lifestyle & Hiburan', 'icon' => 'sparkles', 'color' => '#a855f7'],
                'children' => ['Nongkrong & Kafe', 'Langganan Streaming', 'Gaming & Hobi', 'Liburan & Hiburan']
            ],
            [
                'parent' => ['name' => 'Belanja & Pribadi', 'icon' => 'shopping-bag', 'color' => '#ec4899'],
                'children' => ['Pakaian & Aksesoris', 'Barang Pribadi', 'Kebutuhan Rumah']
            ],
            [
                'parent' => ['name' => 'Kesehatan & Pribadi', 'icon' => 'heart', 'color' => '#14b8a6'],
                'children' => ['Obat & Vitamin', 'Dokter / Medis', 'Skincare & Grooming', 'Olahraga']
            ],
            [
                'parent' => ['name' => 'Keluarga & Sosial', 'icon' => 'user-group', 'color' => '#6366f1'],
                'children' => ['Kirim Orang Tua', 'Sedekah & Zakat', 'Kondangan & Kado']
            ],
            [
                'parent' => ['name' => 'Pengeluaran Lainnya', 'icon' => 'archive-box', 'color' => '#64748b'],
                'children' => ['Biaya Admin Bank', 'Pengeluaran Tak Terduga']
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