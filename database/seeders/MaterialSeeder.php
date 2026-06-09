<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Material;

class MaterialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $materials = [
            "Almond Milk (almonesia)", "Almond Milk (club sehat)", "Almond Milk (JF)", "Almond Powder",
            "Almond Skinless", "Almond Slice", "Apricot", "ARA", "Baby Jackfruit Floss ", "Baked Cashew",
            "Baked Cashew Gladly", "Blueberry", "Chia Seed", "Cholestrol shot", "Coconut Flakes",
            "Coconut Sugar", "Colagen", "Cranberry", "Daging Sapi Kriuk ", "Edamame", "Edamame (LIDIA)",
            "Gojiberry", "Golden Raisin", "Golden raisin jumbo biasa", "Golden Raisin Jumbo CS",
            "Granola", "Hazelnut", "Honey Garlic", "Jamur ", "Keripik apel", "keripik nanas",
            "Keripik Nangka", "Keripik Pisang", "Keripik Salak", "Ketan Hitam", "Kiwi", "Kremesan Hati Ayam ",
            "Kurma ", "Macadamia", "Matcha", "Natural Almond", "Okra ", "Pistachio", "Pistachio non baked",
            "pumpkin Chips", "Pumpkin Seed", "Roasted Almond", "Roasted Almond (Bu ani)", "Sayur Buncis",
            "Sayur Jagung", "Sayur Kentang/singkong", "Sayur Ubi madu", "Sayur Ubi ungu", "Sayur Wortel",
            "Serbuk Daging ", "Serbuk Telur ", "STROBERI", "Sunflower Seed", "Walnut"
        ];

        foreach ($materials as $index => $name) {
            Material::updateOrCreate(
                ['nama_material' => $name],
                [
                    'jenis_material' => 'Bahan Baku',
                    'stok_material' => 0,
                    'satuan' => 'kg',
                    'supplier' => '-'
                ]
            );
        }
    }
}
