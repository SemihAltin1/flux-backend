<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ProfileService
{
    /**
     * Update user profile.
     *
     * @param User $user
     * @param array $data
     * @return User
     */
    public function updateProfile(User $user, array $data): User
    {
        $updateData = [];

        if (isset($data['first_name'])) {
            $updateData['first_name'] = $data['first_name'];
        }

        if (isset($data['last_name'])) {
            $updateData['last_name'] = $data['last_name'];
        }

        if (isset($data['email']) && $data['email'] !== $user->email) {
            $updateData['email'] = $data['email'];
        }

        $user->update($updateData);
        $user->refresh();

        return $user;
    }

    /**
     * Update user password.
     *
     * @param User $user
     * @param string $currentPassword
     * @param string $newPassword
     * @return bool
     */
    public function updatePassword(User $user, string $currentPassword, string $newPassword): bool
    {
        // Verify current password
        if (!Hash::check($currentPassword, $user->password)) {
            return false;
        }

        // Update to new password
        $user->update([
            'password' => Hash::make($newPassword),
        ]);

        return true;
    }
}
