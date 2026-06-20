<?php

namespace App\Services;

use App\Models\TenantSubscription;
use Illuminate\Support\Facades\Cache;

class LicenseService
{
    private const CACHE_KEY = 'license_status';
    private const CACHE_TTL = 86400; // 24 hours
    private const GRACE_DAYS = 7;

    /**
     * Verify license status for the current tenant
     */
    public function verify(?int $tenantId = null): LicenseStatus
    {
        $tenantId = $tenantId ?? app('current_tenant')?->id;

        if (!$tenantId) {
            return new LicenseStatus('no_tenant', null);
        }

        $cacheKey = self::CACHE_KEY . ".{$tenantId}";

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($tenantId) {
            $subscription = TenantSubscription::where('tenant_id', $tenantId)
                ->where('status', '!=', 'suspended')
                ->latest('created_at')
                ->first();

            if (!$subscription) {
                return new LicenseStatus('no_license', null);
            }

            $now = now();

            // Active license
            if ($subscription->expires_at && $now->lt($subscription->expires_at)) {
                return new LicenseStatus('active', $subscription);
            }

            // Grace period
            $graceUntil = $subscription->grace_until
                ?? $subscription->expires_at?->addDays(self::GRACE_DAYS);

            if ($graceUntil && $now->lt($graceUntil)) {
                return new LicenseStatus('grace', $subscription, $graceUntil);
            }

            // Expired
            return new LicenseStatus('expired', $subscription);
        });
    }

    /**
     * Check if a specific feature is available on the current plan
     */
    public function canUseFeature(string $feature, ?int $tenantId = null): bool
    {
        $status = $this->verify($tenantId);

        if ($status->isExpired()) {
            return false; // Read-only mode
        }

        $subscription = $status->subscription;
        if (!$subscription || !$subscription->features) {
            return true; // No restriction defined = all features
        }

        $features = is_array($subscription->features)
            ? $subscription->features
            : json_decode($subscription->features, true);

        return in_array($feature, $features ?? []);
    }

    /**
     * Get max customers allowed on current plan
     */
    public function getMaxCustomers(?int $tenantId = null): int
    {
        $status = $this->verify($tenantId);
        $subscription = $status->subscription;

        if (!$subscription) {
            return 50; // Free tier
        }

        return $subscription->max_customers ?? match ($subscription->plan) {
            'free' => 50,
            'starter' => 500,
            'pro' => 5000,
            'enterprise' => PHP_INT_MAX,
            default => 50,
        };
    }

    /**
     * Clear cached license status
     */
    public function clearCache(?int $tenantId = null): void
    {
        $tenantId = $tenantId ?? app('current_tenant')?->id;
        Cache::forget(self::CACHE_KEY . ".{$tenantId}");
    }
}

/**
 * Value object for license status
 */
class LicenseStatus
{
    public function __construct(
        public readonly string $status,
        public readonly ?TenantSubscription $subscription,
        public readonly ?\DateTimeInterface $graceUntil = null,
    ) {}

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isGrace(): bool
    {
        return $this->status === 'grace';
    }

    public function isExpired(): bool
    {
        return in_array($this->status, ['expired', 'no_license', 'no_tenant']);
    }

    public function isReadOnly(): bool
    {
        return $this->isExpired();
    }

    public function getPlan(): string
    {
        return $this->subscription?->plan ?? 'free';
    }

    public function daysRemaining(): int
    {
        if ($this->isGrace() && $this->graceUntil) {
            return max(0, (int) now()->diffInDays($this->graceUntil, false));
        }

        if ($this->subscription?->expires_at) {
            return max(0, (int) now()->diffInDays($this->subscription->expires_at, false));
        }

        return 0;
    }

    public function toArray(): array
    {
        return [
            'status' => $this->status,
            'plan' => $this->getPlan(),
            'days_remaining' => $this->daysRemaining(),
            'is_read_only' => $this->isReadOnly(),
            'grace_until' => $this->graceUntil?->format('Y-m-d'),
            'expires_at' => $this->subscription?->expires_at?->format('Y-m-d'),
        ];
    }
}
