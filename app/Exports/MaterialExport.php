<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use App\Models\Material;

class MaterialExport implements FromView, ShouldAutoSize
{
    protected $startDate;
    protected $endDate;

    private function normalizeMaterialQuantity($quantity, $unit = 'kg')
    {
        $unit = strtolower(trim((string) $unit));
        if ($unit === 'gram' || $unit === 'g') {
            return (float) $quantity / 1000;
        }
        return (float) $quantity;
    }

    public function __construct($startDate, $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function view(): View
    {
        $materials = Material::with(['logs' => function($query) {
            $query->whereBetween('date', [$this->startDate, $this->endDate]);
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
            ->where('date', '<', $this->startDate)
            ->groupBy('material_id', 'type', 'unit')
            ->get();
        $stokAwalMap = [];
        foreach($stokAwalRaw as $row) {
            if(!isset($stokAwalMap[$row->material_id])) $stokAwalMap[$row->material_id] = 0;
            $normalized = $this->normalizeMaterialQuantity($row->total, $row->unit);
            if($row->type == 'in') $stokAwalMap[$row->material_id] += $normalized;
            else $stokAwalMap[$row->material_id] -= $normalized;
        }

        $viewName = $this->startDate == $this->endDate ? 'exports.material-harian' : 'exports.material';

        return view($viewName, [
            'materials' => $materials,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
            'stokAwalMap' => $stokAwalMap
        ]);
    }
}
