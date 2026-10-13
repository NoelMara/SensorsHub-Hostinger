<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Helpers\ActivityLogHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ProfileController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        return view('user.profile', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'profile_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ]);

        $data = [
            'name' => $request->name,
        ];

        // Handle profile image upload
        if ($request->hasFile('profile_image')) {
            $cloudinary = new \Cloudinary\Cloudinary([
                'cloud' => [
                    'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
                    'api_key'    => env('CLOUDINARY_API_KEY'),
                    'api_secret' => env('CLOUDINARY_API_SECRET'),
                ],
                'url' => ['secure' => true],
            ]);

            // Delete old image from Cloudinary
            if ($user->profile_image) {
                $parsed = parse_url($user->profile_image);
                $path = $parsed['path'] ?? '';
                $publicId = pathinfo($path, PATHINFO_FILENAME);
                
                try {
                    $cloudinary->uploadApi()->destroy($publicId);
                } catch (\Exception $e) {
                    \Log::warning('Failed to delete old Cloudinary image', ['error' => $e->getMessage()]);
                }
            }

            try {
                $result = $cloudinary->uploadApi()->upload($request->file('profile_image')->getRealPath());
                $data['profile_image'] = $result['secure_url'];
            } catch (\Exception $e) {
                \Log::error('Cloudinary upload failed', ['error' => $e->getMessage()]);
                return back()->with('error', 'Profile image upload failed. Please try again.');
            }
        }

        $user->update($data);

        $changes = [];
        if ($user->wasChanged('name')) $changes[] = 'name';
        if ($user->wasChanged('profile_image')) $changes[] = 'profile picture';
        if (!empty($changes)) {
            ActivityLogHelper::log('updated', 'profile', "updated their " . implode(' and ', $changes));
        }

        return back()->with('success', 'Profile updated successfully!');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        Auth::user()->update([
            'password' => Hash::make($request->password),
        ]);
        ActivityLogHelper::log('changed', 'password', "changed their password");

        return back()->with('success', 'Password updated successfully!');
    }
}