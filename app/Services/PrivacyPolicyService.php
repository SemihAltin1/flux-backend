<?php

namespace App\Services;

use App\Models\PrivacyPolicy;

class PrivacyPolicyService
{
    /**
     * Get the active privacy policy.
     *
     * @return PrivacyPolicy|null
     */
    public function getActivePolicy(): ?PrivacyPolicy
    {
        return PrivacyPolicy::getActive();
    }

    /**
     * Get all privacy policies.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllPolicies()
    {
        return PrivacyPolicy::orderBy('effective_date', 'desc')->get();
    }

    /**
     * Get a specific privacy policy by version.
     *
     * @param string $version
     * @return PrivacyPolicy|null
     */
    public function getPolicyByVersion(string $version): ?PrivacyPolicy
    {
        return PrivacyPolicy::where('version', $version)->first();
    }
}
