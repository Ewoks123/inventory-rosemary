<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Admin;

class AdminController extends Controller
{
    public function login()
    {
        return view('admin.login');
    }

    public function authenticate(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $admin = Admin::where('username', $request->username)->first();

        if (! $admin || ! \Hash::check($request->password, $admin->password)) {
            return back()->withErrors(['username' => 'Username atau password salah'])->withInput();
        }

        session(['admin_id' => $admin->id]);
        $request->session()->regenerate();

        return redirect()->route('admin.dashboard');
    }

    public function dashboard()
    {
        $products = \App\Models\Produk::all();
        
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

        $products = $products->sort(function($a, $b) use ($masterProduks) {
            $posA = array_search($a->nama_produk, $masterProduks);
            $posB = array_search($b->nama_produk, $masterProduks);
            $posA = $posA === false ? 9999 : $posA;
            $posB = $posB === false ? 9999 : $posB;
            if ($posA == $posB) {
                return $a->id <=> $b->id;
            }
            return $posA <=> $posB;
        })->values();
        $lowStockCount = \App\Models\Produk::where('stok_produk', '>', 0)->where('stok_produk', '<=', 10)->count();
        $totalStokHabis = \App\Models\Produk::where('stok_produk', '<=', 0)->count(); 
        
        return view('admin.dashboard', compact('products', 'lowStockCount', 'totalStokHabis'));
    }

    public function logout(Request $request)
    {
        $request->session()->forget('admin_id');
        $request->session()->regenerateToken();
        return redirect()->route('admin.login');
    }

    public function inventory()
    {
        return view('admin.inventory');
    }

    public function setting()
    {
        $admins = Admin::all();
        return view('admin.setting', compact('admins'));
    }

    public function updatePassword(Request $request)
    {
        $messages = [
            'current_password.required' => 'Password saat ini wajib diisi.',
            'new_password.required' => 'Password baru wajib diisi.',
            'confirm_password.required' => 'Konfirmasi password wajib diisi.',
            'confirm_password.same' => 'Konfirmasi password tidak cocok dengan password baru.',
        ];

        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string',
            'confirm_password' => 'required|string|same:new_password',
        ], $messages);

        $admin = Admin::find(session('admin_id'));

        if (! \Hash::check($request->current_password, $admin->password)) {
            return back()->withErrors(['current_password' => 'Password saat ini salah']);
        }

        $admin->update([
            'password' => bcrypt($request->new_password)
        ]);

        return back()->with('success', 'Password berhasil diperbarui.');
    }

    public function profile()
    {
        $admin = Admin::first();
        return view('admin.profile', compact('admin'));
    }

    public function index()
    {
        $admins = Admin::all();
        return view('admin.index', compact('admins'));
    }

    public function create()
    {
        return view('admin.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_admin' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:admins',
            'password' => 'required|string|min:8',
            'email' => 'required|email|unique:admins',
            'role_admin' => 'required|string',
        ]);

        Admin::create([
            'nama_admin' => $request->nama_admin,
            'username' => $request->username,
            'password' => bcrypt($request->password),
            'email' => $request->email,
            'role_admin' => $request->role_admin,
        ]);

        return redirect()->route('admins.index')->with('success', 'Admin created successfully.');
    }

    public function show($id)
    {
        $admin = Admin::findOrFail($id);
        return view('admin.show', compact('admin'));
    }

    public function edit($id)
    {
        $admin = Admin::findOrFail($id);
        return view('admin.edit', compact('admin'));
    }

    public function update(Request $request, $id)
    {
        $admin = Admin::findOrFail($id);

        $request->validate([
            'nama_admin' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:admins,username,' . $id,
            'password' => 'nullable|string|min:8',
            'email' => 'required|email|unique:admins,email,' . $id,
            'role_admin' => 'required|string',
        ]);

        $data = [
            'nama_admin' => $request->nama_admin,
            'username' => $request->username,
            'email' => $request->email,
            'role_admin' => $request->role_admin,
        ];

        if ($request->password) {
            $data['password'] = bcrypt($request->password);
        }

        $admin->update($data);

        return redirect()->route('admins.index')->with('success', 'Admin updated successfully.');
    }

    public function destroy($id)
    {
        $admin = Admin::findOrFail($id);
        $admin->delete();

        return redirect()->route('admins.index')->with('success', 'Admin deleted successfully.');
    }
}
