<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class ContactMessageController extends Controller
{
    public function index(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));
        $filter = (string) $request->query('filter', '');

        $query = ContactMessage::query()->orderByDesc('created_at');

        if ($filter === 'unread') {
            $query->unread();
        } elseif ($filter === 'read') {
            $query->whereNotNull('read_at');
        }

        if ($q !== '') {
            $like = '%'.$q.'%';
            $query->where(function ($w) use ($like, $q): void {
                $w->where('name', 'like', $like)
                    ->orWhere('email', 'like', $like)
                    ->orWhere('topic', 'like', $like)
                    ->orWhere('message', 'like', $like);
                if (ctype_digit($q)) {
                    $w->orWhere('id', (int) $q);
                }
            });
        }

        $messages = $query->paginate(25)->withQueryString();
        $unreadCount = ContactMessage::query()->unread()->count();

        return view('admin.contact-messages.index', compact('messages', 'q', 'filter', 'unreadCount'));
    }

    public function show(ContactMessage $contactMessage): View
    {
        $contactMessage->markAsRead();

        return view('admin.contact-messages.show', [
            'message' => $contactMessage,
        ]);
    }

    public function markUnread(ContactMessage $contactMessage): RedirectResponse
    {
        $contactMessage->forceFill(['read_at' => null])->save();

        return redirect()
            ->route('admin.contact-messages.show', $contactMessage)
            ->with('status', __('Mesaj okunmadı olarak işaretlendi.'));
    }

    public function destroy(ContactMessage $contactMessage): RedirectResponse
    {
        $contactMessage->delete();

        return redirect()
            ->route('admin.contact-messages.index')
            ->with('status', __('Mesaj silindi.'));
    }
}
