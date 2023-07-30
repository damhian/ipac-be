<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\CompaniesRequest;
use App\Models\Companies;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
             // Check file and store image in storage folder under banner folder
             if ($request->hasFile('image_url')) {
                $image = $request->file('image_url');
                $path  = $image->store('companies', 'public');
            }

            // Create Company
            Companies::create([
                'image_url' => $path,
                'name' => $request->name,
                'about' => $request->about
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Company Successfully created'
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
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

            $companies->name = $request->name;
            $companies->about = $request->about;

            if ($request->image_url) {
                // Delete old image if exists
                if($companies->image_url)
                    Storage::disk('public')->delete($companies->image_url);

                $image                  = $request->image_url;
                $path                   = $image->store('companies', 'public');
                $companies->image_url   = $path;
            }

            // Update Company
            $companies->save();
            
            DB::commit();

            return response()->json([
                'message' => 'Company successfully updated'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
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

        // Check and delete image if exists
        if($companies->image_url)  
            Storage::disk('public')->delete($companies->image_url);

        // Delete company
        $companies->delete();

        // Return json response
        return response()->json([
            'message' => 'Company successfully deleted'
        ], 200);

    }

    public function search(Request $request) {
        
        $searchTerm = $request->name;
        
        $companies = Companies::where('name', 'like', "%{$searchTerm}%")->get();

        return response()->json([
            'companies' => $companies
        ]);
    }
}
