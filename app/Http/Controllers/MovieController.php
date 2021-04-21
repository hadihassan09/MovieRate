<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MovieController extends Controller
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
        $movies = Movie::all();
        return response()->json([
            'movies' => $movies,
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
            'title' => 'required|string|unique:movies',
            'description' => 'string',
            'poster' => 'string',
            'release_date' => 'date'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors'=>$validator->errors()], 422);
        }

        $movie = Movie::create([
            'title' => $request->title,
            'description' => $request->description,
            'poster' => $request->poster,
            'release_date' => $request->release_date
        ]);

        return response()->json([
            'movie' => $movie,
            'success' => true,
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
        $movie = Movie::find($id);
        if($movie)
            return response()->json([
                'movie' => $movie,
            ]);
        return response()->json(['error'=>'id does not exist'], 404);
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
        $movie = Movie::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|unique:movies,title,'.$id,
            'description' => 'string',
            'poster' => 'string',
            'release_date' => 'date'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors'=>$validator->errors()], 422);
        }

        $movie->title = $request->title;
        $movie->description = $request->description;
        if($request->poster)
            $movie->poster = $request->poster;
        $movie->release_date = $request->release_date;
        $movie->save();

        return response()->json([
            'movie' => $movie,
            'success' => true,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        $movie = Movie::find($id);
        if($movie) {
            $movie->delete();
            return response()->json([
                'success' => 'true',
                'message' => 'Movie Deleted Successfully'
            ]);
        }
        return response()->json(['error'=>'id does not exist'], 404);
    }
}
