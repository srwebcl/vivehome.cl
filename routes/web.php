<?php

use App\Http\Controllers\Admin\AllPropertyController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CustomFieldDefinitionController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Asesor\PropertyController as AsesorPropertyController; // <-- Añadimos un alias para claridad
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// --- Ruta Pública Principal ---
Route::get('/', function () {
    return view('welcome');
});

// --- Ruta del Dashboard Principal (Redirige según el rol) ---
Route::get('/dashboard', function () {
    $user = auth()->user();
    if ($user->role === 'admin') {
        return redirect()->route('admin.dashboard');
    }
    if ($user->role === 'asesor') {
        return redirect()->route('asesor.properties.index');
    }
    // Fallback por si acaso, aunque no debería llegar aquí con los roles actuales.
    return redirect('/');
})->middleware(['auth', 'verified'])->name('dashboard');


// --- GRUPO DE RUTAS DEL SUPER-ADMINISTRADOR ---
Route::middleware(['auth', 'verified', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', function () { return view('admin.dashboard'); })->name('dashboard');
    Route::resource('users', UserController::class);
    Route::resource('categories', CategoryController::class);
    Route::resource('custom-field-definitions', CustomFieldDefinitionController::class)->names('custom_fields');
    Route::get('settings', [SettingController::class, 'edit'])->name('settings.edit');
    Route::put('settings', [SettingController::class, 'update'])->name('settings.update');
    Route::get('all-properties', [AllPropertyController::class, 'index'])->name('all-properties.index');
    Route::get('all-properties/{property}', [AllPropertyController::class, 'show'])->name('all-properties.show');
    Route::patch('all-properties/{property}/status', [AllPropertyController::class, 'updateStatus'])->name('all-properties.updateStatus');
    Route::delete('all-properties/{property}', [AllPropertyController::class, 'destroy'])->name('all-properties.destroy');
});


// --- INICIO: NUEVO GRUPO DE RUTAS PARA ASESORES ---
Route::middleware(['auth', 'verified', 'asesor'])->prefix('asesor')->name('asesor.')->group(function () {
    // Esta única línea crea automáticamente todas las rutas necesarias para el CRUD de propiedades:
    // - asesor.properties.index (GET)
    // - asesor.properties.create (GET)
    // - asesor.properties.store (POST)
    // - asesor.properties.show (GET)
    // - asesor.properties.edit (GET)
    // - asesor.properties.update (PUT/PATCH)
    // - asesor.properties.destroy (DELETE)
    Route::resource('properties', AsesorPropertyController::class);
});
// --- FIN: NUEVO GRUPO DE RUTAS PARA ASESORES ---


// --- GRUPO DE RUTAS DE PERFIL DE USUARIO (PARA TODOS) ---
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


// --- Rutas de autenticación de Breeze ---
require __DIR__.'/auth.php';