<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\ImageuploaderRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ImageuploaderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ImageuploaderRequest $request)
    {
       if ($request->image) {
            $file = $request->image;
            $fileName = uniqid() . '.' . $file->getClientOriginalExtension();

             // Store the file in the storage/app/public directory (using the 'public' disk)
            Storage::disk('public')->put($fileName, file_get_contents($file));
            
            // Return a response with the file URL so the client can access it
            $fileUrl = asset('storage/' . $fileName);
       }else{

            $fileUrl = $request->image_url;

       }
       
        return response()->json([
            'message'   => 'file uploaded successfully!',
            'file_url'  => $fileUrl
        ], 200);

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
