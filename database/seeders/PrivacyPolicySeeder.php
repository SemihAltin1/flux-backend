<?php

namespace Database\Seeders;

use App\Models\PrivacyPolicy;
use Illuminate\Database\Seeder;

class PrivacyPolicySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PrivacyPolicy::create([
            'version' => '1.0',
            'content' => $this->getPrivacyPolicyContent(),
            'is_active' => true,
            'effective_date' => now(),
        ]);
    }

    /**
     * Get the privacy policy content.
     *
     * @return string
     */
    private function getPrivacyPolicyContent(): string
    {
        return <<<'EOT'
# Privacy Policy for FLUX Notes

**Effective Date:** February 3, 2026

## 1. Introduction

Welcome to FLUX Notes ("we", "our", or "us"). We are committed to protecting your privacy and ensuring the security of your personal information. This Privacy Policy explains how we collect, use, disclose, and safeguard your information when you use our mobile application.

## 2. Information We Collect

### Personal Information
- First and Last Name
- Email Address
- Password (encrypted)

### User-Generated Content
- Notes and their content
- Categories created by users
- Note timestamps and metadata

## 3. How We Use Your Information

We use the information we collect to:
- Provide, operate, and maintain our service
- Improve and personalize your experience
- Authenticate your account and prevent unauthorized access
- Send password reset emails when requested
- Analyze usage patterns to improve our service

## 4. Data Storage and Security

- All passwords are encrypted using industry-standard hashing algorithms
- Data is stored securely on our servers
- We implement appropriate technical and organizational measures to protect your data
- Access to user data is restricted to authorized personnel only

## 5. Data Sharing

We do not sell, trade, or otherwise transfer your personal information to third parties. Your notes and personal data remain private and are only accessible to you.

## 6. Your Rights

You have the right to:
- Access your personal information
- Update or correct your information
- Delete your account and associated data
- Request a copy of your data

## 7. Password Reset

When you request a password reset:
- We generate a secure, time-limited token
- A reset link is sent to your registered email address
- The token expires after 60 minutes for security

## 8. Children's Privacy

Our service is not intended for users under the age of 13. We do not knowingly collect personal information from children under 13.

## 9. Changes to This Privacy Policy

We may update this Privacy Policy from time to time. We will notify you of any changes by posting the new Privacy Policy on this page and updating the "Effective Date."

## 10. Contact Us

If you have any questions about this Privacy Policy, please contact us at:
- Email: privacy@flux.com

---

By using FLUX Notes, you agree to the collection and use of information in accordance with this Privacy Policy.
EOT;
    }
}
