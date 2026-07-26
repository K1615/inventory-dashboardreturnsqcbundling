<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DemoLoginController extends Controller
{
    public function show(Request $request)
    {
        if ($request->session()->has('role')) {
            return redirect()->route('inventory.dashboard');
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:60',
            'role' => 'required|in:admin,manager,staff',
        ]);

        $request->session()->put('role', $validated['role']);
        $request->session()->put(
            'user_name',
            $validated['name'] !== null && $validated['name'] !== ''
                ? $validated['name']
                : ucfirst($validated['role']).' Demo User'
        );

        return redirect()->route('inventory.dashboard');
    }

    public function logout(Request $request)
    {
        $request->session()->forget(['role', 'user_name']);

        return redirect()->route('login');
    }
}
