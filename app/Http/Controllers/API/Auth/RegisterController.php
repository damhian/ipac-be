<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Models\Userprofiles;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class RegisterController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        // Validate the input
        $validator = Validator::make($request->all(), [
            'username' => ['required', 'string', 'max:255', Rule::unique('users')],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')],
            // 'password' => ['required', 'min:8'],
            'firstName' => ['required', 'string', 'max:50'],
            'lastName' => ['nullable', 'string', 'max:50'],
            'trainingProgram' => ['required', 'string', 'max:80'],
            'batch' => ['required', 'string', 'max:25'],
            'currentJob' => ['nullable', 'string', 'max:255'],
            'currentStatus' => ['required', 'string', 'max:60'],
            'tahunMasuk' => ['required', 'integer'],
            'tahunLulus' => ['required', 'integer']
        ], [
            'email.unique' => 'Email is already used!',
            'username.unique' => 'Username is already used!',
            'firstName.required' => 'The first name field is required!',
            'firstName.max' => 'The first name may not be greater than :max characters.',
            'lastName.max' => 'The last name may not be greater than :max characters.',
            'trainingProgram.max' => 'The training program may not be greater than :max characters!',
            'batch.max' => 'The batch may not be greater than :max characters.',
            'currentJob.max' => 'The current job may not be greater than :max characters.',
            'currentStatus.max' => 'The current status may not be greater than :max characters.',
            'tahunMasuk.required' => 'The tahun masuk field is required!',
            'tahunMasuk.integer' => 'The tahun masuk must be an integer!',
            'tahunLulus.required' => 'The tahun lulus field is required!',
            'tahunLulus.integer' => 'The tahun lulus must be an integer!'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = User::create([
            'username'          => $request->username,
            'email'             => $request->email,
            'password'          => bcrypt('ipac2023'),
            'current_status'    => strtoupper($request->currentStatus)
        ]);

        Userprofiles::create([
            'alumni_id'         => $user->id,
            'first_name'        => $request->firstName,
            'last_name'         => $request->lastName,
            'training_program'  => $request->trainingProgram,
            'batch'             => $request->batch,
            'current_job'       => $request->currentJob,
            'tahun_masuk'       => $request->tahunMasuk,
            'tahun_lulus'       => $request->tahunLulus,
            'nomor_anggota'     => $this->generateNomorAnggota($request->tahunLulus)
        ]);

        $token = $user->createToken('myAppToken');

        return (new UserResource($user))
        ->additional([
            'token' => $token->plainTextToken,
        ]);
    }

    private function generateNomorAnggota($tahunLulus)
    {
        $lastThreeDigit = Userprofiles::where('tahun_lulus', $tahunLulus)->count() + 1;
        $lastThreeDigit = str_pad($lastThreeDigit, 3, '0', STR_PAD_LEFT);

        return $tahunLulus . $lastThreeDigit;
    }
}