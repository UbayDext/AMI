<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function edit(Request $request)
    {
        return view('profile.edit', ['user' => $request->user()]);
    }

    public function update(Request $request)
    {
        return back()->with('status', 'profile-updated');
    }

    public function destroy(Request $request)
    {
        return back();
    }
}
