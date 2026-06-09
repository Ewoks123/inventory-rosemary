<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\PenjualanController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\ProduksiController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect()->route('admin.login');
});

Route::get('/admin/login', [AdminController::class, 'login'])->name('admin.login');
Route::post('/admin/login', [AdminController::class, 'authenticate'])->name('admin.authenticate');
Route::post('/admin/logout', [AdminController::class, 'logout'])->name('admin.logout');
Route::middleware(['admin.auth'])->group(function () {
    Route::redirect('/admin', '/admin/dashboard');
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/admin/inventory', [AdminController::class, 'inventory'])->name('admin.inventory');
    Route::get('/admin/setting', [AdminController::class, 'setting'])->name('admin.setting');
    Route::post('/admin/setting/password', [AdminController::class, 'updatePassword'])->name('admin.update_password');
    Route::get('/admin/profile', [AdminController::class, 'profile'])->name('admin.profile');

    Route::resource('admins', AdminController::class);
    Route::get('/materials/menu', [MaterialController::class, 'menu'])->name('materials.menu');
    Route::get('/materials/report', [MaterialController::class, 'report'])->name('materials.report');
    Route::get('/materials/report/pdf', [MaterialController::class, 'exportPdf'])->name('materials.report.pdf');
    Route::get('/materials/report/excel', [MaterialController::class, 'exportExcel'])->name('materials.report.excel');
    Route::get('/materials/history', [MaterialController::class, 'history'])->name('materials.history');
    Route::get('/materials/pharian', [MaterialController::class, 'pharian'])->name('materials.pharian');
    Route::get('/materials/pharian/add', [MaterialController::class, 'addPharian'])->name('materials.addpharian');
    Route::post('/materials/pharian/add', [MaterialController::class, 'storePharian'])->name('materials.storepharian');
    Route::get('/materials/pharian/{id}/edit', [MaterialController::class, 'editPharian'])->name('materials.pharian.edit');
    Route::put('/materials/pharian/{id}', [MaterialController::class, 'updatePharian'])->name('materials.pharian.update');
    Route::get('/materials/actual', [MaterialController::class, 'index'])->name('materials.actual');
    Route::get('/materials/add-in', [MaterialController::class, 'stockIn'])->name('materials.addin');
    Route::post('/materials/add-in', [MaterialController::class, 'storeStockIn'])->name('materials.storein');
    Route::resource('materials', MaterialController::class);

    Route::get('/produks/report', [ProdukController::class, 'report'])->name('produks.report');
    Route::get('/produks/report/pdf', [ProdukController::class, 'exportPdf'])->name('produks.report.pdf');
    Route::get('/produks/report/excel', [ProdukController::class, 'exportExcel'])->name('produks.report.excel');
    Route::resource('produks', ProdukController::class);

    Route::get('/penjualans/report', [PenjualanController::class, 'report'])->name('penjualans.report');
    Route::get('/penjualans/report/pdf', [PenjualanController::class, 'exportPdf'])->name('penjualans.report.pdf');
    Route::get('/penjualans/report/excel', [PenjualanController::class, 'exportExcel'])->name('penjualans.report.excel');
    Route::resource('penjualans', PenjualanController::class);

    Route::resource('produksis', ProduksiController::class);

    Route::resource('users', UserController::class);
});
