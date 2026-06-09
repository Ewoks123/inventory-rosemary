<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produksi;
use App\Models\Produk;
use App\Models\Material;

class ProduksiController extends Controller
{
    private function getSortedProduks()
    {
        return Produk::orderBy('nama_produk', 'asc')->get();
    }

    private function getSortedMaterials()
    {
        return Material::orderBy('nama_material', 'asc')->get();
    }

    public function index()
    {
        $produksis = Produksi::with(['produk', 'material'])->get();
        $produks = $this->getSortedProduks();
        $materials = $this->getSortedMaterials();
        return view('produk.produksi', compact('produksis', 'produks', 'materials'));
    }

    public function create()
    {
        $produks = $this->getSortedProduks();
        $materials = $this->getSortedMaterials();
        return view('produk.produksi', compact('produks', 'materials'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_produk' => 'required|exists:produks,id',
            'jumlah_produksi' => 'required|numeric|min:1',
            'tanggal_produksi' => 'required|date',
        ]);

        $produk = Produk::findOrFail($request->id_produk);

        $produk->stok_produk += $request->jumlah_produksi;
        $produk->save();

        Produksi::create([
            'id_produk' => $produk->id,
            'jumlah_produksi' => $request->jumlah_produksi,
            'tanggal_produksi' => $request->tanggal_produksi,
        ]);

        return redirect()->route('produks.index')->with('success', 'Produksi berhasil ditambahkan.');
    }

    public function show($id)
    {
        $produksi = Produksi::with(['produk', 'material'])->findOrFail($id);
        return view('produk.produksi-detail', compact('produksi'));
    }

    public function edit($id)
    {
        $produksi = Produksi::findOrFail($id);
        $produks = $this->getSortedProduks();
        $materials = $this->getSortedMaterials();
        return view('produk.produksi-edit', compact('produksi', 'produks', 'materials'));
    }

    public function update(Request $request, $id)
    {
        $produksi = Produksi::findOrFail($id);

        $request->validate([
            'id_produk' => 'required|exists:produks,id',
            'id_material' => 'required|exists:materials,id',
            'jumlah_produksi' => 'required|numeric|min:1',
            'material_digunakan' => 'required|numeric|min:0',
            'tanggal_produksi' => 'required|date',
            'keterangan' => 'required|string|max:255',
        ]);

        $oldMaterial = Material::findOrFail($produksi->id_material);
        $oldProduct = Produk::findOrFail($produksi->id_produk);

        $oldMaterial->stok_material += $produksi->material_digunakan;
        $oldMaterial->save();

        $oldProduct->stok_produk -= $produksi->jumlah_produksi;
        $oldProduct->save();

        $newMaterial = Material::findOrFail($request->id_material);
        if ($newMaterial->stok_material < $request->material_digunakan) {
            return back()->withErrors(['material_digunakan' => 'Stok material tidak mencukupi.'])->withInput();
        }

        $newProduct = Produk::findOrFail($request->id_produk);
        $newMaterial->stok_material -= $request->material_digunakan;
        $newMaterial->save();

        $newProduct->stok_produk += $request->jumlah_produksi;
        $newProduct->save();

        $produksi->update([
            'id_produk' => $request->id_produk,
            'id_material' => $request->id_material,
            'jumlah_produksi' => $request->jumlah_produksi,
            'material_digunakan' => $request->material_digunakan,
            'tanggal_produksi' => $request->tanggal_produksi,
            'keterangan' => $request->keterangan ?? $produksi->keterangan,
        ]);

        return redirect()->route('produksis.index')->with('success', 'Produksi berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $produksi = Produksi::findOrFail($id);
        $material = Material::findOrFail($produksi->id_material);
        $produk = Produk::findOrFail($produksi->id_produk);

        $material->stok_material += $produksi->material_digunakan;
        $material->save();

        $produk->stok_produk -= $produksi->jumlah_produksi;
        $produk->save();

        $produksi->delete();

        return redirect()->route('produksis.index')->with('success', 'Produksi berhasil dihapus.');
    }
}
