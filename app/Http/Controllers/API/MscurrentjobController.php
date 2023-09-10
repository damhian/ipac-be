<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\MscurrentjobRequest;
use App\Models\Ms_current_job;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MscurrentjobController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $ms_curjob = Ms_current_job::all();

        return response()->json([
            'jobs' => $ms_curjob
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(MscurrentjobRequest $request)
    {
        try {
            Ms_current_job::create([
                "job" => $request->job
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Job succesfully created'
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
        $ms_curjob = Ms_current_job::find($id);

        if(!$ms_curjob)
            return response()->json([
                'message' => 'Job not found!'
            ], 404);

        return response()->json([
            'job' => $ms_curjob
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(MscurrentjobRequest $request, string $id)
    {
        try {
            $ms_curjob = Ms_current_job::find($id);

            if(!$ms_curjob)
                return response()->json([
                    'message' => 'Job not found!'
                ]);
            
            $ms_curjob->job = $request->job;

            $ms_curjob->save();

            DB::commit();
            
            return response()->json([
                'message' => 'Job successfully updated'
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
        $ms_curjob = Ms_current_job::find($id);

        if(!$ms_curjob)
            return response()->json([
                'message' => 'Job not found!'
            ], 404);
        
        $ms_curjob->delete();

        return response()->json([
            'message' => 'Job successfully deleted'
        ], 200);
    }
}
