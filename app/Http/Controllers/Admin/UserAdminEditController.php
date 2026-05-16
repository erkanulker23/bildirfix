<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Enums\VerificationStatus;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserAdminEditController extends Controller
{
    public function edit(User $user): View
    {
        return view('admin.users.edit', [
            'user' => $user,
            'roles' => UserRole::cases(),
            'verificationStatuses' => VerificationStatus::cases(),
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
        ]);

        $newRole = $data['role'] instanceof UserRole
            ? $data['role']
            : UserRole::from((string) $data['role']);

        $verificationStatus = $data['verification_status'] instanceof VerificationStatus
            ? $data['verification_status']
            : VerificationStatus::from((string) $data['verification_status']);

        if ($user->role === UserRole::SuperAdmin && $newRole !== UserRole::SuperAdmin) {
            $remaining = User::query()
                ->where('role', UserRole::SuperAdmin)
                ->where('id', '!=', $user->id)
                ->exists();
            if (! $remaining) {
                return back()->withErrors(['role' => __('En az bir süper yönetici hesabı kalmalıdır.')])->withInput();
            }
        }

        $actor = $request->user();
        if ($actor !== null && (int) $user->id === (int) $actor->id && $newRole !== UserRole::SuperAdmin) {
            $remaining = User::query()
                ->where('role', UserRole::SuperAdmin)
                ->where('id', '!=', $actor->id)
                ->exists();
            if (! $remaining) {
                return back()->withErrors(['role' => __('Kendinizi süper yöneticilikten çıkaramazsınız; önce başka bir süper yönetici tanımlayın.')])->withInput();
            }
        }

        $emailVerifiedAt = $data['email_verified']
            ? ($user->email_verified_at ?? now())
            : null;

        $phoneVerifiedAt = $data['phone_verified']
            ? ($user->phone_verified_at ?? now())
            : null;

        $user->forceFill([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] !== '' && $data['phone'] !== null ? $data['phone'] : null,
            'role' => $newRole,
            'verification_status' => $verificationStatus,
            'email_verified_at' => $emailVerifiedAt,
            'phone_verified_at' => $phoneVerifiedAt,
        ])->save();

        return redirect()
            ->route('admin.users.edit', $user)
            ->with('status', __('Kullanıcı güncellendi.'));
    }
}
