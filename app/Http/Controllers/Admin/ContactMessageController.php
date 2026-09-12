<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\Request;

class ContactMessageController extends Controller
{
    public function index(Request $request)
    {
        $data = $request->validate(['status' => ['nullable', 'in:all,unread,read'], 'search' => ['nullable', 'string', 'max:255']]);
        $query = ContactMessage::query();
        if (($data['status'] ?? '') === 'unread') {
            $query->whereNull('read_at');
        } elseif (($data['status'] ?? '') === 'read') {
            $query->whereNotNull('read_at');
        }
        if ($search = trim($data['search'] ?? '')) {
            $query->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('subject', 'like', "%{$search}%");
            });
        }

        return view('admin.contact-messages.index', ['messages' => $query->latest('id')->paginate(20)->withQueryString()]);
    }

    public function show(int $id)
    {
        return view('admin.contact-messages.show', ['message' => ContactMessage::query()->findOrFail($id)]);
    }

    public function read(int $id)
    {
        $message = ContactMessage::query()->findOrFail($id);
        ContactMessage::whereKey($message->id)->whereNull('read_at')->update(['read_at' => now()]);

        return response()->noContent();
    }

    public function notifications()
    {
        return response()->json(['unread_count' => ContactMessage::query()->whereNull('read_at')->count()]);
    }
}
