<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LoginMethodSetting;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LoginMethodController extends Controller
{
    public function index()
    {
        $loginMethods = LoginMethodSetting::all();
        return view('admin.login-methods.index', compact('loginMethods'));
    }

    public function update(Request $request, LoginMethodSetting $loginMethod)
    {
        $request->validate([
            'is_enabled' => 'sometimes|boolean',
            'display_name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'settings' => 'nullable|array'
        ]);

        $updateData = [];

        if ($request->has('is_enabled')) {
            $updateData['is_enabled'] = $request->boolean('is_enabled');
        }

        if ($request->has('display_name')) {
            $updateData['display_name'] = $request->display_name;
        }

        if ($request->has('description')) {
            $updateData['description'] = $request->description;
        }

        if ($request->has('settings')) {
            $updateData['settings'] = $request->settings;
        }

        $loginMethod->update($updateData);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Login method updated successfully.',
                'data' => $loginMethod->fresh()
            ]);
        }

        return redirect()->route('admin.login-methods.index')
            ->with('success', 'Login method updated successfully.');
    }

    public function toggle(Request $request, LoginMethodSetting $loginMethod)
    {
        $request->validate([
            'is_enabled' => 'required|boolean'
        ]);

        $loginMethod->update([
            'is_enabled' => $request->boolean('is_enabled')
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Login method status updated successfully.',
            'is_enabled' => $loginMethod->is_enabled
        ]);
    }

    public function getSettings(LoginMethodSetting $loginMethod)
    {
        return response()->json($loginMethod);
    }
}
