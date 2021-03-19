<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Whisper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
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
            $thumbnail = $user->thumbnail;
        } else {
            $thumbnailFile = $request->file;
            $fileName = 'thumbnail_' . Auth::id() . '.' . $thumbnailFile->getClientOriginalExtension();
            $thumbnailFile->storeAs('public/profile_images/', $fileName);
            $thumbnail = 'storage/profile_images/' . $fileName;
        }
        $update = [
            'name' => $request->name,
            'email' => $request->email,
            'thumbnail' => $thumbnail,
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
