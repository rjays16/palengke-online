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
            'contacts.*.title' => 'sometimes|string|max:255',
            'contacts.*.subtitle' => 'sometimes|string|max:255',
            'contacts.*.value' => 'sometimes|nullable|string|max:500',
            'contacts.*.type' => 'sometimes|string|in:chat,phone,email,facebook',
            'contacts.*.icon' => 'sometimes|nullable|string|max:50',
            'contacts.*.sort_order' => 'sometimes|integer',
            'contacts.*.enabled' => 'sometimes|boolean',
        ]);

        $editable = ['title', 'subtitle', 'value', 'type', 'icon', 'enabled', 'sort_order'];

        foreach ($validated['contacts'] as $item) {
            $contact = ContactSupport::findOrFail($item['id']);
            $data = array_intersect_key($item, array_flip($editable));
            if (!empty($data)) {
                $contact->fill($data)->save();
            }
        }

        return response()->json([
            'message' => 'Contact support settings updated successfully.',
        ]);
    }
}
