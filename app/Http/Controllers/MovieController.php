<?php

namespace App\Http\Controllers;

use App\Jobs\ExportMovies;
use App\Models\Actor;
use App\Models\Director;
use App\Models\Movie;
use App\Models\Trailer;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use mysql_xdevapi\Exception;

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
        $movies = Movie::
            with('genres')
            ->with('trailers')
            ->get();
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
            'poster' => 'file|mimes:jpg,bmp,png',
            'release_date' => 'date',
            'genres' => 'required|array',
            'genres.*' => 'integer|exists:genres,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors'=>$validator->errors()], 422);
        }
        $poster = '';
        if ($request->hasFile('poster')) {
            $file_extention = $request->file('poster')->extension();
            $request->file('poster')->move(storage_path('/posters'), $request->title.'.'.$file_extention);
            $poster = 'http://moverate/'.Storage::url('posters/'.$request->title.'.'.$file_extention);
        }


        $movie = Movie::create([
            'title' => $request->title,
            'description' => $request->description,
            'poster' => $poster,
            'release_date' => $request->release_date
        ]);
        $movie->genres()->attach($request->genres);

        $movie['genres'] = $movie->genres;
        $movie['trailers'] = [];
        $movie['directors'] = [];
        $movie['ratings'] = [];

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
        if($movie) {
            $movie['genres'] = $movie->genres;
            $movie['trailers'] = $movie->trailers;
            $movie['actors'] = $movie->actors;
            $movie['directors'] = $movie->directors;
            $movie['ratings'] =  $movie->ratings;
            return response()->json([
                'movie' => $movie,
            ]);
        }
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
        try {
            $movie = Movie::findOrFail($id);
        }catch (\Exception $e){
            return response()->json(['error'=>'Movie Not Found'], 404);
        }
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|unique:movies,title,'.$id,
            'description' => 'string',
            'poster' => 'file|mimes:jpg,bmp,png',
            'release_date' => 'date',
            'genres' => 'required|array',
            'genres.*' => 'integer|exists:genres,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors'=>$validator->errors()], 422);
        }

        $poster = '';
        if ($request->hasFile('poster')) {
            $file_extention = $request->file('poster')->extension();
            $request->file('poster')->move(storage_path('/posters'), $request->title.'.'.$file_extention);
            $poster = 'http://moverate/'.Storage::url('posters/'.$request->title.'.'.$file_extention);
        }

        $movie->title = $request->title;
        $movie->description = $request->description;
        if($request->poster)
            $movie->poster = $poster;
        $movie->release_date = $request->release_date;
        $movie->save();

        try {
            $movie->genres()->attach($request->genres);
        }catch (\Exception $exception){
            // genres already exsist.
        }

        $movie['genres'] = $movie->genres;
        $movie['trailers'] = $movie->trailers;
        $movie['actors'] = $movie->actors;
        $movie['directors'] = $movie->directors;
        $movie['ratings'] = $movie->ratings;

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

    /**
     * Add Trailer to the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return JsonResponse
     */
    public function addTrailer(Request $request, $id){
        try {
            $movie = Movie::findOrFail($id);
        }catch (\Exception $e){
            return response()->json(['error'=>'Movie Not Found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'trailer' => 'required|string|unique:trailers,trailer,'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors'=>$validator->errors()], 422);
        }

        $trailer = Trailer::create([
            'trailer' => $request->trailer,
            'movie_id' => $id
        ]);

        $movie['genres'] = $movie->genres;
        $movie['trailers'] = $movie->trailers;
        $movie['actors'] = $movie->actors;
        $movie['directors'] = $movie->directors;
        $movie['ratings'] = $movie->ratings;

        return response()->json([
            'movie' => $movie,
            'success' => true,
        ]);
    }

    /**
     * Remove Trailer to the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return JsonResponse
     */
    public function removeTrailer($id){
        $trailer = Trailer::find($id);
        if($trailer) {
            $trailer->delete();
            return response()->json([
                'success' => 'true',
                'message' => 'Trailer Deleted Successfully'
            ]);
        }
        return response()->json(['error'=>'id does not exist'], 404);
    }

    /**
     * Add Actor to the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return JsonResponse
     */
    public function addActor($id, $actor_id){
        try {
            $movie = Movie::findOrFail($id);
        }catch (\Exception $e){
            return response()->json(['error'=>'Movie Not Found'], 404);
        }
        try {
            $actor = Actor::findOrFail($actor_id);
        }catch (\Exception $e){
            return response()->json(['error'=>'Actor Not Found'], 404);
        }
        if(!$movie->actors->contains($actor)) {
            $movie->actors()->attach($actor);
            $movie->load('actors');
        }else{
            return response()->json(['error'=>'Actor Already Exists'], 304);
        }

        $movie['genres'] = $movie->genres;
        $movie['trailers'] = $movie->trailers;
        $movie['actors'] = $movie->actors;
        $movie['directors'] = $movie->directors;
        $movie['ratings'] = $movie->ratings;

        return response()->json([
            'movie' => $movie,
            'success' => true,
        ]);
    }

    /**
     * Remove Actor to the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return JsonResponse
     */
    public function removeActor($actor_id, $id){
        try {
            $movie = Movie::findOrFail($id);
        }catch (\Exception $e){
            return response()->json(['error'=>'Movie Not Found'], 404);
        }

        try {
            $actor = Actor::findOrFail($actor_id);
        }catch (\Exception $e){
            return response()->json(['error'=>'Actor Not Found'], 404);
        }

        if($movie->actors->contains($actor)) {
            $movie->actors()->detach($actor);
            $movie->load('actors');
        }
        else
            return response()->json(['error'=>'Actor Doesnt Exists'], 304);

        $movie['genres'] = $movie->genres;
        $movie['trailers'] = $movie->trailers;
        $movie['actors'] = $movie->actors;
        $movie['directors'] = $movie->directors;
        $movie['ratings'] = $movie->ratings;

        return response()->json([
            'movie' => $movie,
            'success' => true,
        ]);
    }

    /**
     * Add Director to the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return JsonResponse
     */
    public function addDirector($director_id, $id){
        try {
            $movie = Movie::findOrFail($id);
        }catch (Exception $e){
            return response()->json(['error'=>'Movie Not Found'], 404);
        }

        try {
            $director = Director::findOrFail($director_id);
        }catch (\Exception $e){
            return response()->json(['error'=>'Director Not Found'], 404);
        }

        if(!$movie->directors->contains($director)) {
            $movie->directors()->attach($director);
            $movie->load('directors');
        }else{
            return response()->json(['error'=>'Director Already Exists'], 304);
        }

        $movie['genres'] = $movie->genres;
        $movie['trailers'] = $movie->trailers;
        $movie['actors'] = $movie->actors;
        $movie['directors'] = $movie->directors;
        $movie['ratings'] = $movie->ratings;

        return response()->json([
            'movie' => $movie,
            'success' => true,
        ]);
    }

    /**
     * Remove Director to the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return JsonResponse
     */
    public function removeDirector($director_id, $id){
        try {
            $movie = Movie::findOrFail($id);
        }catch (Exception $e){
            return response()->json(['error'=>'Movie Not Found'], 404);
        }

        try {
            $director = Director::findOrFail($director_id);
        }catch (\Exception $e){
            return response()->json(['error'=>'Director Not Found'], 404);
        }

        if($movie->directors->contains($director)) {
            $movie->directors()->detach($director);
            $movie->load('directors');
        }
        else
            return response()->json(['error'=>'Director Doesnt Exists'], 304);

        $movie['genres'] = $movie->genres;
        $movie['trailers'] = $movie->trailers;
        $movie['actors'] = $movie->actors;
        $movie['directors'] = $movie->directors;
        $movie['ratings'] = $movie->ratings;

        return response()->json([
            'movie' => $movie,
            'success' => true,
        ]);
    }


    /**
     * Returns the Top Amount of the specified resource in storage.
     *
     * @return JsonResponse
     */
    public function topMovies()
    {
        return Queue::push(new ExportMovies());
        return response()->json([
            'success' => true
        ]);
    }
}
