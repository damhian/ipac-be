<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StrukturorganisasiRequest;
use App\Models\Strukturorganisasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class StrukturorganisasiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = Strukturorganisasi::orderBy('level');

        $strorg = $query->get();

        return response()->json([
            'Struktur Organisasi' => $strorg
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StrukturorganisasiRequest $request)
    {
        try {

            // Check file image
            if ($request->hasFile('image')) {
                $image  = $request->file('image');
                $path   = $image->store('struktur_organisasi', 'public');
            }

            // Create Struktur Organisasi
            $strorgdata = Strukturorganisasi::create([
                'nama'          => $request->nama,
                'jabatan'       => $request->jabatan,
                'level'         => $request->level,
                'image_url'     => $path,
                'created_by'    => Auth::id(),
            ]);

            $strorgdata->save();

            DB::commit();

            return response()->json([
                'message' => 'Struktur organisasi successfully created'
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
        $strorgdata = Strukturorganisasi::find($id);

        if(!$strorgdata)
            return response()->json([
                'message' => 'data struktur organisasi not found!'
            ], 404);

        return response()->json([
            'Data Struktur Organisasi' => $strorgdata
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StrukturorganisasiRequest $request, string $id)
    {
        try {
            $strorgdata = Strukturorganisasi::find($id);

            if(!$strorgdata)
                return response()->json([
                    'message' => 'Data not found'
                ]);

            $strorgdata->nama       = $request->nama;
            $strorgdata->jabatan    = $request->jabatan;
            $strorgdata->level      = $request->level;

            if($request->hasFile('image')){
                // Delete old file if exist
                if($strorgdata->image_url)
                    Storage::disk('public')->delete($strorgdata->image_url);

                $image                  = $request->file('image');
                $path                   = $image->store('struktur_organisasi', 'public');
                $strorgdata->image_url  = $path;
            }

            $strorgdata->save();

            DB::commit();

            return response()->json([
                'message' => 'Struktur organisasi data successfully updated'
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
        try {
            $strorgdata = Strukturorganisasi::find($id);

            if(!$strorgdata)
                return response()->json([
                    'message' => 'Data not found'
                ], 404);

            Storage::disk('public')->delete($strorgdata->image_url);

            $strorgdata->delete();
                
            DB::commit();

            return response()->json([
                'message' => 'Struktur organisasi data successfully deleted'
            ], 200);
            
        } catch (\Throwable $th) {
            DB::rollBack();
            
            return response()->json([
                'message' => 'something went wrong!',
                'error message' => $th
            ], 500);
        }
    }

    public function search(Request $request){

        $searchTerm = $request->query('nama');

        $strorgdata = Strukturorganisasi::where('nama', 'LIKE', "%{$searchTerm}%")->get();

        return response()->json([
            'Data Struktur Organisasi' => $strorgdata
        ]);
    }
}
