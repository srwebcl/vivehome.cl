<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\User;
use App\Models\Category;
use Illuminate\Http\Request;

class AllPropertyController extends Controller
{
    // ... (El método index() no cambia) ...
    public function index(Request $request)
    {
        $query = Property::with(['user', 'category'])
                         ->orderBy('created_at', 'desc');

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

        $properties = $query->paginate(15);

        $statuses = Property::select('status')->distinct()->pluck('status');
        $asesores = User::where('role', 'asesor')->orderBy('name')->pluck('name', 'id');
        $categories = Category::orderBy('name')->pluck('name', 'id');
        $typeOperations = Property::select('type_operation')->distinct()->pluck('type_operation');

        return view('admin.all-properties.index', compact(
            'properties', 'statuses', 'asesores', 'categories', 'typeOperations'
        ));
    }

    /**
     * Muestra los detalles de una propiedad específica.
     */
    public function show(Property $property)
    {
        $property->load([
            'user',
            'category',
            'photos',
            'videos',
            'features',
            // CORRECCIÓN: Ahora cargamos la relación con el nombre correcto 'definition'.
            'customFieldValues.definition'
        ]);

        return view('admin.all-properties.show', compact('property'));
    }

    // ... (El resto de los métodos: updateStatus, destroy no cambian) ...
    public function updateStatus(Request $request, Property $property)
    {
        $validStatuses = ['Disponible', 'Arrendada', 'Vendida'];
        $request->validate([
            'status' => ['required', 'string', \Illuminate\Validation\Rule::in($validStatuses)],
        ]);
        try {
            $property->status = $request->status;
            $property->save();
            return redirect()->route('admin.all-properties.index')
                             ->with('success', "Estado de la propiedad \"{$property->title}\" actualizado a \"{$property->status}\" exitosamente.");
        } catch (\Exception $e) {
            return redirect()->route('admin.all-properties.index')
                             ->with('error', 'Hubo un problema al actualizar el estado de la propiedad.');
        }
    }

    public function destroy(Property $property)
    {
        try {
            $propertyTitle = $property->title;
            $property->delete();
            return redirect()->route('admin.all-properties.index')
                             ->with('success', "Propiedad \"{$propertyTitle}\" eliminada exitosamente.");
        } catch (\Exception $e) {
            return redirect()->route('admin.all-properties.index')
                             ->with('error', 'Hubo un problema al eliminar la propiedad.');
        }
    }
}