<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SettingsController extends Controller
{
    public function index()
    {
        $superAdmins = User::whereHas('roles', fn($q) => $q->where('name', 'super-admin'))
            ->get();

        $systemStats = [
            'php_version'     => PHP_VERSION,
            'laravel_version' => app()->version(),
            'db_name'         => config('database.connections.mysql.database'),
            'app_env'         => config('app.env'),
            'app_url'         => config('app.url'),
            'cache_driver'    => config('cache.default'),
            'session_driver'  => config('session.driver'),
            'queue_driver'    => config('queue.default'),
        ];

        return view('super-admin.settings', compact('superAdmins', 'systemStats'));
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password'     => 'required|min:8|confirmed',
        ]);

        $user = auth()->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->with('error', 'Current password is incorrect.');
        }

        $user->update(['password' => Hash::make($request->new_password)]);

        return back()->with('success', 'Password updated successfully.');
    }
}