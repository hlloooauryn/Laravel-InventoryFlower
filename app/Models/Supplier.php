<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Supplier extends Model
{
    const UPDATED_AT = null;

    protected $fillable = [
        'name',
        'contact',
        'address',
    ];

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_suppliers')
            ->withPivot('supplier_price');
    }
}
