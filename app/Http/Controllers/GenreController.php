<?php

namespace App\Http\Controllers;

use App\Models\Genre;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GenreController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index()
    {
        $genres = Genre::all();
        return response()->json([
            'genres' => $genres
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'genre' => 'required|string|unique:genres,genre|'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors'=>$validator->errors()], 422);
        }

        $genre = Genre::create([
            'genre' => $request->genre
        ]);

        return response()->json([
            'success' => true,
            'genre' => $genre
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return JsonResponse
     */
    public function show($id)
    {
        try {
            $genre = Genre::findOrFail($id);
        }catch (\Exception $e){
            return response()->json(['error'=>'Genre Not Found'], 404);
        }

        return response()->json([
            'genre' => $genre,
            'success' => true,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return JsonResponse
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        $genre = Genre::find($id);
        if($genre) {
            $genre->delete();
            return response()->json([
                'success' => 'true',
                'message' => 'Genre Deleted Successfully'
            ]);
        }
        return response()->json(['error'=>'id does not exist'], 404);
    }
}
