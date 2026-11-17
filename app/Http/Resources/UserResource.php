<?php 
 
namespace App\Http\Resources; 
use Illuminate\Http\Request; 
use Illuminate\Http\Resources\Json\JsonResource; 
use Illuminate\Support\Facades\Storage;
 
class UserResource extends JsonResource 
{ 
    public function toArray(Request $request): array 
    { 
        $photoPath = $this->foto_profile;
        if ($photoPath && str_starts_with($photoPath, 'storage/')) {
            $photoPath = substr($photoPath, strlen('storage/'));
        }

        return [ 
            'id' => $this->id, 
            'name' => $this->name, 
            'email' => $this->email, 
            'role' => $this->role, 
            'no_hp' => $this->no_hp, 
            'alamat' => $this->alamat, 
            'foto_profile' => $photoPath ? Storage::disk('public')->url($photoPath) : null,
            'bergabung_sejak' => $this->created_at?->format('Y-m-d'), 
        ]; 
    } 
}