<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Dump\trashController;
use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validated();
        $encryption = new trashController();

        if ($request->hasFile('signature')) {

            $manager = new ImageManager(new Driver());

            $image = $manager->read($request->file('signature'));

            $filename = Str::uuid() . '.webp';

            $path = storage_path('app/public/signatures/' . $filename);

            if (!file_exists(storage_path('app/public/signatures'))) {
                mkdir(storage_path('app/public/signatures'), 0755, true);
            }

            $image->scale(width: 600)
                ->toWebp(70)
                ->save($path);

            if ($user->signature) {
                Storage::disk('public')->delete($user->signature);
            }

            $validated['signature'] = 'signatures/' . $filename;
        }

        if ($request->input('section') === 'ai') {

            if (!empty($validated['openai_api_key']) && $validated['openai_api_key'] !== '************') {
                $user->openai_api_key = encrypt($validated['openai_api_key']);
            }

            if (array_key_exists('ai_prompt', $validated)) {
                $user->ai_prompt = $validated['ai_prompt'];
            }

            $user->save();

            return Redirect::route('page' , ['token' => $encryption->encrypt('profile')])->with('status', 'ai-settings-updated');
        }

        $user->fill($validated);

        $user->save();

        return Redirect::route('page' , ['token' => $encryption->encrypt('profile')])->with('status', 'profile-updated');
    }


    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
