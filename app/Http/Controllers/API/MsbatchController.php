<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\MsbatchRequest;
use App\Models\Ms_batch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MsbatchController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $ms_batch = Ms_batch::all();

        return response()->json([
            'batch' => $ms_batch
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(MsbatchRequest $request)
    {
        try {
            Ms_batch::create([
                "batch" => $request->batch
            ]);

            DB::commit();

            return response()->json([
                'message' => 'batch successfully created' 
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
        $ms_batch = Ms_batch::find($id);

        if(!$ms_batch)
            return response()->json([
                'message' => 'batch not found!'
            ], 404);

        return response()->json([
            'status' => $ms_batch
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(MsbatchRequest $request, string $id)
    {
        try {
            $ms_batch = Ms_batch::find($id);

            if(!$ms_batch)
            return response()->json([
                'message' => 'Batch not found!'
            ], 404);
        
            $ms_batch->batch = $request->batch;

            $ms_batch->save();

            DB::commit();
            
            return response()->json([
                'message' => 'Batch successfully updated'
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
        $ms_batch = Ms_batch::find($id);

        if(!$ms_batch)
            return response()->json([
                'message' => 'Batch not found!'
            ], 404);
        
        $ms_batch->delete();

        return response()->json([
            'message' => 'Batch successfully deleted'
        ], 200);
    }
}
