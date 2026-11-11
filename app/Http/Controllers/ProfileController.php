<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function edit(Request $request)
    {
        return view('profile.edit', ['user' => $request->user()]);
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'no_hp' => ['nullable', 'string', 'max:15'],
            'alamat' => ['nullable', 'string'],
        ]);

        $user->update($data);

        return redirect()->route('profile.edit')->with('success', 'Profil berhasil diperbarui.');
    }

    public function uploadPhoto(Request $request)
    {
        $request->validate([
            'foto_profile' => ['required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ]);

        $photoPath = $request->file('foto_profile')->store('profile', 'public');
        if ($photoPath === false) {
            throw new \RuntimeException('Foto profil gagal disimpan.');
        }

        $user = $request->user();
        $previousPhoto = $user->foto_profile;
        $user->update(['foto_profile' => 'storage/' . $photoPath]);
        $this->deleteStoredPhoto($previousPhoto);

        return redirect()->route('profile.edit')->with('success', 'Foto profil berhasil diunggah.');
    }

    public function deletePhoto(Request $request)
    {
        $user = $request->user();
        $previousPhoto = $user->foto_profile;
        $user->update(['foto_profile' => null]);
        $this->deleteStoredPhoto($previousPhoto);

        return redirect()->route('profile.edit')->with('success', 'Foto profil berhasil dihapus.');
    }

    private function deleteStoredPhoto(?string $photoPath): void
    {
        if (!$photoPath) {
            return;
        }

        $path = str_starts_with($photoPath, 'storage/')
            ? substr($photoPath, strlen('storage/'))
            : $photoPath;

        Storage::disk('public')->delete($path);
    }
}
