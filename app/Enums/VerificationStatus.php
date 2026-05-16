<?php

namespace App\Enums;

enum VerificationStatus: string
{
    case PendingOtp = 'pending_otp';
    case Verified = 'verified';
    case Suspended = 'suspended';
}
