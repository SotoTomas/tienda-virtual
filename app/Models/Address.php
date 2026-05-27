<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Address extends Model
{
    protected $fillable = [
        'user_id', 'name', 'phone', 'street', 'number',
        'floor', 'apartment', 'city', 'state', 'postal_code',
        'country', 'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Convierte la dirección a array para guardar como snapshot en la orden
    public function toSnapshot(): array
    {
        return [
            'name'        => $this->name,
            'phone'       => $this->phone,
            'street'      => $this->street,
            'number'      => $this->number,
            'floor'       => $this->floor,
            'apartment'   => $this->apartment,
            'city'        => $this->city,
            'state'       => $this->state,
            'postal_code' => $this->postal_code,
            'country'     => $this->country,
        ];
    }
}