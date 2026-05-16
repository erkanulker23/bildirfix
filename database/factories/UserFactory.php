<?php

namespace Database\Factories;

use App\Enums\UserRole;
use App\Enums\VerificationStatus;
use App\Models\User;
use App\Support\Phone;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected static ?string $password;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $phoneDigits = fake()->numerify('5#########');

        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => Phone::normalize($phoneDigits),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'role' => UserRole::VerifiedUser->value,
            'verification_status' => VerificationStatus::Verified->value,
            'phone_verified_at' => now(),
            'email_verified_at' => now(),
            'score' => fake()->numberBetween(0, 500),
            'trust_score' => fake()->numberBetween(30, 100),
        ];
    }

    /**
     * @return $this
     */
    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => UserRole::Admin->value,
            'verification_status' => VerificationStatus::Verified->value,
            'phone_verified_at' => now(),
        ]);
    }

    /**
     * @return $this
     */
    public function pendingPhone(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => UserRole::User->value,
            'verification_status' => VerificationStatus::PendingOtp->value,
            'phone_verified_at' => null,
        ]);
    }

    /**
     * @return $this
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
