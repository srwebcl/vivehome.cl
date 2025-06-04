<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Importar BelongsTo

class PropertyCustomFieldValue extends Model
{
    use HasFactory;

    protected $table = 'property_custom_field_values';

    protected $fillable = [
        'property_id',
        'custom_field_definition_id',
        'value',
    ];

    /**
     * Define la relación: este Valor pertenece a una Propiedad.
     */
    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    /**
     * Define la relación: este Valor pertenece a una Definición de Campo Personalizado.
     */
    public function definition(): BelongsTo
    {
        return $this->belongsTo(CustomFieldDefinition::class, 'custom_field_definition_id');
    }
}