<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\MstrainingRequest;
use App\Models\Ms_training_program;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MstrainingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $ms_training = Ms_training_program::all();

        return response()->json([
            'training program' => $ms_training
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(MstrainingRequest $request)
    {
        try {
            Ms_training_program::create([
                "training_program" => $request->training_program
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Training program succesfully created'
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
        $ms_training = Ms_training_program::find($id);

        if(!$ms_training)
            return response()->json([
                'message' => 'Training program not found!'
            ], 404);

        return response()->json([
            'Training program' => $ms_training
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(MstrainingRequest $request, string $id)
    {
        try {
            $ms_training = Ms_training_program::find($id);

            if(!$ms_training)
                return response()->json([
                    'message' => 'Status not found!'
                ]);
            
            $ms_training->training_program = $request->training_program;

            $ms_training->save();

            DB::commit();
            
            return response()->json([
                'message' => 'Training program successfully updated'
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
        $ms_training = Ms_training_program::find($id);

        if(!$ms_training)
            return response()->json([
                'message' => 'Training program not found!'
            ], 404);
        
        $ms_training->delete();

        return response()->json([
            'message' => 'Training program successfully deleted'
        ], 200);
    }
}
