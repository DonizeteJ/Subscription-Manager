<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Audit extends Model
{
    use HasFactory;

    protected $table = 'subscription_plan_products_audit';
    protected $dates = ['created_at'];

    public const ACTION_ADDED = 'added';
    public const ACTION_DELETED = 'deleted';

    protected $fillable = [
        'user_id',
        'subscription_plan_id',
        'product_id',
        'action'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function subscriptionPlan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class);
    }
}
