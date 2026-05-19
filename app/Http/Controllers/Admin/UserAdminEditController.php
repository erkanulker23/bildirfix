<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Enums\VerificationStatus;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\SuperAdmin;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\View\View;

class UserAdminEditController extends Controller
{
    public function edit(User $user): View
    {
        $roles = collect(UserRole::cases())
            ->filter(fn (UserRole $role): bool => SuperAdmin::canAssignRole($role) || SuperAdmin::is($user))
            ->values()
            ->all();

        return view('admin.users.edit', [
            'user' => $user,
            'roles' => $roles,
            'verificationStatuses' => VerificationStatus::cases(),
            'isDesignatedSuperAdmin' => SuperAdmin::is($user),
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:64', Rule::unique('users', 'phone')->ignore($user->id)],
            'role' => ['required', Rule::enum(UserRole::class)],
            'verification_status' => ['required', Rule::enum(VerificationStatus::class)],
            'email_verified' => ['required', 'boolean'],
            'phone_verified' => ['required', 'boolean'],
            'password' => ['nullable', 'confirmed', PasswordRule::defaults()],
        ]);

        $newRole = $data['role'] instanceof UserRole
            ? $data['role']
            : UserRole::from((string) $data['role']);

        $email = strtolower(trim((string) $data['email']));
        $isDesignatedEmail = $email === SuperAdmin::email();

        if ($newRole === UserRole::SuperAdmin && ! $isDesignatedEmail) {
            return back()->withErrors([
                'role' => __('Süper yönetici yalnızca :email adresine atanabilir.', ['email' => SuperAdmin::email()]),
            ])->withInput();
        }

        if (SuperAdmin::is($user)) {
            if (! $isDesignatedEmail) {
                return back()->withErrors([
                    'email' => __('Süper yönetici e-postası değiştirilemez.'),
                ])->withInput();
            }
            $newRole = UserRole::SuperAdmin;
        }

        if ($isDesignatedEmail) {
            $newRole = UserRole::SuperAdmin;
        }

        $verificationStatus = $data['verification_status'] instanceof VerificationStatus
            ? $data['verification_status']
            : VerificationStatus::from((string) $data['verification_status']);

        $emailVerifiedAt = $data['email_verified']
            ? ($user->email_verified_at ?? now())
            : null;

        $phoneVerifiedAt = $data['phone_verified']
            ? ($user->phone_verified_at ?? now())
            : null;

        $fill = [
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] !== '' && $data['phone'] !== null ? $data['phone'] : null,
            'role' => $newRole,
            'verification_status' => $verificationStatus,
            'email_verified_at' => $emailVerifiedAt,
            'phone_verified_at' => $phoneVerifiedAt,
        ];

        if (! empty($data['password'])) {
            $fill['password'] = Hash::make((string) $data['password']);
        }

        $user->forceFill($fill)->save();

        return redirect()
            ->route('admin.users.edit', $user)
            ->with('status', __('Kullanıcı güncellendi.'));
    }

    public function sendPasswordReset(User $user): RedirectResponse
    {
        if ($user->email === null || trim((string) $user->email) === '') {
            return back()->withErrors([
                'password_reset' => __('Bu kullanıcının e-posta adresi yok; sıfırlama gönderilemez.'),
            ]);
        }

        $status = Password::sendResetLink(['email' => $user->email]);

        return $status === Password::RESET_LINK_SENT
            ? back()->with('status', __('Şifre sıfırlama bağlantısı :email adresine gönderildi.', ['email' => $user->email]))
            : back()->withErrors(['password_reset' => __($status)]);
    }
}
