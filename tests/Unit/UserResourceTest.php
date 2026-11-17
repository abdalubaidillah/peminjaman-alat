<?php

namespace Tests\Unit;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UserResourceTest extends TestCase
{
    public function test_api_profile_photo_url_handles_legacy_storage_prefix(): void
    {
        $user = new User([
            'foto_profile' => 'storage/profile/profile.jpg',
        ]);

        $photoUrl = (new UserResource($user))->toArray(request())['foto_profile'];

        $this->assertSame(Storage::disk('public')->url('profile/profile.jpg'), $photoUrl);
    }
}
