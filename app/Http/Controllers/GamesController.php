<?php

namespace App\Http\Controllers;

use App\Models\Games;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GamesController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function home()
    {
        //

        $games = Games::all();

        return view('home', compact('games'));
    }

    public function index()
    {
        //

        $games = Games::all();

        return view('portfolio.index', compact('games'));
    }

    public function count()
    {

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //

        return view('portfolio.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255',
            'project_status' => 'max:255',
            'project_type' => 'max:255',
            'project_duration' => 'max:255',
            'software_used' => 'max:255',
            'languages_used' => 'max:255',
            'primary_roles' => 'max:255',
            'file_path' => 'max:255',
            'itch_link' => 'max:255',
            'steam_link' => 'max:255',
            'body' => '',
            'image_path' => 'image|mimes:jpeg,png,jpg,gif|max:20480', // Adjust the allowed file types and size
        ]);

        // Handle file upload
        if ($request->hasFile('image_path')) {
            $originName = $request->file('image_path')->getClientOriginalName();
            $fileName = pathinfo($originName, PATHINFO_FILENAME);
            $extension = $request->file('image_path')->getClientOriginalExtension();
            $fileName = $fileName . '_' . time() . '.' . $extension;

            $request->file('image_path')->move(public_path('media'), $fileName);

            $url = asset('media/' . $fileName);

            // Store image path in variable
            $imagePath = 'media/' . $fileName;

            // Create record in the database
            Games::create([
                'name' => $request->input('name'),
                'project_status' => $request->input('project_status'),
                'project_type' => $request->input('project_type'),
                'project_duration' => $request->input('project_duration'),
                'software_used' => $request->input('software_used'),
                'languages_used' => $request->input('languages_used'),
                'primary_roles' => $request->input('primary_roles'),
                'file_path' => $request->input('file_path'),
                'itch_link' => $request->input('itch_link'),
                'steam_link' => $request->input('steam_link'),
                'body' => $request->input('body'),
                'image_path' => $imagePath, // Assign $imagePath here
            ]);

            return redirect('/')->with("success", "Portfolio Article has been created");
        }

        return back()->withErrors(['image_path' => 'The image file is required.']);
    }




    public function upload(Request $request) : JsonResponse {
        if ($request->hasFile('upload')) {
            $originName = $request->file('upload')->getClientOriginalName();
            $fileName = pathinfo($originName, PATHINFO_FILENAME);
            $extension = $request->file('upload')->getClientOriginalExtension();
            $fileName = $fileName . '_' . time() . '.' . $extension;

            $request->file('upload')->move(public_path('media'), $fileName);

            $url = asset('media/' . $fileName);

            return response()->json(['fileName' => $fileName, 'uploaded'=> 1, 'url' => $url]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        //
        $game = Games::find($id);

        return view('portfolio.show', compact('game'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        //

        $game = Games::findOrFail($id);

        return view('portfolio.edit', compact('game'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $game = Games::findOrFail($id); // Find the existing record

        $request->validate([
            'name' => 'required|max:255',
            'project_status' => 'max:255',
            'project_type' => 'max:255',
            'project_duration' => 'max:255',
            'software_used' => 'max:255',
            'languages_used' => 'max:255',
            'primary_roles' => 'max:255',
            'file_path' => 'max:255',
            'itch_link' => 'max:255',
            'steam_link' => 'max:255',
            'body' => '',
            'image_path' => 'image|mimes:jpeg,png,jpg,gif', // Adjust the allowed file types and size
        ]);

        // Handle file upload
        if ($request->hasFile('image_path')) {
            // Delete the old image file if it exists
            if (Storage::exists($game->image_path)) {
                Storage::delete($game->image_path);
            }

            // Upload and save the new image file
            $imagePath = $request->file('image_path')->store('media');

            $game->update([
                'name' => $request->input('name'),
                'project_status' => $request->input('project_status'),
                'project_type' => $request->input('project_type'),
                'project_duration' => $request->input('project_duration'),
                'software_used' => $request->input('software_used'),
                'languages_used' => $request->input('languages_used'),
                'primary_roles' => $request->input('primary_roles'),
                'file_path' => $request->input('file_path'),
                'itch_link' => $request->input('itch_link'),
                'steam_link' => $request->input('steam_link'),
                'body' => $request->input('body'),
                'image_path' => $imagePath,
            ]);

            return redirect('/')->with("success", "Portfolio Article has been updated");
        }

        // If no new image is uploaded, update other fields without changing the image
        $game->update($request->only(['name', 'project_status', 'project_type', 'project_duration', 'software_used', 'languages_used', 'primary_roles', 'file_path', 'itch_link', 'steam_link', 'body']));

        return redirect('/')->with("success", "Portfolio Article has been updated");
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        //

        $game = Games::findOrFail($id);
        $game->delete();

        return redirect('/')->with("success", "Portfolio Article has been deleted");
    }
}
