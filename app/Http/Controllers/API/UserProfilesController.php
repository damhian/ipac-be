<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserprofileRequest;
use App\Models\Idcard;
use App\Traits\HttpResponses;
use App\Models\Userprofiles;
use App\Models\Usergallery;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class UserprofilesController extends Controller
{

    use HttpResponses;

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserprofileRequest $request)
    {
        try {

            $userProfiles = Userprofiles::where('alumni_id', Auth::id())->first();
            // Check user profiles
            if ($userProfiles) {

                $userProfiles->alumni_id         = Auth::id();
                // $userProfiles->nomor_anggota     = $request->nomor_anggota;
                $userProfiles->profile_image_id  = Auth::id();
                $userProfiles->license_number    = $request->license_number;
                $userProfiles->first_name        = $request->first_name;
                $userProfiles->last_name         = $request->last_name;
                $userProfiles->tahun_masuk       = $request->tahun_masuk;
                $userProfiles->tahun_lulus       = $request->tahun_lulus;
                $userProfiles->training_program  = $request->training_program;
                $userProfiles->batch             = $request->batch;
                $userProfiles->current_job       = $request->current_job;
                $userProfiles->current_workplace = $request->current_workplace;
                $userProfiles->birth_place       = $request->birth_place;
                $userProfiles->date_of_birth     = $request->date_of_birth;
                $userProfiles->address           = $request->address;
                $userProfiles->phone_number      = $request->phone_number;
                $userProfiles->phone_number_code = $request->phone_number_code;
                $userProfiles->gender            = $request->gender;

                $userGallery = $userProfiles->userGallery ?? new Usergallery();

                //Update image
                if($request->hasFile('image')){
                    // Delete the old file if exist
                    if ($userProfiles->userGallery) {
                        Storage::disk('public')->delete($userProfiles->userGallery->image_url);
                    }

                    $userGallery->alumni_id = $userProfiles->alumni_id;
                    
                    $image = $request->file('image');
                    $path = $image->store('user_gallery', 'public');
                    $userGallery->image_url = $path;

                    $userGallery->delete();

                    $userGallery->save();

                    $userProfiles->save();
                }

                $userIdcards = Idcard::where('alumni_id', Auth::id())->first();

                if (!$userIdcards) {
                    Idcard::create([
                        'alumni_id'         => $userProfiles->alumni_id,
                        'nomor_anggota'     => $userProfiles->nomor_anggota,
                        'first_name'        => $userProfiles->first_name,
                        'last_name'         => $userProfiles->last_name,
                        'image_url'         => $userGallery->image_url,
                    ]);
                } else {
                    $userIdcards->alumni_id     = $userProfiles->alumni_id;
                    $userIdcards->nomor_anggota = $userProfiles->nomor_anggota;
                    $userIdcards->first_name    = $userProfiles->first_name;
                    $userIdcards->last_name     = $userProfiles->last_name;
                    $userIdcards->image_url     = $userProfiles->userGallery;
                }

                $userProfiles->save();

                DB::commit();

            } else {
                
                // Create User Profile
                $userProfileData        = Userprofiles::create([
                    'alumni_id'         => Auth::id(),
                    'nomor_anggota'     => $this->generateNomorAnggota($request->tahun_lulus),
                    'profile_image_id'  => Auth::id(),
                    'license_number'    => $request->license_number,
                    'first_name'        => $request->first_name,
                    'last_name'         => $request->last_name,
                    'tahun_masuk'       => $request->tahun_masuk,
                    'tahun_lulus'       => $request->tahun_lulus,
                    'training_program'  => $request->training_program,
                    'batch'             => $request->batch,
                    'current_job'       => $request->current_job,
                    'current_workplace' => $request->current_workplace,
                    'birth_place'       => $request->birth_place,
                    'date_of_birth'     => $request->date_of_birth,
                    'address'           => $request->address,
                    'phone_number'      => $request->phone_number,
                    'phone_number_code' => $request->phone_number_code,
                    'gender'            => $request->gender
                ]);

                // Create User Gallery
                $userGallery = new Usergallery();
                $userGallery->alumni_id = $userProfileData->alumni_id;

                // Handle file upload
                if ($request->hasFile('image')) {
                    $image = $request->file('image');
                    $path = $image->store('user_gallery', 'public');
                    $userGallery->image_url = $path;
                }   

                Idcard::create([
                    'alumni_id'         => $userProfileData->alumni_id,
                    'nomor_anggota'     => $userProfileData->nomor_anggota,
                    'first_name'        => $userProfileData->first_name,
                    'last_name'         => $userProfileData->last_name,
                    'image_url'         => $userGallery->image_url,
                ]);

                $userProfileData->save();
                
                $userGallery->save();
                
                DB::commit();
            }

            return response()->json([
                'message' => 'User profile and gallery successfully created'
            ], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            // return json response
            return response()->json([
                'message' => 'Something went wrong!',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {        
        $userProfiles = Userprofiles::with('userExperiences.company', 'userGallery', 'userIdcards')->where('alumni_id', $id)->first();

        if(!$userProfiles)
            return response()->json([
                'message' => 'user profiles not found!'
            ], 404);

        // Return response success
        return response()->json([
            'User Profiles' => $userProfiles
        ], 200);
    }

    public function showByToken()
    {
        // Get the authenticated user's token
        $user = Auth::user();
                
        // Find the store associated with the token
        $userProfiles = Userprofiles::with('userExperiences.company', 'userGallery', 'userIdcards')->where('alumni_id', $user->id)->first();

        if (!$userProfiles) {
            return response()->json([
                'User Profile' => $userProfiles,
                'message'      => 'User profile not found!'
            ], 200);
        }

        // Return response success
        return response()->json([
            'User Profiles' => $userProfiles
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserprofileRequest $request, string $id)
    {
       try {
        //Find user profile by alumni_id as user id
        $userProfiles = Userprofiles::with('userGallery', 'userIdcard')->where('alumni_id' ,$id)->first();

        if(!$userProfiles)
            return response()->json([
                'message' => 'User profile not found!'
            ], 404);

        if($userProfiles->alumni_id != Auth::id()){
            DB::rollBack();
            return response()->json([
                'message' => 'Unauthorized!'
            ], 401);
        }

        $userProfiles->alumni_id         = Auth::id();
        // $userProfiles->nomor_anggota     = $request->nomor_anggota;
        $userProfiles->profile_image_id  = Auth::id();
        $userProfiles->license_number    = $request->license_number;
        $userProfiles->first_name        = $request->first_name;
        $userProfiles->last_name         = $request->last_name;
        $userProfiles->tahun_masuk       = $request->tahun_masuk;
        $userProfiles->tahun_lulus       = $request->tahun_lulus;
        $userProfiles->training_program  = $request->training_program;
        $userProfiles->batch             = $request->batch;
        $userProfiles->current_job       = $request->current_job;
        $userProfiles->current_workplace = $request->current_workplace;
        $userProfiles->birth_place       = $request->birth_place;
        $userProfiles->date_of_birth     = $request->date_of_birth;
        $userProfiles->address           = $request->address;
        $userProfiles->phone_number      = $request->phone_number;
        $userProfiles->phone_number_code = $request->phone_number_code;
        $userProfiles->gender            = $request->gender;

        $path = null;

        //Update image
        if($request->hasFile('image')){
            // Delete the old file if exist
            if ($userProfiles->userGallery) {
                Storage::disk('public')->delete($userProfiles->userGallery->image_url);
            }

            $userGallery = $userProfiles->userGallery ?? new Usergallery();
            $userGallery->alumni_id = $userProfiles->alumni_id;
            
            // save user Gallery
            $image = $request->file('image');
            $path = $image->store('user_gallery', 'public');
            $userGallery->image_url = $path;

            $userGallery->delete();
            $userGallery->save();

            $userProfiles->save();
        }

        //Find user idcards by alumni_id as user id
        $userIdcards = Idcard::where('alumni_id' ,$id)->first();

        $userIdcards->alumni_id     = Auth::id();
        // $userIdcards->nomor_anggota = $userProfiles->nomor_anggota;
        $userIdcards->first_name    = $request->first_name;
        $userIdcards->last_name     = $request->last_name;
        $userIdcards->image_url     = $path;

        $userIdcards->save();

        $userProfiles->save();

        DB::commit();

        return response()->json([
            'message' => 'User profile successfully updated'
        ], 200);

       } catch (\Throwable $th) {
        DB::rollBack();
         // return json response
         return response()->json([
            'message' => 'something went wrong!',
            'error message' => $th
        ], 500);
       }
    }

    private function generateNomorAnggota($tahunLulus)
    {
        $lastThreeDigit = Userprofiles::where('tahun_lulus', $tahunLulus)->count() + 1;
        $lastThreeDigit = str_pad($lastThreeDigit, 3, '0', STR_PAD_LEFT);

        return $tahunLulus . $lastThreeDigit;
    }
}
