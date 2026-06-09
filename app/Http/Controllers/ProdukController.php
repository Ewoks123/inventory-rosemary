<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produk;
use App\Models\Penjualan;
use App\Models\Produksi;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ProdukExport;
use Barryvdh\DomPDF\Facade\Pdf;

class ProdukController extends Controller
{
    public function index()
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

        return view('produk.product', compact('produks'));
    }

    public function create()
    {
        $dbProduks = Produk::orderBy('nama_produk', 'asc')->pluck('nama_produk')->toArray();
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
        
        $allProduks = array_unique(array_merge($masterProduks, $dbProduks));

        return view('produk.product-add', compact('allProduks'));
    }

    private function normalizePrice($price)
    {
        $value = trim((string) $price);
        $value = preg_replace('/[^\d,\.\-]/u', '', $value);

        $hasComma = strpos($value, ',') !== false;
        $hasDot = strpos($value, '.') !== false;

        if ($hasComma && $hasDot) {
            $value = str_replace('.', '', $value);
            $value = str_replace(',', '.', $value);
        } elseif ($hasComma) {
            if (preg_match('/^\d{1,3}(,\d{3})+$/', $value)) {
                $value = str_replace(',', '', $value);
            } else {
                $value = str_replace(',', '.', $value);
            }
        } elseif ($hasDot) {
            if (preg_match('/^\d{1,3}(\.\d{3})+$/', $value)) {
                $value = str_replace('.', '', $value);
            }
        }

        return $value === '' ? 0.0 : (float) $value;
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_produk' => 'required|string|max:255',
            'kategori' => 'required|string|max:255',
            'stok_produk' => 'required|numeric|min:0',
            'harga_produk' => 'required|numeric|min:0',
            'satuan' => 'required|string|max:50',
        ]);

        $request->merge(['harga_produk' => $this->normalizePrice($request->harga_produk)]);

        // Clean the input name: remove zero-width chars and trim
        $inputName = str_replace("\u{2060}", '', $request->nama_produk);
        $inputName = trim($inputName);

        // Search for product with cleaned name
        // We use a broader check or clean the database comparison
        $produk = Produk::where('nama_produk', $inputName)
            ->orWhere('nama_produk', 'LIKE', '%' . $inputName . '%')
            ->get()
            ->filter(function($p) use ($inputName) {
                $cleanDbName = str_replace("\u{2060}", '', $p->nama_produk);
                return trim($cleanDbName) === $inputName;
            })->first();

        if ($produk) {
            // Update existing product: add to stock and update details
            $produk->stok_produk += $request->stok_produk;
            $produk->harga_produk = $request->harga_produk;
            $produk->kategori = $request->kategori;
            $produk->satuan = $request->satuan;
            $produk->save();
            $msg = 'Stok produk "' . $produk->nama_produk . '" berhasil diperbarui.';
        } else {
            // Create new product
            Produk::create([
                'nama_produk' => $inputName,
                'kategori' => $request->kategori,
                'stok_produk' => $request->stok_produk,
                'harga_produk' => $request->harga_produk,
                'satuan' => $request->satuan,
                'tanggal_input' => now(),
            ]);
            $msg = 'Produk baru berhasil ditambahkan.';
        }

        return redirect()->route('produks.index')->with('success', $msg);
    }

    public function show($id)
    {
        $produk = Produk::findOrFail($id);
        return view('produk.product-detail', compact('produk'));
    }

    public function edit($id)
    {
        $produk = Produk::findOrFail($id);
        return view('produk.product-edit', compact('produk'));
    }

    public function update(Request $request, $id)
    {
        $produk = Produk::findOrFail($id);

        $request->validate([
            'nama_produk' => 'required|string|max:255',
            'kategori' => 'required|string|max:255',
            'stok_produk' => 'required|numeric|min:0',
            'harga_produk' => 'required|numeric|min:0',
            'satuan' => 'required|string|max:50',
        ]);

        $produk->update([
            'nama_produk' => $request->nama_produk,
            'kategori' => $request->kategori,
            'stok_produk' => $request->stok_produk,
            'harga_produk' => $request->harga_produk,
            'satuan' => $request->satuan,
        ]);

        return redirect()->route('produks.index')->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $produk = Produk::findOrFail($id);
        $produk->delete();

        return redirect()->route('produks.index')->with('success', 'Produk berhasil dihapus.');
    }

    public function report(\Illuminate\Http\Request $request)
    {
        $startDate = $request->input('start_date', date('Y-m-01'));
        $endDate = $request->input('end_date', date('Y-m-t'));

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
                    ->whereBetween('tanggal_penjualan', [$startDate, $endDate])
                    ->get();
        $produksis = \App\Models\Produksi::with(['produk', 'material'])
                    ->whereBetween('tanggal_produksi', [$startDate, $endDate])
                    ->get();
        
        $reportRoute = 'produks.report';
        $reportPdfRoute = 'produks.report.pdf';
        $reportExcelRoute = 'produks.report.excel';
        $reportBackRoute = 'produks.index';

        return view('produk.laporan', compact('produks', 'sales', 'produksis', 'startDate', 'endDate', 'reportRoute', 'reportPdfRoute', 'reportExcelRoute', 'reportBackRoute'));
    }

    public function exportExcel(Request $request)
    {
        $startDate = $request->input('start_date', date('Y-m-01'));
        $endDate = $request->input('end_date', date('Y-m-t'));
        return Excel::download(new ProdukExport($startDate, $endDate), 'Laporan_Produk_'.$startDate.'_to_'.$endDate.'.xlsx');
    }

    public function exportPdf(Request $request)
    {
        $startDate = $request->input('start_date', date('Y-m-01'));
        $endDate = $request->input('end_date', date('Y-m-t'));

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
                    ->whereBetween('tanggal_penjualan', [$startDate, $endDate])
                    ->get();
        $produksis = \App\Models\Produksi::with(['produk', 'material'])
                    ->whereBetween('tanggal_produksi', [$startDate, $endDate])
                    ->get();
        
        $pdf = Pdf::loadView('exports.produk', compact('produks', 'sales', 'produksis', 'startDate', 'endDate'))->setPaper('a0', 'landscape');
        return $pdf->download('Laporan_Produk_'.$startDate.'_to_'.$endDate.'.pdf');
    }
}
