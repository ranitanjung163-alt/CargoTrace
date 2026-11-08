<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuditLogController extends Controller
{
    public function index(Request $request): View
    {
        /** @var User $user */
        $user = Auth::user();

        if (! $user->isAdmin()) {
            abort(403, 'Only administrators can inspect audit logs.');
        }

        $query = AuditLog::with('user');

        if ($request->filled('action')) {
            $query->where('action', $request->input('action'));
        }

        if ($request->filled('entity_type')) {
            $query->where('entity_type', $request->input('entity_type'));
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->input('user_id'));
        }

        $logs = $query->latest('created_at')->paginate(20)->withQueryString();
        $users = User::orderBy('name')->get();
        $actions = AuditLog::distinct()->pluck('action');

        return view('audit_logs.index', [
            'logs' => $logs,
            'users' => $users,
            'actions' => $actions,
        ]);
    }
}
