<?php

namespace App\Http\Controllers\Admin;

use App\Models\ActionLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActionLogController extends Controller
{
    // Log a new action
    public function store(Request $request)
    {
        $request->validate([
            'note' => 'nullable|string|max:255',
        ]);

        ActionLog::create([
            'user_id' => Auth::check() ? Auth::id() : null,
            'ip_address' => $request->ip(),
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'action' => sprintf('%s %s', $request->method(), $request->fullUrl()),
            'note' => $request->note,
        ]);

        return response()->json(['message' => 'Action logged successfully.'], 201);
    }

    // Retrieve action logs
    public function index()
    {
        $logs = ActionLog::orderBy('created_at', 'desc')->paginate(10);
        return response()->json($logs);
    }
}
