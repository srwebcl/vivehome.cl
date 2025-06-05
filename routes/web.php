<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CustomFieldDefinitionController;
use App\Http\Controllers\Admin\SettingController; // <-- IMPORTA EL NUEVO SettingController
use App\Http\Controllers\Admin\AllPropertyController;

Route::get('/', function () {
    return view('welcome');
});

// RUTAS DE ADMINISTRACIÓN
Route::middleware(['auth', 'verified', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', function () { return view('admin.dashboard'); })->name('dashboard');

    // Rutas para CRUD de Usuarios
    Route::resource('users', UserController::class);

    // Rutas para CRUD de Categorías
    Route::resource('categories', CategoryController::class);

    // Rutas para CRUD de Definiciones de Campos Personalizados
    Route::resource('custom-field-definitions', CustomFieldDefinitionController::class)->names('custom_fields');

    // Rutas para la Configuración del Sitio (¡AQUÍ LAS AÑADIMOS!)
    Route::get('settings', [SettingController::class, 'edit'])->name('settings.edit');
    Route::put('settings', [SettingController::class, 'update'])->name('settings.update');
    // Fin de Rutas para la Configuración del Sitio

    // Rutas de Perfil (dentro del admin)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Rutas para la Gestión de Todas las Propiedades por el Admin
    Route::get('all-properties', [AllPropertyController::class, 'index'])->name('all-properties.index');
    Route::get('all-properties/{property}', [AllPropertyController::class, 'show'])->name('all-properties.show');
    Route::patch('all-properties/{property}/status', [AllPropertyController::class, 'updateStatus'])->name('all-properties.updateStatus');
    Route::delete('all-properties/{property}', [AllPropertyController::class, 'destroy'])->name('all-properties.destroy'); // <-- NUEVA RUTA
    });

require __DIR__.'/auth.php';