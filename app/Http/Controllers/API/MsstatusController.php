<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\MsstatusRequest;
use App\Models\Ms_status;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MsstatusController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $ms_status = Ms_status::all();

        return response()->json([
            'status' => $ms_status
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(MsstatusRequest $request)
    {
        try {
            Ms_status::create([
                "status" => $request->status
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Status succesfully created'
            ], 200);

        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'message' => 'something went wrong!',
                'error message' => $th
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $ms_status = Ms_status::find($id);

        if(!$ms_status)
            return response()->json([
                'message' => 'Status not found!'
            ], 404);

        return response()->json([
            'status' => $ms_status
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(MsstatusRequest $request, string $id)
    {
        try {
            $ms_status = Ms_status::find($id);

            if(!$ms_status)
                return response()->json([
                    'message' => 'Status not found!'
                ]);
            
            $ms_status->status = $request->status;

            $ms_status->save();

            DB::commit();
            
            return response()->json([
                'message' => 'Status successfully updated'
            ], 200);

        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'message' => 'something went wrong!',
                'error message' => $th
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $ms_status = Ms_status::find($id);

        if(!$ms_status)
            return response()->json([
                'message' => 'Status not found!'
            ], 404);
        
        $ms_status->delete();

        return response()->json([
            'message' => 'Status successfully deleted'
        ], 200);
    }
}
