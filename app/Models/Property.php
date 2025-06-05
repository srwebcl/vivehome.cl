<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Storage; // <--- IMPORTANTE: Añadir para la manipulación de archivos

class Property extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'slug',
        'description',
        'operation_type',
        'type_property',  // Añadido aquí, asegúrate que exista en tu tabla properties
        'price',
        'currency',
        'address',
        'commune',
        'city',
        'region',
        'latitude',
        'longitude',
        'total_area_m2',
        'built_area_m2',
        'bedrooms',
        'bathrooms',
        'parking_lots',
        'storage_units',
        'status',
        'is_featured',
    ];

    /**
     * Los atributos que deben ser convertidos a tipos nativos.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'price' => 'decimal:2',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'total_area_m2' => 'decimal:2',
        'built_area_m2' => 'decimal:2',
        'is_featured' => 'boolean',
        'bedrooms' => 'integer',
        'bathrooms' => 'integer',
        'parking_lots' => 'integer',
        'storage_units' => 'integer',
    ];

    /**
     * Código a ejecutar cuando el modelo es "booted".
     * Aquí manejamos la eliminación en cascada de datos relacionados.
     */
    protected static function booted(): void
    {
        static::deleting(function (Property $property) {
            // 1. Eliminar fotos asociadas (registros y archivos)
            // Asume que la relación se llama 'photos()' y el modelo PropertyPhoto tiene un campo 'image_path'
            // que guarda la ruta del archivo en el disco 'public'.
            if ($property->photos()->exists()) {
                foreach ($property->photos as $photo) {
                    if ($photo->image_path && Storage::disk('public')->exists($photo->image_path)) {
                        Storage::disk('public')->delete($photo->image_path);
                    }
                    $photo->delete(); // Elimina el registro de la foto de la BD
                }
            }

            // 2. Eliminar videos asociados (registros y archivos, si son locales)
            // Asume que la relación se llama 'videos()', el modelo PropertyVideo tiene 'video_path'
            // para archivos locales y un booleano 'is_external_link'.
            if ($property->videos()->exists()) {
                foreach ($property->videos as $video) {
                    if (!$video->is_external_link && $video->video_path && Storage::disk('public')->exists($video->video_path)) {
                        Storage::disk('public')->delete($video->video_path);
                    }
                    $video->delete(); // Elimina el registro del video de la BD
                }
            }

            // 3. Eliminar valores de campos personalizados asociados
            // Asume que la relación se llama 'customFieldValues()'
            if ($property->customFieldValues()->exists()) {
                // Opción A: Si los PropertyCustomFieldValue no tienen lógica compleja al eliminarse (más eficiente)
                $property->customFieldValues()->delete();

                // Opción B: Si necesitas que se disparen eventos en cada PropertyCustomFieldValue (menos eficiente para muchos registros)
                // foreach ($property->customFieldValues as $customValue) {
                //     $customValue->delete();
                // }
            }

            // 4. Desvincular características (para relaciones ManyToMany)
            // Asume que la relación se llama 'features()'
            if ($property->features()->exists()) {
                $property->features()->detach(); // Elimina las entradas de la tabla pivote
            }
        });
    }

    /**
     * Define la relación: una Propiedad pertenece a un Usuario (Asesor).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Define la relación: una Propiedad pertenece a una Categoría.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Define la relación: una Propiedad tiene muchas Fotos.
     * Asegúrate que el modelo App\Models\PropertyPhoto existe y tiene un campo 'image_path'.
     */
    public function photos(): HasMany
    {
        return $this->hasMany(PropertyPhoto::class);
    }

    /**
     * Define la relación: una Propiedad tiene muchos Videos.
     * Asegúrate que el modelo App\Models\PropertyVideo existe y tiene campos como 'video_path' e 'is_external_link'.
     */
    public function videos(): HasMany
    {
        return $this->hasMany(PropertyVideo::class);
    }

    /**
     * Define la relación: una Propiedad tiene muchos Valores de Campos Personalizados.
     * Asegúrate que el modelo App\Models\PropertyCustomFieldValue existe.
     */
    public function customFieldValues(): HasMany
    {
        return $this->hasMany(PropertyCustomFieldValue::class);
    }

    /**
     * Define la relación: una Propiedad pertenece a muchas Características (muchos-a-muchos).
     * Asegúrate que el modelo App\Models\PropertyFeature existe.
     */
    public function features(): BelongsToMany
    {
        return $this->belongsToMany(PropertyFeature::class, 'feature_property', 'property_id', 'property_feature_id')
                    ->withTimestamps(); // Opcional: si quieres que se actualicen los timestamps en la tabla pivote
    }
}