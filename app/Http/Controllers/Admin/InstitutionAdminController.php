<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Institution;
use App\Support\Phone;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\View\View;

class InstitutionAdminController extends Controller
{
    public function index(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));

        $query = Institution::query()
            ->with(['city:id,name', 'accountUser:id,name,email'])
            ->withCount('posts as complaints_count')
            ->orderBy('name');

        if ($q !== '') {
            $like = '%'.$q.'%';
            $query->where(function ($w) use ($like, $q): void {
                $w->where('name', 'like', $like)
                    ->orWhere('type', 'like', $like)
                    ->orWhere('public_email', 'like', $like)
                    ->orWhereHas('city', fn ($c) => $c->where('name', 'like', $like))
                    ->orWhereHas('accountUser', fn ($u) => $u->where('name', 'like', $like)->orWhere('email', 'like', $like));
                if (ctype_digit($q)) {
                    $w->orWhere('id', (int) $q);
                }
            });
        }

        $institutions = $query->paginate(30)->withQueryString();
        $types = config('institutions.types', []);

        return view('admin.institutions.index', compact('institutions', 'q', 'types'));
    }

    public function edit(Institution $institution): View
    {
        $institution->load('accountUser:id,name,email,phone');

        $cities = City::query()->orderBy('name')->get(['id', 'name', 'plate']);
        $types = config('institutions.types', []);

        return view('admin.institutions.edit', [
            'institution' => $institution,
            'accountUser' => $institution->accountUser,
            'cities' => $cities,
            'types' => $types,
        ]);
    }

    public function update(Request $request, Institution $institution): RedirectResponse
    {
        $typeKeys = array_keys(config('institutions.types', []));

        $accountUser = $institution->accountUser;

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['nullable', Rule::in($typeKeys)],
            'city_id' => ['nullable', 'integer', 'exists:cities,id'],
            'verified' => ['required', 'integer', 'in:0,1'],
            'logo' => ['nullable', 'image', 'max:2048'],
            'logo_url' => ['nullable', 'string', 'max:2048'],
            'remove_logo' => ['sometimes', 'boolean'],
            'website' => ['nullable', 'string', 'max:2048'],
            'public_email' => ['nullable', 'string', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:64'],
            'address' => ['nullable', 'string', 'max:500'],
            'account_name' => ['nullable', 'string', 'max:255'],
            'account_email' => [
                'nullable',
                'string',
                'email',
                'max:255',
                $accountUser !== null
                    ? Rule::unique('users', 'email')->ignore($accountUser->id)
                    : Rule::unique('users', 'email'),
            ],
            'account_phone' => [
                'nullable',
                'string',
                'max:64',
                $accountUser !== null
                    ? Rule::unique('users', 'phone')->ignore($accountUser->id)
                    : Rule::unique('users', 'phone'),
            ],
            'account_password' => ['nullable', 'confirmed', PasswordRule::defaults()],
        ]);

        $data['verified'] = ((int) $data['verified']) === 1;

        if ($request->boolean('remove_logo')) {
            $data['logo_url'] = null;
        }

        if ($request->hasFile('logo')) {
            $dir = public_path('images/institutions');
            if (! is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
            $filename = 'kurum-'.$institution->id.'-'.time().'.'.$request->file('logo')->guessExtension();
            $request->file('logo')->move($dir, $filename);
            $data['logo_url'] = '/images/institutions/'.$filename;
        }

        foreach (['logo_url', 'website', 'public_email', 'phone', 'address', 'type'] as $key) {
            if (isset($data[$key]) && $data[$key] === '') {
                $data[$key] = null;
            }
        }

        unset($data['logo']);

        $institution->update($data);

        if ($accountUser !== null) {
            $userFill = [];
            if (! empty($data['account_name'])) {
                $userFill['name'] = $data['account_name'];
            }
            if (! empty($data['account_email'])) {
                $userFill['email'] = strtolower(trim((string) $data['account_email']));
            }
            if (array_key_exists('account_phone', $data)) {
                $phone = trim((string) ($data['account_phone'] ?? ''));
                $userFill['phone'] = $phone !== '' ? Phone::normalize($phone) : null;
            }
            if (! empty($data['account_password'])) {
                $userFill['password'] = Hash::make((string) $data['account_password']);
            }
            if ($userFill !== []) {
                $accountUser->forceFill($userFill)->save();
            }
        }

        return redirect()
            ->route('admin.institutions.edit', $institution)
            ->with('status', __('Kurum ve hesap bilgileri güncellendi.'));
    }

    public function sendAccountPasswordReset(Institution $institution): RedirectResponse
    {
        $accountUser = $institution->accountUser;
        if ($accountUser === null || trim((string) $accountUser->email) === '') {
            return back()->withErrors([
                'account_password_reset' => __('Bağlı kurum hesabı veya e-posta bulunamadı.'),
            ]);
        }

        $status = \Illuminate\Support\Facades\Password::sendResetLink(['email' => $accountUser->email]);

        return $status === \Illuminate\Support\Facades\Password::RESET_LINK_SENT
            ? back()->with('status', __('Şifre sıfırlama bağlantısı kurum hesabına gönderildi.'))
            : back()->withErrors(['account_password_reset' => __($status)]);
    }
}
