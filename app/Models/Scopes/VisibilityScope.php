<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

/**
 * VisibilityScope — Membatasi data yang bisa dilihat user berdasarkan role permission.
 *
 * Logika (sama dengan sistem lama):
 * - can_view_all_customers = true → tidak ada filter
 * - view_by_area = true → filter berdasarkan allowed_area_ids di role
 * - view_by_sales = true → filter berdasarkan allowed_sales_ids di user
 * - view_own_only = true → hanya data dengan created_by = user id
 *
 * Apply ke model Customer:
 *   use App\Models\Scopes\VisibilityScope;
 *   protected static function booted() { static::addGlobalScope(new VisibilityScope); }
 */
class VisibilityScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        $user = Auth::user();

        if (!$user || !$user->role) {
            return;
        }

        // System admin sees all
        if ($user->is_system_admin) {
            return;
        }

        $role = $user->role;

        // can_view_all_customers → no filter
        if ($role->can_view_all_customers) {
            return;
        }

        // view_own_only → only records created by this user
        if ($role->view_own_only) {
            $builder->where($model->getTable() . '.created_by', $user->id);
            return;
        }

        $conditions = [];

        // view_by_area → filter by allowed_area_ids
        if ($role->view_by_area) {
            $areaIds = $user->perms_view_area_ids ?? $role->allowed_area_ids ?? [];
            if (!empty($areaIds)) {
                $conditions[] = function ($query) use ($model, $areaIds) {
                    $query->whereIn($model->getTable() . '.area_id', $areaIds);
                };
            }
        }

        // view_by_sales → filter by allowed_sales_ids or own user
        if ($role->view_by_sales) {
            $salesIds = $user->perms_view_sales_ids ?? $role->allowed_sales_ids ?? [];
            if (!empty($salesIds)) {
                $conditions[] = function ($query) use ($model, $salesIds) {
                    $query->whereIn($model->getTable() . '.sales_id', $salesIds);
                };
            } else {
                // Backward compat: if no custom list, filter by own user
                $conditions[] = function ($query) use ($model, $user) {
                    $query->where($model->getTable() . '.sales_id', $user->id);
                };
            }
        }

        // Apply conditions with OR logic (area OR sales)
        if (count($conditions) > 1) {
            $builder->where(function ($query) use ($conditions) {
                foreach ($conditions as $i => $condition) {
                    if ($i === 0) {
                        $condition($query);
                    } else {
                        $query->orWhere($condition);
                    }
                }
            });
        } elseif (count($conditions) === 1) {
            $conditions[0]($builder);
        } else {
            // No area or sales filter, fallback to own data
            $builder->where($model->getTable() . '.sales_id', $user->id);
        }
    }
}
