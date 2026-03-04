<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ProfileController extends Controller
{
    /**
     * Show the profile settings page
     */
    public function show()
    {
        $user = Auth::user();
        if ($user->role === 'admin' || $user->role === 'moderator') {
            return view('admin_profile_settings');
        }
        return view('profile_settings');
    }

    /**
     * Update user profile (profile picture and basic info)
     */
    public function update(Request $request)
    {
        $request->validate([
            'profile_picture' => 'nullable|image|mimes:jpeg,png,gif,webp|max:5120', // 5MB
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
        ]);

        $user = Auth::user();

        // Handle profile picture upload
        if ($request->hasFile('profile_picture')) {
            try {
                // Delete old profile picture if it exists
                if ($user->profile_picture && Storage::disk('public')->exists(str_replace('storage/', '', $user->profile_picture))) {
                    Storage::disk('public')->delete(str_replace('storage/', '', $user->profile_picture));
                }

                // Process and store the image
                $file = $request->file('profile_picture');
                $filename = 'profile_' . $user->id . '_' . time() . '.webp';
                
                // Create directory if it doesn't exist
                Storage::disk('public')->makeDirectory('profiles', 0755, true);

                // Read the image file and convert to WebP
                $manager = new ImageManager(new Driver());
                $image = $manager->read($file->getRealPath());
                
                // Resize image to reasonable dimensions (max 500x500)
                $image = $image->scaleDown(500, 500);

                // Convert to WebP and store
                $webpContent = $image->toWebp(85);
                Storage::disk('public')->put('profiles/' . $filename, $webpContent);

                // Update user profile picture path
                $user->profile_picture = 'storage/profiles/' . $filename;
            } catch (\Exception $e) {
                return back()->with('error', 'Failed to upload profile picture: ' . $e->getMessage());
            }
        }

        // Update basic info
        if ($request->filled('first_name')) {
            $user->first_name = $request->first_name;
        }
        if ($request->filled('last_name')) {
            $user->last_name = $request->last_name;
        }

        $user->save();

        return back()->with('success', 'Profile updated successfully!');
    }
}
