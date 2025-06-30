<?php

namespace App\Http\Controllers\Asesor;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\CustomFieldDefinition;
use App\Models\Property;
use App\Models\PropertyFeature;
use App\Models\PropertyPhoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PropertyController extends Controller
{
    public function index()
    {
        $asesorId = Auth::id();
        $properties = Property::where('user_id', $asesorId)->with('category')->latest()->paginate(10);
        return view('asesor.properties.index', compact('properties'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();
        $features = PropertyFeature::orderBy('name')->get();
        $customFields = CustomFieldDefinition::orderBy('name')->get();
        return view('asesor.properties.create', compact('categories', 'features', 'customFields'));
    }

    public function store(Request $request)
    {
        // --- MODIFICACIÓN 1: Añadido 'is_featured' a la validación ---
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'operation_type' => 'required|in:Venta,Arriendo',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'currency' => 'required|in:CLP,UF',
            'address' => 'nullable|string|max:255',
            'commune' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'region' => 'nullable|string|max:255',
            'total_area_m2' => 'nullable|numeric|min:0',
            'built_area_m2' => 'nullable|numeric|min:0',
            'bedrooms' => 'nullable|integer|min:0',
            'bathrooms' => 'nullable|integer|min:0',
            'parking_lots' => 'nullable|integer|min:0',
            'storage_units' => 'nullable|integer|min:0',
            'is_featured' => 'nullable|boolean', // <-- CAMPO AÑADIDO
            'features' => 'nullable|array',
            'features.*' => 'exists:property_features,id',
            'custom_fields' => 'nullable|array',
            'custom_fields.*' => 'nullable|string|max:65535',
            'photos' => 'nullable|array',
            'photos.*' => 'image|mimes:jpeg,png,jpg,webp|max:2048',
            'video_url' => 'nullable|url|max:255',
        ]);
        try {
            $propertyData = collect($validatedData)->except(['features', 'custom_fields', 'photos', 'video_url'])->all();
            $propertyData['user_id'] = Auth::id();
            $propertyData['slug'] = Str::slug($propertyData['title']);
            $propertyData['status'] = 'Disponible';
            
            // --- MODIFICACIÓN 2: Procesar correctamente el valor del checkbox ---
            // $request->boolean() devuelve true si el campo es "1", "true", "on", o si simplemente existe. Devuelve false en caso contrario.
            $propertyData['is_featured'] = $request->boolean('is_featured');

            $property = Property::create($propertyData);

            if ($request->has('features')) {
                $property->features()->sync($request->features);
            }
            if ($request->has('custom_fields')) {
                foreach ($request->custom_fields as $fieldId => $value) {
                    if (!is_null($value) && $value !== '') {
                        $property->customFieldValues()->create(['custom_field_definition_id' => $fieldId, 'value' => $value]);
                    }
                }
            }
            if ($request->hasFile('photos')) {
                foreach ($request->file('photos') as $photo) {
                    $path = $photo->store('properties', 'public');
                    $property->photos()->create(['file_path' => $path]);
                }
            }
            
            if ($request->filled('video_url')) {
                $property->videos()->create(['video_url' => $request->video_url]);
            }

            return redirect()->route('asesor.properties.index')->with('success', '¡Propiedad creada exitosamente!');
        } catch (\Exception $e) {
            Log::error('Error al crear propiedad: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Hubo un problema al guardar la propiedad.');
        }
    }

    public function show(Property $property)
    {
        return redirect()->route('asesor.properties.edit', $property);
    }

    public function edit(Property $property)
    {
        if (Auth::id() !== $property->user_id) {
            abort(403, 'No tienes permiso para editar esta propiedad.');
        }
        $categories = Category::orderBy('name')->get();
        $features = PropertyFeature::orderBy('name')->get();
        $customFields = CustomFieldDefinition::orderBy('name')->get();
        return view('asesor.properties.edit', compact('property', 'categories', 'features', 'customFields'));
    }

    public function update(Request $request, Property $property)
    {
        if (Auth::id() !== $property->user_id) {
            abort(403);
        }
        
        // --- MODIFICACIÓN 3: Añadido 'is_featured' a la validación ---
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'operation_type' => 'required|in:Venta,Arriendo',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'currency' => 'required|in:CLP,UF',
            'address' => 'nullable|string|max:255',
            'commune' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'region' => 'nullable|string|max:255',
            'total_area_m2' => 'nullable|numeric|min:0',
            'built_area_m2' => 'nullable|numeric|min:0',
            'bedrooms' => 'nullable|integer|min:0',
            'bathrooms' => 'nullable|integer|min:0',
            'parking_lots' => 'nullable|integer|min:0',
            'storage_units' => 'nullable|integer|min:0',
            'is_featured' => 'nullable|boolean', // <-- CAMPO AÑADIDO
            'features' => 'nullable|array', 'features.*' => 'exists:property_features,id',
            'custom_fields' => 'nullable|array', 'custom_fields.*' => 'nullable|string|max:65535',
            'photos' => 'nullable|array',
            'photos.*' => 'image|mimes:jpeg,png,jpg,webp|max:2048',
            'delete_photos' => 'nullable|array',
            'delete_photos.*' => 'exists:property_photos,id',
            'video_url' => 'nullable|url|max:255',
        ]);

        try {
            $propertyData = collect($validatedData)->except(['features', 'custom_fields', 'photos', 'delete_photos', 'video_url'])->all();
            if ($property->title !== $propertyData['title']) {
                $propertyData['slug'] = Str::slug($propertyData['title']);
            }
            
            // --- MODIFICACIÓN 4: Procesar correctamente el valor del checkbox ---
            $propertyData['is_featured'] = $request->boolean('is_featured');
            
            $property->update($propertyData);

            $property->features()->sync($request->input('features', []));

            if ($request->has('custom_fields')) {
                foreach ($request->custom_fields as $fieldId => $value) {
                    if (!is_null($value) && $value !== '') {
                        $property->customFieldValues()->updateOrCreate(['custom_field_definition_id' => $fieldId],['value' => $value]);
                    } else {
                        $property->customFieldValues()->where('custom_field_definition_id', $fieldId)->delete();
                    }
                }
            }

            if ($request->has('delete_photos')) {
                foreach ($request->delete_photos as $photoId) {
                    $photo = PropertyPhoto::find($photoId);
                    if ($photo && $photo->property_id === $property->id) {
                        Storage::disk('public')->delete($photo->file_path);
                        $photo->delete();
                    }
                }
            }
            if ($request->hasFile('photos')) {
                foreach ($request->file('photos') as $photo) {
                    $path = $photo->store('properties', 'public');
                    $property->photos()->create(['file_path' => $path]);
                }
            }

            if ($request->filled('video_url')) {
                $property->videos()->updateOrCreate(
                    ['property_id' => $property->id],
                    ['video_url' => $request->video_url]
                );
            } else {
                $property->videos()->delete();
            }

            return redirect()->route('asesor.properties.index')->with('success', '¡Propiedad actualizada exitosamente!');

        } catch (\Exception $e) {
            Log::error("Error al actualizar propiedad ID {$property->id}: " . $e->getMessage());
            return back()->withInput()->with('error', 'Hubo un problema al actualizar la propiedad.');
        }
    }

    public function destroy(Property $property)
    {
        if (Auth::id() !== $property->user_id) {
            abort(403, 'No tienes permiso para eliminar la propiedad.');
        }
        try {
            $property->delete();
            return redirect()->route('asesor.properties.index')->with('success', 'Propiedad eliminada exitosamente.');
        } catch (\Exception $e) {
            Log::error("Error al eliminar propiedad ID {$property->id}: " . $e->getMessage());
            return back()->with('error', 'Hubo un problema al eliminar la propiedad.');
        }
    }
}