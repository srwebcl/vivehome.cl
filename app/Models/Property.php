<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;      // Importar BelongsTo
use Illuminate\Database\Eloquent\Relations\HasMany;       // Importar HasMany
use Illuminate\Database\Eloquent\Relations\BelongsToMany; // Importar BelongsToMany

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
        'price' => 'decimal:2', // Asumiendo 2 decimales para el precio
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
     */
    public function photos(): HasMany
    {
        return $this->hasMany(PropertyPhoto::class);
    }

    /**
     * Define la relación: una Propiedad tiene muchos Videos.
     */
    public function videos(): HasMany
    {
        return $this->hasMany(PropertyVideo::class);
    }

    /**
     * Define la relación: una Propiedad tiene muchos Valores de Campos Personalizados.
     */
    public function customFieldValues(): HasMany
    {
        return $this->hasMany(PropertyCustomFieldValue::class);
    }

    /**
     * Define la relación: una Propiedad pertenece a muchas Características (muchos-a-muchos).
     */
    public function features(): BelongsToMany
    {
        return $this->belongsToMany(PropertyFeature::class, 'feature_property', 'property_id', 'property_feature_id')
                    ->withTimestamps(); // Opcional: si quieres que se actualicen los timestamps en la tabla pivote
    }
}