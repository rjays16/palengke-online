<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CallSignal;
use Illuminate\Http\Request;

class CallSignalController extends Controller
{
    public function start(Request $request)
    {
        $request->validate([
            'conversation_id' => 'required|integer',
            'caller_email' => 'required|email',
            'caller_name' => 'required|string',
            'caller_type' => 'required|in:seller,driver',
            'receiver_email' => 'required|email',
        ]);

        CallSignal::where('caller_email', $request->caller_email)
            ->whereIn('status', ['ringing'])
            ->update(['status' => 'ended']);

        CallSignal::where('receiver_email', $request->receiver_email)
            ->whereIn('status', ['ringing'])
            ->update(['status' => 'ended']);

        $signal = CallSignal::create([
            'conversation_id' => $request->conversation_id,
            'caller_email' => $request->caller_email,
            'caller_name' => $request->caller_name,
            'caller_type' => $request->caller_type,
            'receiver_email' => $request->receiver_email,
            'status' => 'ringing',
        ]);

        return response()->json([
            'signal' => [
                'id' => $signal->id,
                'caller_name' => $signal->caller_name,
                'caller_type' => $signal->caller_type,
                'status' => $signal->status,
            ],
        ]);
    }

    public function check(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $signal = CallSignal::where('receiver_email', $request->email)
            ->where('status', 'ringing')
            ->latest()
            ->first();

        if (!$signal) {
            return response()->json(['signal' => null]);
        }

        return response()->json([
            'signal' => [
                'id' => $signal->id,
                'conversation_id' => $signal->conversation_id,
                'caller_email' => $signal->caller_email,
                'caller_name' => $signal->caller_name,
                'caller_type' => $signal->caller_type,
                'status' => $signal->status,
            ],
        ]);
    }

    public function accept(Request $request, $id)
    {
        $signal = CallSignal::find($id);
        if (!$signal) {
            return response()->json(['message' => 'Call not found'], 404);
        }

        $signal->update(['status' => 'accepted']);
        return response()->json(['status' => 'accepted']);
    }

    public function decline(Request $request, $id)
    {
        $signal = CallSignal::find($id);
        if (!$signal) {
            return response()->json(['message' => 'Call not found'], 404);
        }

        $signal->update(['status' => 'declined']);
        return response()->json(['status' => 'declined']);
    }

    public function end(Request $request, $id)
    {
        $signal = CallSignal::find($id);
        if (!$signal) {
            return response()->json(['message' => 'Call not found'], 404);
        }

        $signal->update(['status' => 'ended']);
        return response()->json(['status' => 'ended']);
    }

    public function callerStatus(Request $request, $id)
    {
        $signal = CallSignal::find($id);
        if (!$signal) {
            return response()->json(['signal' => null]);
        }

        return response()->json([
            'signal' => [
                'id' => $signal->id,
                'status' => $signal->status,
            ],
        ]);
    }
}
