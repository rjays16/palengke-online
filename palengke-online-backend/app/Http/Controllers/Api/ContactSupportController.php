<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ContactSupport;
use Illuminate\Http\Request;

class ContactSupportController extends Controller
{
    public function index()
    {
        $contacts = ContactSupport::orderBy('sort_order')
            ->where('enabled', true)
            ->get();

        return response()->json([
            'contacts' => $contacts,
        ]);
    }

    public function adminIndex()
    {
        $contacts = ContactSupport::orderBy('sort_order')->get();

        return response()->json([
            'contacts' => $contacts,
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'contacts' => 'required|array',
            'contacts.*.id' => 'required|integer|exists:contact_supports,id',
            'contacts.*.enabled' => 'required|boolean',
        ]);

        foreach ($validated['contacts'] as $item) {
            ContactSupport::where('id', $item['id'])->update(['enabled' => $item['enabled']]);
        }

        return response()->json([
            'message' => 'Contact support settings updated successfully.',
        ]);
    }
}
