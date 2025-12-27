<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MessFoodItem extends Model
{
    use HasFactory;
    protected $table = 'mess_food_items';
    protected $fillable = [
        'name',
        'category',
        'description',
        'calories',
        'price',
        'status',
        'image'
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    /**
     * Scope for active items
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'Active');
    }

    /**
     * Scope for inactive items
     */
    public function scopeInactive($query)
    {
        return $query->where('status', 'Inactive');
    }
}
