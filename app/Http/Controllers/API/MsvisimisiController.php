<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\MsvisimisiRequest;
use App\Models\Ms_visi_misi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MsvisimisiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $ms_visimisi = Ms_visi_misi::all();

        return response()->json([
            'visi, misi & about' => $ms_visimisi
        ], 200);
    }

    public function visimisi()
    {
        $visimisi = Ms_visi_misi::whereIn('type', ['visi','misi'])->get();

        return response()->json([
            'visi & misi' => $visimisi
        ], 200);
    }

    public function about()
    {
        $about = Ms_visi_misi::whereIn('type', ['about'])->get();

        return response()->json([
            'about' => $about
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(MsvisimisiRequest $request)
    {
        try {
            Ms_visi_misi::create([
                "type"      => $request->type,
                "content"   => $request->content
            ]);

            DB::commit();

            return response()->json([
                'message' => 'content successfully created' 
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
        $ms_visimisi = Ms_visi_misi::find($id);

        if(!$ms_visimisi)
            return response()->json([
                'message' => 'visi, misi or about not found!'
            ]);
        
        return response()->json([
            'content' => $ms_visimisi
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(MsvisimisiRequest $request, string $id)
    {   
        try {
            $ms_visimisi = Ms_visi_misi::find($id);

            if(!$ms_visimisi)
                return response()->json([
                    'message' => 'visi, misi or about not found!'
                ], 404);

            $ms_visimisi->type = $request->type;
            $ms_visimisi->content = $request->content;

            $ms_visimisi->save();

            DB::commit();

            return response()->json([
                'message' => 'content successfully updated' 
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
        $ms_visimisi = Ms_visi_misi::find($id);

        if(!$ms_visimisi)
            return response()->json([
                'message' => 'visi, misi or about not found!'
            ], 404);
        
        $ms_visimisi->delete();

        return response()->json([
            'message' => 'visi, misi, or about successfully deleted'
        ], 200);
    }
}
