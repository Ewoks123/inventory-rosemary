<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Material;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\MaterialExport;
use Barryvdh\DomPDF\Facade\Pdf;

class MaterialController extends Controller
{
    private function normalizeMaterialQuantity($quantity, $unit = 'kg')
    {
        $unit = strtolower(trim((string) $unit));
        if ($unit === 'gram' || $unit === 'g') {
            return (float) $quantity / 1000;
        }
        return (float) $quantity;
    }

    private function normalizeMaterialPrice($price)
    {
        $value = trim((string) $price);
        $value = preg_replace('/[^\d,\.\-]/u', '', $value);

        $hasComma = strpos($value, ',') !== false;
        $hasDot = strpos($value, '.') !== false;

        if ($hasComma && $hasDot) {
            // Indonesian format like 1.234,56 -> 1234.56
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
            // otherwise keep dot as decimal point
        }

        return $value === '' ? 0.0 : (float) $value;
    }

    public function index()
    {
        $materials = Material::with('logs')->get();
        foreach ($materials as $material) {
            $material->total_in = $material->logs->where('type', 'in')->sum(function($log) {
                return $this->normalizeMaterialQuantity($log->quantity, $log->unit);
            });
            $material->total_out = $material->logs->where('type', 'out')->sum(function($log) {
                return $this->normalizeMaterialQuantity($log->quantity, $log->unit);
            });
            $material->total_price = $material->logs->where('type', 'in')->sum(function($log) {
                return $this->normalizeMaterialPrice($log->price);
            });
            
            // Force stok_material to strictly match logs for consistent display
            $material->stok_material = $material->total_in - $material->total_out;
        }

        $masterMaterials = [
            'Almond Milk (almonesia)', 'Almond Milk (club sehat)', 'Almond Milk (JF)', 'Almond Powder', 'Almond Skinless', 'Almond Slice', 'Apricot', 'ARA', 'Baby Jackfruit Floss ', 'Baked Cashew', 'Baked Cashew Gladly', 'Blueberry', 'Chia Seed', 'Cholestrol shot', 'Coconut Flakes', 'Coconut Sugar', 'Colagen', 'Cranberry', 'Daging Sapi Kriuk ', 'Edamame', 'Edamame (LIDIA)', 'Gojiberry', 'Golden Raisin', 'Golden raisin jumbo biasa', 'Golden Raisin Jumbo CS', 'Granola', 'Hazelnut', 'Honey Garlic', 'Jamur ', 'Keripik apel', 'keripik nanas', 'Keripik Nangka', 'Keripik Pisang', 'Keripik Salak', 'Ketan Hitam', 'Kiwi', 'Kremesan Hati Ayam ', 'Kurma ', 'Macadamia', 'Matcha', 'Natural Almond', 'Okra ', 'Pistachio', 'Pistachio non baked', 'pumpkin Chips', 'Pumpkin Seed', 'Roasted Almond', 'Roasted Almond (Bu ani)', 'Sayur Buncis', 'Sayur Jagung', 'Sayur Kentang/singkong', 'Sayur Ubi madu', 'Sayur Ubi ungu', 'Sayur Wortel', 'Serbuk Daging ', 'Serbuk Telur ', 'STROBERI', 'Sunflower Seed', 'Walnut'
        ];

        $materials = $materials->sort(function($a, $b) use ($masterMaterials) {
            $posA = array_search($a->nama_material, $masterMaterials);
            $posB = array_search($b->nama_material, $masterMaterials);
            $posA = $posA === false ? 9999 : $posA;
            $posB = $posB === false ? 9999 : $posB;
            if ($posA == $posB) {
                return $a->id <=> $b->id;
            }
            return $posA <=> $posB;
        })->values();
        
        $totalIn = $materials->sum('total_in');
        $totalOut = $materials->sum('total_out');
        $totalSupply = $materials->sum(function($material) {
            return $material->logs->where('type', 'in')->sum(function($log) {
                return $this->normalizeMaterialPrice($log->price);
            });
        });
        $totalOutPrice = $materials->sum(function($material) {
            return $material->logs->where('type', 'out')->sum(function($log) {
                return $this->normalizeMaterialPrice($log->price);
            });
        });

        return view('material.stokmaterial-actual', compact('materials', 'totalIn', 'totalOut', 'totalSupply', 'totalOutPrice'));
    }

    public function menu()
    {
        return view('material.stokmaterial-menu');
    }

    public function addPharian()
    {
        $materials = Material::orderByRaw('TRIM(nama_material) asc')->get();
        return view('material.pharian-add', compact('materials'));
    }

    public function create()
    {
        $materials = Material::orderByRaw('TRIM(nama_material) asc')->get();
        return view('material.stokmaterial-add', compact('materials'));
    }

    public function report(\Illuminate\Http\Request $request)
    {
        $startDate = $request->input('start_date', date('Y-m-01'));
        $endDate = $request->input('end_date', date('Y-m-t'));

        $materials = Material::with(['logs' => function($query) use ($startDate, $endDate) {
            $query->whereBetween('date', [$startDate, $endDate]);
        }])->get();

        $materials->each(function($material) {
            $material->logs->transform(function($log) {
                $log->quantity = $this->normalizeMaterialQuantity($log->quantity, $log->unit);
                $log->unit = 'kg';
                return $log;
            });
        });

        $masterMaterials = [
            'Almond Milk (almonesia)', 'Almond Milk (club sehat)', 'Almond Milk (JF)', 'Almond Powder', 'Almond Skinless', 'Almond Slice', 'Apricot', 'ARA', 'Baby Jackfruit Floss ', 'Baked Cashew', 'Baked Cashew Gladly', 'Blueberry', 'Chia Seed', 'Cholestrol shot', 'Coconut Flakes', 'Coconut Sugar', 'Colagen', 'Cranberry', 'Daging Sapi Kriuk ', 'Edamame', 'Edamame (LIDIA)', 'Gojiberry', 'Golden Raisin', 'Golden raisin jumbo biasa', 'Golden Raisin Jumbo CS', 'Granola', 'Hazelnut', 'Honey Garlic', 'Jamur ', 'Keripik apel', 'keripik nanas', 'Keripik Nangka', 'Keripik Pisang', 'Keripik Salak', 'Ketan Hitam', 'Kiwi', 'Kremesan Hati Ayam ', 'Kurma ', 'Macadamia', 'Matcha', 'Natural Almond', 'Okra ', 'Pistachio', 'Pistachio non baked', 'pumpkin Chips', 'Pumpkin Seed', 'Roasted Almond', 'Roasted Almond (Bu ani)', 'Sayur Buncis', 'Sayur Jagung', 'Sayur Kentang/singkong', 'Sayur Ubi madu', 'Sayur Ubi ungu', 'Sayur Wortel', 'Serbuk Daging ', 'Serbuk Telur ', 'STROBERI', 'Sunflower Seed', 'Walnut'
        ];

        $materials = $materials->sort(function($a, $b) use ($masterMaterials) {
            $posA = array_search($a->nama_material, $masterMaterials);
            $posB = array_search($b->nama_material, $masterMaterials);
            $posA = $posA === false ? 9999 : $posA;
            $posB = $posB === false ? 9999 : $posB;
            if ($posA == $posB) {
                return $a->id <=> $b->id;
            }
            return $posA <=> $posB;
        })->values();

        $stokAwalRaw = \Illuminate\Support\Facades\DB::table('material_logs')
            ->select('material_id', 'type', 'unit', \Illuminate\Support\Facades\DB::raw('SUM(quantity) as total'))
            ->where('date', '<', $startDate)
            ->groupBy('material_id', 'type', 'unit')
            ->get();
        $stokAwalMap = [];
        foreach($stokAwalRaw as $row) {
            if(!isset($stokAwalMap[$row->material_id])) $stokAwalMap[$row->material_id] = 0;
            $normalizedQty = $this->normalizeMaterialQuantity($row->total, $row->unit);
            if($row->type == 'in') $stokAwalMap[$row->material_id] += $normalizedQty;
            else $stokAwalMap[$row->material_id] -= $normalizedQty;
        }

        if ($startDate == $endDate) {
            return view('material.stokmaterial-harian', compact('materials', 'startDate', 'endDate', 'stokAwalMap'));
        }

        return view('material.stokmaterial-report', compact('materials', 'startDate', 'endDate', 'stokAwalMap'));
    }

    public function exportExcel(Request $request)
    {
        $startDate = $request->input('start_date', date('Y-m-01'));
        $endDate = $request->input('end_date', date('Y-m-t'));
        return Excel::download(new MaterialExport($startDate, $endDate), 'Laporan_Material_'.$startDate.'_to_'.$endDate.'.xlsx');
    }

    public function exportPdf(Request $request)
    {
        $startDate = $request->input('start_date', date('Y-m-01'));
        $endDate = $request->input('end_date', date('Y-m-t'));

        $materials = Material::with(['logs' => function($query) use ($startDate, $endDate) {
            $query->whereBetween('date', [$startDate, $endDate]);
        }])->get();

        $materials->each(function($material) {
            $material->logs->transform(function($log) {
                $log->quantity = $this->normalizeMaterialQuantity($log->quantity, $log->unit);
                $log->unit = 'kg';
                return $log;
            });
        });

        $masterMaterials = [
            'Almond Milk (almonesia)', 'Almond Milk (club sehat)', 'Almond Milk (JF)', 'Almond Powder', 'Almond Skinless', 'Almond Slice', 'Apricot', 'ARA', 'Baby Jackfruit Floss ', 'Baked Cashew', 'Baked Cashew Gladly', 'Blueberry', 'Chia Seed', 'Cholestrol shot', 'Coconut Flakes', 'Coconut Sugar', 'Colagen', 'Cranberry', 'Daging Sapi Kriuk ', 'Edamame', 'Edamame (LIDIA)', 'Gojiberry', 'Golden Raisin', 'Golden raisin jumbo biasa', 'Golden Raisin Jumbo CS', 'Granola', 'Hazelnut', 'Honey Garlic', 'Jamur ', 'Keripik apel', 'keripik nanas', 'Keripik Nangka', 'Keripik Pisang', 'Keripik Salak', 'Ketan Hitam', 'Kiwi', 'Kremesan Hati Ayam ', 'Kurma ', 'Macadamia', 'Matcha', 'Natural Almond', 'Okra ', 'Pistachio', 'Pistachio non baked', 'pumpkin Chips', 'Pumpkin Seed', 'Roasted Almond', 'Roasted Almond (Bu ani)', 'Sayur Buncis', 'Sayur Jagung', 'Sayur Kentang/singkong', 'Sayur Ubi madu', 'Sayur Ubi ungu', 'Sayur Wortel', 'Serbuk Daging ', 'Serbuk Telur ', 'STROBERI', 'Sunflower Seed', 'Walnut'
        ];

        $materials = $materials->sort(function($a, $b) use ($masterMaterials) {
            $posA = array_search($a->nama_material, $masterMaterials);
            $posB = array_search($b->nama_material, $masterMaterials);
            $posA = $posA === false ? 9999 : $posA;
            $posB = $posB === false ? 9999 : $posB;
            if ($posA == $posB) {
                return $a->id <=> $b->id;
            }
            return $posA <=> $posB;
        })->values();

        $stokAwalRaw = \Illuminate\Support\Facades\DB::table('material_logs')
            ->select('material_id', 'type', 'unit', \Illuminate\Support\Facades\DB::raw('SUM(quantity) as total'))
            ->where('date', '<', $startDate)
            ->groupBy('material_id', 'type', 'unit')
            ->get();
        $stokAwalMap = [];
        foreach($stokAwalRaw as $row) {
            if(!isset($stokAwalMap[$row->material_id])) $stokAwalMap[$row->material_id] = 0;
            $normalizedQty = $this->normalizeMaterialQuantity($row->total, $row->unit);
            if($row->type == 'in') $stokAwalMap[$row->material_id] += $normalizedQty;
            else $stokAwalMap[$row->material_id] -= $normalizedQty;
        }

        if ($startDate == $endDate) {
            $pdf = Pdf::loadView('exports.material-harian', compact('materials', 'startDate', 'endDate', 'stokAwalMap'))->setPaper('a4', 'landscape');
        } else {
            $pdf = Pdf::loadView('exports.material', compact('materials', 'startDate', 'endDate', 'stokAwalMap'))->setPaper('a0', 'landscape');
        }
        
        return $pdf->download('Laporan_Material_'.$startDate.'_to_'.$endDate.'.pdf');
    }

    public function history()
    {
        $logs = \App\Models\MaterialLog::with('material')->latest()->get();
        return view('material.stokmaterial-history', compact('logs'));
    }

    public function pharian()
    {
        $logs = \App\Models\MaterialLog::with('material')
            ->where('type', 'out')
            ->latest()
            ->get();
        
        $totalIn = \App\Models\MaterialLog::where('type', 'in')->get()->sum(function($log) {
            return $this->normalizeMaterialQuantity($log->quantity, $log->unit);
        });
        $totalOut = \App\Models\MaterialLog::where('type', 'out')->get()->sum(function($log) {
            return $this->normalizeMaterialQuantity($log->quantity, $log->unit);
        });
        
        return view('material.pharian', compact('logs', 'totalIn', 'totalOut'));
    }

    public function storePharian(Request $request)
    {
        $request->merge(['price' => $this->normalizeMaterialPrice($request->price)]);

        $request->validate([
            'material_id' => 'required|exists:materials,id',
            'quantity' => 'required|numeric|min:0.001',
            'unit' => 'nullable|string|in:kg,gram',
            'date' => 'required|date',
            'price' => 'required|numeric',
        ]);

        $material = Material::findOrFail($request->material_id);
        $qtyInKg = $this->normalizeMaterialQuantity($request->quantity, $request->unit ?? 'kg');

        if ($material->stok_material < $qtyInKg) {
            return back()->withErrors(['quantity' => 'Stok material tidak mencukupi.'])->withInput();
        }

        $material->stok_material -= $qtyInKg;
        $material->save();

        \App\Models\MaterialLog::create([
            'material_id' => $material->id,
            'type' => 'out',
            'quantity' => $qtyInKg,
            'unit' => 'kg',
            'price' => $request->price,
            'date' => $request->date,
            'note' => 'Material Keluar Produksi → Produksi Harian'
        ]);

        return redirect()->route('materials.index')->with('success', 'Produksi harian berhasil disimpan.');
    }

    public function stockIn()
    {
        $dbMaterials = Material::orderBy('nama_material', 'asc')->pluck('nama_material')->toArray();
        $masterMaterials = [
            'Almond Milk (almonesia)', 'Almond Milk (club sehat)', 'Almond Milk (JF)', 'Almond Powder', 'Almond Skinless', 'Almond Slice', 'Apricot', 'ARA', 'Baby Jackfruit Floss ', 'Baked Cashew', 'Baked Cashew Gladly', 'Blueberry', 'Chia Seed', 'Cholestrol shot', 'Coconut Flakes', 'Coconut Sugar', 'Colagen', 'Cranberry', 'Daging Sapi Kriuk ', 'Edamame', 'Edamame (LIDIA)', 'Gojiberry', 'Golden Raisin', 'Golden raisin jumbo biasa', 'Golden Raisin Jumbo CS', 'Granola', 'Hazelnut', 'Honey Garlic', 'Jamur ', 'Keripik apel', 'keripik nanas', 'Keripik Nangka', 'Keripik Pisang', 'Keripik Salak', 'Ketan Hitam', 'Kiwi', 'Kremesan Hati Ayam ', 'Kurma ', 'Macadamia', 'Matcha', 'Natural Almond', 'Okra ', 'Pistachio', 'Pistachio non baked', 'pumpkin Chips', 'Pumpkin Seed', 'Roasted Almond', 'Roasted Almond (Bu ani)', 'Sayur Buncis', 'Sayur Jagung', 'Sayur Kentang/singkong', 'Sayur Ubi madu', 'Sayur Ubi ungu', 'Sayur Wortel', 'Serbuk Daging ', 'Serbuk Telur ', 'STROBERI', 'Sunflower Seed', 'Walnut'
        ];
        $allMaterials = array_unique(array_merge($masterMaterials, $dbMaterials));
        $allMaterials = array_map('trim', $allMaterials);
        usort($allMaterials, function($a, $b) {
            return strcasecmp($a, $b);
        });
        $materialPrices = Material::pluck('supplier', 'nama_material')->toArray();
        $materialPrices = array_combine(array_map('trim', array_keys($materialPrices)), array_values($materialPrices));

        return view('material.stokmaterial-add', compact('allMaterials', 'materialPrices'));
    }

    public function storeStockIn(Request $request)
    {
        $request->merge(['price' => $this->normalizeMaterialPrice($request->price)]);

        $request->validate([
            'nama_material' => 'required|string',
            'quantity' => 'required|numeric|min:0.001',
            'unit' => 'nullable|string|in:kg,gram',
            'date' => 'required|date',
            'price' => 'required|numeric',
        ]);

        $material = Material::firstOrCreate(
            ['nama_material' => $request->nama_material],
            ['jenis_material' => 'Bahan Baku', 'stok_material' => 0, 'satuan' => $request->unit ?? 'kg', 'kode_material' => '']
        );

        $qtyInKg = $this->normalizeMaterialQuantity($request->quantity, $request->unit ?? 'kg');

        $material->stok_material += $qtyInKg;
        $material->supplier = $request->price;
        $material->save();

        \App\Models\MaterialLog::create([
            'material_id' => $material->id,
            'type' => 'in',
            'quantity' => $qtyInKg,
            'unit' => 'kg',
            'price' => $request->price,
            'date' => $request->date,
            'note' => 'Stok Material Masuk'
        ]);

        return redirect()->route('materials.index')->with('success', 'Stok material berhasil ditambahkan.');
    }

    public function show($id)
    {
        $material = Material::findOrFail($id);
        return view('material.show', compact('material'));
    }

    public function edit($id)
    {
        $material = Material::findOrFail($id);
        return view('material.stokmaterial-edit', compact('material'));
    }

    public function update(Request $request, $id)
    {
        $material = Material::findOrFail($id);
        $oldStok = $material->stok_material;

        // Clean price from Indonesian number format before validation
        if ($request->filled('supplier')) {
            $request->merge([
                'supplier' => $this->normalizeMaterialPrice($request->supplier)
            ]);
        }

        $request->validate([
            'nama_material' => 'required|string|max:255',
            'jenis_material' => 'required|string|max:255',
            'stok_material' => 'required|numeric|min:0',
            'satuan' => 'required|string|max:50',
            'supplier' => 'nullable|numeric',
        ]);

        $diff = $request->stok_material - $oldStok;

        if ($diff != 0) {
            $latestIn = \App\Models\MaterialLog::where('material_id', $material->id)->where('type', 'in')->latest('id')->first();
            if ($latestIn) {
                $latestIn->quantity += $diff;
                if ($latestIn->quantity < 0) {
                    $latestIn->quantity = 0;
                }
                $latestIn->save();
            } else {
                if ($diff > 0) {
                    \App\Models\MaterialLog::create([
                        'material_id' => $material->id,
                        'type' => 'in',
                        'quantity' => $diff,
                        'unit' => $material->satuan,
                        'price' => $material->supplier,
                        'date' => date('Y-m-d'),
                        'note' => 'Penyesuaian Stok (Edit Material)'
                    ]);
                }
            }
        }

        $material->update($request->only([
            'nama_material',
            'jenis_material',
            'stok_material',
            'satuan',
            'supplier',
        ]));

        return redirect()->route('materials.index')->with('success', 'Material berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $material = Material::findOrFail($id);
        $material->delete();

        return redirect()->route('materials.index')->with('success', 'Material berhasil dihapus.');
    }

    public function editPharian($id)
    {
        $log = \App\Models\MaterialLog::with('material')->findOrFail($id);
        $materials = Material::all();
        return view('material.pharian-edit', compact('log', 'materials'));
    }

    public function updatePharian(Request $request, $id)
    {
        $log = \App\Models\MaterialLog::findOrFail($id);
        $material = Material::findOrFail($log->material_id);

        // Clean price from dots (thousands separator)
        if ($request->filled('price')) {
            $request->merge([
                'price' => str_replace('.', '', $request->price)
            ]);
        }

        $request->merge(['price' => $this->normalizeMaterialPrice($request->price)]);

        $request->validate([
            'quantity' => 'required|numeric|min:0.001',
            'unit' => 'nullable|string|in:kg,gram',
            'date' => 'required|date',
            'price' => 'required|numeric',
        ]);

        $qtyOutKg = $this->normalizeMaterialQuantity($request->quantity, $request->unit ?? 'kg');

        // Adjust stock: reverse old, apply new
        $material->stok_material += $log->quantity; // Reverse old out
        if ($material->stok_material < $qtyOutKg) {
             return back()->withErrors(['quantity' => 'Stok material tidak mencukupi.'])->withInput();
        }
        $material->stok_material -= $qtyOutKg; // Apply new out
        $material->save();

        $log->update([
            'quantity' => $qtyOutKg,
            'unit' => 'kg',
            'price' => $request->price,
            'date' => $request->date,
        ]);

        return redirect()->route('materials.pharian')->with('success', 'Produksi harian berhasil diperbarui.');
    }
}
