<?php

namespace App\Repositories;

use App\Models\Audit;
use Illuminate\Database\Eloquent\Collection;

class AuditRepository
{
    public function getAll(): Collection
    {
        return Audit::all();
    }

    public function getBySubscriptionPlanId(int $id): Collection
    {
        return Audit::where('subscription_plan_id', $id)->get();
    }

    public function create(array $data): Audit
    {
        return Audit::create($data);
    }
}
