<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Whisper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user = User::find($id);
        if ($request->file === "null") {
            $fileName = $user->thumbnail;
        } else {
            $thumbnailFile = $request->file;
            $extention = $thumbnailFile->getClientOriginalExtension();
            $fileName = 'profile_images/thumbnail_' . Auth::id() . '.' . $extention;
            $thumbnailFile = \Image::make($thumbnailFile)->resize(100, 100)->encode($extention);
            Storage::cloud()->put($fileName, $thumbnailFile);
        }
        $update = [
            'name' => $request->name,
            'email' => $request->email,
            'thumbnail' => $fileName,
        ];
        $user->update($update);
        return response("OK", 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $deleteUser = User::find($id);
        Whisper::where('user_id', $id)->delete();
        $deleteUser->delete();
        return response("OK", 200);
    }
}
