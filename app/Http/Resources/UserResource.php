<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        $userData = [
            'id'                => $this->id,
            'username'          => $this->username,
            'email'             => $this->email,
            'role'              => $this->role,
            'current_status'    => $this->current_status
        ];

        // Include userProfiles if it's available
        $userProfiles = $this->userProfiles ?? null;

        if ($userProfiles !== null) {
            $userData['userProfiles'] = [
                'alumni_id'         => $userProfiles->alumni_id,
                'first_name'        => $userProfiles->first_name,
                'last_name'         => $userProfiles->last_name,
                'training_program'  => $userProfiles->training_program,
                'batch'             => $userProfiles->batch,
                'tahun_masuk'       => $userProfiles->tahun_masuk,
                'tahun_lulus'       => $userProfiles->tahun_lulus,
                'nomor_anggota'     => $userProfiles->nomor_anggota,
                // 'current_job'       => $userProfiles->current_job == null ? '-' : $userProfiles->current_job
                'current_job'       => $userProfiles->current_job
            ];
        }
        
        return $userData;
    }
}
