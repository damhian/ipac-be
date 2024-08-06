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
            $path = $file->store('img_media', 'public');

            // // check the api run on local or hosted on the web
            // $host = $request->getHost();

            // if ($host === 'localhost' || $host === '127.0.0.1') {
            //     // Running on localhost
            //     $fileUrl = asset('storage/' . $path);
            // } else {
            //     // Running on the web server
            //     $fileUrl = asset('public/storage/' . $path);
            // }

            // $fileUrl = url('storage/' .$path);

            $fileUrl = $path;
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
