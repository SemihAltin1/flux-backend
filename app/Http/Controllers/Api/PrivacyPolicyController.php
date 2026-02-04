<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PrivacyPolicyService;
use Illuminate\Http\JsonResponse;

class PrivacyPolicyController extends Controller
{
    protected $privacyPolicyService;

    public function __construct(PrivacyPolicyService $privacyPolicyService)
    {
        $this->privacyPolicyService = $privacyPolicyService;
    }

    /**
     * Get the active privacy policy.
     *
     * @return JsonResponse
     */
    public function getPrivacyPolicy(): JsonResponse
    {
        try {
            $policy = $this->privacyPolicyService->getActivePolicy();

            if (!$policy) {
                return response()->json([
                    'success' => false,
                    'message' => 'No active privacy policy found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Privacy policy retrieved successfully',
                'data' => [
                    'policy' => $policy,
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve privacy policy',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
