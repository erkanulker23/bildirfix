<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

final class ContactPageController extends Controller
{
    public function show(): View
    {
        return view('pages.contact');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255'],
            'topic' => ['nullable', 'string', 'max:80'],
            'message' => ['required', 'string', 'max:4000'],
        ]);

        Log::notice('iletisim.form', [
            'name' => $data['name'],
            'email' => $data['email'],
            'topic' => $data['topic'] ?? null,
            'message' => $data['message'],
            'ip' => $request->ip(),
        ]);

        return back()->with('status', __('Mesajınız alındı. Yakında döneceğiz.'));
    }
}
