<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubscriptionPlan extends Model
{
    use HasFactory;

    protected $table = 'subscription_plans';
    protected $dates = ['created_at', 'updated_at'];

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'price'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'subscription_plan_products', 'subscription_plan_id', 'product_id');
    }

    public function productsHistory(): HasMany
    {
        return $this->hasMany(Audit::class);
    }
}
