<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Property; // Importa el modelo Property
use App\Models\User;    // Importa el modelo User para filtrar por asesor
use App\Models\Category; // Importa el modelo Category para filtrar por categoría
use Illuminate\Http\Request;

class AllPropertyController extends Controller
{
    /**
     * Muestra un listado de todas las propiedades para el Super Administrador.
     */
    public function index(Request $request)
    {
        $query = Property::with(['user', 'category']) // Carga las relaciones para optimizar consultas
                         ->orderBy('created_at', 'desc'); // Ordena por más recientes primero

        // Ejemplo de filtros básicos (puedes expandir esto)
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('asesor_id')) {
            $query->where('user_id', $request->asesor_id);
        }
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        if ($request->filled('type_operation')) {
            $query->where('type_operation', $request->type_operation);
        }

        $properties = $query->paginate(15); // Pagina los resultados

        // Para los desplegables de los filtros
        $statuses = Property::select('status')->distinct()->pluck('status'); // O define un array/enum de estados
        $asesores = User::where('role', 'asesor')->orderBy('name')->pluck('name', 'id'); // Asume que los asesores tienen rol 'asesor'
        $categories = Category::orderBy('name')->pluck('name', 'id');
        $typeOperations = Property::select('type_operation')->distinct()->pluck('type_operation'); // O define un array/enum

        // La vista que se retorna para el método index
        return view('admin.all-properties.index', compact(
            'properties',
            'statuses',
            'asesores',
            'categories',
            'typeOperations'
        ));
        // La vista estará en resources/views/admin/all-properties/index.blade.php
    } // <-- AQUÍ TERMINA EL MÉTODO INDEX

    /**
     * Muestra los detalles de una propiedad específica para el Super Administrador.
     *
     * @param  \App\Models\Property  $property  (Inyección de modelo vía Route Model Binding)
     * @return \Illuminate\View\View
     */
    public function show(Property $property)
{
    $property->load([
        'user',     // Relación existente y correcta
        'category', // Relación existente y correcta
        'photos',   // ANTES: 'propertyPhotos', AHORA: 'photos'
        'videos',   // ANTES: 'propertyVideos', AHORA: 'videos'
        'features', // Relación existente y correcta (asegúrate que PropertyFeature exista)
        'customFieldValues.customFieldDefinition' // ANTES: 'propertyCustomFieldValues.customFieldDefinition', AHORA: 'customFieldValues.customFieldDefinition'
    ]);

    return view('admin.all-properties.show', compact('property'));
}
/**
 * Actualiza el estado de una propiedad específica.
 *
 * @param  \Illuminate\Http\Request  $request
 * @param  \App\Models\Property  $property
 * @return \Illuminate\Http\RedirectResponse
 */
public function updateStatus(Request $request, Property $property)
{
    // Define los estados válidos
    $validStatuses = ['Disponible', 'Arrendada', 'Vendida']; // Puedes expandir esto o moverlo a un config/enum

    $request->validate([
        'status' => ['required', 'string', \Illuminate\Validation\Rule::in($validStatuses)],
    ]);

    try {
        $property->status = $request->status;
        $property->save();

        // \Illuminate\Support\Facades\Log::info("Estado de propiedad ID {$property->id} actualizado a {$property->status} por admin.");
        return redirect()->route('admin.all-properties.index')
                         ->with('success', "Estado de la propiedad \"{$property->title}\" actualizado a \"{$property->status}\" exitosamente.");
    } catch (\Exception $e) {
        // \Illuminate\Support\Facades\Log::error("Error al actualizar estado de propiedad ID {$property->id}: " . $e->getMessage());
        return redirect()->route('admin.all-properties.index')
                         ->with('error', 'Hubo un problema al actualizar el estado de la propiedad.');
    }
}
/**
 * Elimina una propiedad específica del sistema.
 *
 * @param  \App\Models\Property  $property
 * @return \Illuminate\Http\RedirectResponse
 */
public function destroy(Property $property)
{
    try {
        $propertyTitle = $property->title; // Guardar para el mensaje

        // Aquí es donde idealmente se manejaría la eliminación de datos relacionados
        // (fotos, videos, valores de campos personalizados, etc.)
        // Por ahora, solo eliminaremos el registro principal de la propiedad.
        // Más adelante podemos añadir la lógica de limpieza de datos relacionados.

        // Ejemplo de cómo podrías empezar a limpiar relaciones (requiere que las relaciones existan en el modelo Property)
        // $property->propertyPhotos()->delete(); // Esto eliminaría los registros, no necesariamente los archivos.
        // $property->propertyVideos()->delete();
        // $property->customFieldValues()->delete();
        // $property->features()->detach(); // Para relaciones BelongsToMany

        $property->delete(); // Elimina la propiedad

        // \Illuminate\Support\Facades\Log::info("Propiedad ID {$property->id} ('{$propertyTitle}') eliminada por admin.");
        return redirect()->route('admin.all-properties.index')
                         ->with('success', "Propiedad \"{$propertyTitle}\" eliminada exitosamente.");
    } catch (\Exception $e) {
        // \Illuminate\Support\Facades\Log::error("Error al eliminar propiedad ID {$property->id}: " . $e->getMessage());
        return redirect()->route('admin.all-properties.index')
                         ->with('error', 'Hubo un problema al eliminar la propiedad.');
    }
}
}