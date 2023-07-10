<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\CompaniesRequest;
use App\Models\Companies;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage; // php artisan storage:link => here's the link to get the picture url('')."/storage/{$img}"

class CompaniesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // All Companies
        $companies = Companies::all();

        return response()->json([
            'companies' => $companies
        ], 200);
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
    public function store(CompaniesRequest $request)
    {
        try {
            $imagename = Str::random(32).".".$request->image_url->getClientOriginalExtension();

            // Create Company
            Companies::create([
                'image_url' => $imagename,
                'name' => $request->name,
                'about' => $request->about
            ]);

            // Save Image in Storage folder
            Storage::disk('public')->put($imagename, file_get_contents($request->image_url));

            return response()->json([
                'message' => 'Company Successfully created'
            ], 200);

        } catch (\Exception $e) {
            // return json response
            return response()->json([
                'message' => 'something went wrong!'
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        // Companies Detail
        $companies = Companies::find($id);
        
        if (!$companies) {
            return response()->json([
                'message' => 'company not found!'
            ], 404);
        }

        // Return Response Success
        return response()->json([
            'companies' => $companies
        ], 200);
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
    public function update(CompaniesRequest $request, string $id)
    {
        try {
            // Find company
            $companies = Companies::find($id);
        
            if (!$companies) {
                return response()->json([
                    'message' => 'company not found!'
                ], 404);
            }

            // echo "request : $request->name";
            // echo "description : $request->about";
            $companies->name = $request->name;
            $companies->about = $request->about;

            if ($request->image_url) {
                // Public Storage
                $storage = Storage::disk('public');

                // Old image delete
                if ($storage->exists($companies->image_url)) {
                    $storage->delete($companies->image_url);
                }

                // Image name
                $imagename = Str::random(32).".".$request->image_url->getClientOriginalExtension();
                $companies->image_url = $imagename;

                // Save image in public folder
                $storage->put($imagename, file_get_contents($request->image_url));
            }

            // Update Company
            $companies->save();

            return response()->json([
                'message' => 'Company successfully updated'
            ]);

        } catch (\Exception $e) {
            // Return Json response
            return response()->json([
                'message' => 'Something went wrong!'
            ]);
        }

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Detail info
        $companies = Companies::find($id);
        
        if (!$companies) {
            return response()->json([
                'message' => 'company not found!'
            ], 404);
        }

        //Public storage
        $storage = Storage::disk('public');

        // Check public storage for image
        if ($storage->exists($companies->image_url)) {
            $storage->delete($companies->image_url);
        }

        // Delete company
        $companies->delete();

        // Return json response
        return response()->json([
            'message' => 'Company successfully deleted'
        ], 200);

    }
}
