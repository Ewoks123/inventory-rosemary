<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use App\Models\Produk;

class ProdukExport implements FromView, ShouldAutoSize
{
    protected $startDate;
    protected $endDate;

    public function __construct($startDate, $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function view(): View
    {
        $produks = Produk::all();
        
        $masterProduks = [
            "Almond Milk 160 gr", "Almond Milk 320 gr", "Almond Milk Matcha 160 gr", "Almond Milk Matcha 320 gr",
            "Almond Skinless 250", "Almond Sliced 250", "Almond Sliced 500", "Apricot 250", "Apricot 500", "Apricot 1000",
            "Baked Cashew Nut 300", "Blueberry 100", "Blueberry 250", "Blueberry 500", "Blueberry 1000", "Chia Seed 300",
            "ChocoPump", "Coconut Flakes 300", "Cranberry 100", "Cranberry 250", "Cranberry 500", "Cranberry 1000",
            "Gojiberry 100", "Gojiberry 250", "Gojiberry 500", "Gojiberry 1000", "Golden Raisin 300", "Hazelnut 100",
            "Hazelnut 250", "Hazelnut 500", "Honey Garlic Almond 1000", "Honey Garlic Almond 300", "Hurly Burly Nut 150",
            "Hurly Burly Nut 300", "Macadamia 100", "Macadamia 250", "Macadamia 500", "MASTERMIND LARGE - LARGE",
            "MASTERMIND LARGE - MEDIUM", "MASTERMIND LARGE - SMALL", "Natural Almond 100", "Natural Almond 1000",
            "Natural Almond 250", "Natural Almond 500", "Nutty Crunch 250", "Pistachio 100", "Pistachio 250",
            "Pistachio 500", "Pumpkin Seed 1000", "Pumpkin Seed 250", "Pumpkin Seed 500", "Roasted Almond 1000",
            "Roasted Almond 300", "Sunflower Seed 1000", "Sunflower Seed 250", "Sunflower Seed 500", "Walnut 100",
            "Walnut 250", "Walnut 500", "Walnut 1000", "Gladly Snack 180", "Nutty Crunch 115", "Roasted Almond 115",
            "Wonder Bar", "Honey Garlic Almond 115", "Nutty Crunch 100", "Roasted Almond 100", "Honey Garlic Almond 100",
            "Gladly Snack 100", "Apricot 100", "Sunflower 100", "Blueberry 50", "Cholesterol Pro-Shot", "Keripik buah",
            "Keripik sayur", "Keripik buah extra stroberi", "Keripik buah stroberi", "Keripik buah fig",
            "Keripik buah stroberi & fig", "Okra chips", "Vegy Baby", "Granola Macadamia", "Granola Pumpkin",
            "Teri Crispy", "Fruity Mix 1100 ML", "Fruity Mix Special", "Veggie Mix 1100 ML", "Paket Mix Foil",
            "Almond Milk Ketan Hitam 160 gr", "Almond Milk Ketan Hitam 320 gr", "Keripik Daging", "Serbuk Daging",
            "Serbuk Telur", "Serbuk Hati Ayam", "Serbuk Hati Sapi", "Nanas Chips", "Nangka Chips", "Salak Chips",
            "Pisang Chips", "Fruit Chips 1 kg", "Apel Chips", "Wortel Chips", "Ubi madu", "Ubi ungu", "Pumpkin Chips",
            "Baby Jackfruit", "Edamame 30 gr", "Edamame 350 ml", "Edamame 1100ml", "Edamame 500ml", "Edamame 200ml",
            "Edamame 300 ml", "Kiwi Chips", "Mushroom Chips", "Jujube Chips", "Golden Green Edamame 200ml",
            "Golden Green Edamame 500ml", "Cinnamon Banana Bite", "SP Okra", "Fruit Chips 350 ml", "Stroberi Chips 350 ml",
            "Fig Chips 350 ml", "Veggie Chips 350 ml", "Biscotti", "Fruit Chips 300 ml", "Veggie Chips 300 ml",
            "Strawberry Chips 300 ml", "Strawberry Chips 500 ml", "Garlic Chips", "Fruit Chips 500 ml", "Veggie Chips 500 ml",
            "Paket Resolusi Anti Diabetes", "Paket PCOS Reset 5", "Festive Season", "Velvet Treasure", "Symphony Serenade",
            "Violet Dawn", "Lavender Radiance Joy", "Starlit Noel", "Carousel d'or", "Hampers lebaran box taj mahal",
            "Hampers lebaran knot bag", "Hampers imlek tas maroon", "Hampers lebaran Nur Minar",
            "Hampers lebaran Humayyun / mini pouch", "Hampers lebaran Shahi Fort / anyaman",
            "Hampers lebaran Jahanara / mutiara", "Hampers lebaran Taj / Celestial / Taj Mahal",
            "Hampers lebaran \u2060Mughal Royal Legacy / Luxury", "Hampers lebaran IED Gourmet", "Rosemary Sky Gourmet"
        ];
        
        $produks = $produks->sort(function($a, $b) use ($masterProduks) {
            $posA = array_search($a->nama_produk, $masterProduks);
            $posB = array_search($b->nama_produk, $masterProduks);
            $posA = $posA === false ? 9999 : $posA;
            $posB = $posB === false ? 9999 : $posB;
            if ($posA == $posB) {
                return $a->id <=> $b->id;
            }
            return $posA <=> $posB;
        })->values();
        $sales = \App\Models\Penjualan::with('produk')
                    ->whereBetween('tanggal_penjualan', [$this->startDate, $this->endDate])
                    ->get();
        $produksis = \App\Models\Produksi::with(['produk', 'material'])
                    ->whereBetween('tanggal_produksi', [$this->startDate, $this->endDate])
                    ->get();
        
        return view('exports.produk', [
            'produks' => $produks,
            'sales' => $sales,
            'produksis' => $produksis,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate
        ]);
    }
}
