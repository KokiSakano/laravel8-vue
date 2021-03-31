<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Whisper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class WhisperController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $whispers = Whisper::with('user')->latest()->paginate(10);
        $authId = Auth::id();
        $imgPath = [];
        foreach ($whispers as $whisper) {
            $imgPath[$whisper->user->id] = Storage::disk('minio1')->temporaryUrl(
                $whisper->user->thumbnail,
                now()->addMinutes(1)
            );
        };
        return array("whispers" => $whispers, "loginUserId" => $authId, "imgPath" => $imgPath);
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
        $user = Auth::user();
        $request->validate([
            'whisper' => 'required|max:120',
        ]);
        $newdata = [
            'whisp' => $request->whisper,
            'user_id' => $user->id,
            'good' => 0,
        ];
        $whisper = Whisper::create($newdata);
        return response("OK", 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Whisper  $whisper
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        $user = Auth::user();
        $whispers = Whisper::with('user')->whereHas(
            'user',
            function ($query) use ($user) {
                $query->where('name', $user->name);
            }
        )->latest()->paginate(10);
        $imgPath = [
            $user->id => Storage::disk('minio1')->temporaryUrl(
                $user->thumbnail,
                now()->addMinutes(1)
            )
        ];
        return array("whispers" => $whispers, "loginUser" => $user, "imgPath" => $imgPath);
    }

    public function showUser($id)
    {
        $user = User::find($id);
        $whispers = Whisper::with('user')->whereHas(
            'user',
            function ($query) use ($user) {
                $query->where('name', $user->name);
            }
        )->latest()->paginate(10);
        $imgPath = [
            $user->id => Storage::disk('minio1')->temporaryUrl(
                $user->thumbnail,
                now()->addMinutes(1)
            )
        ];
        return array("whispers" => $whispers, "imgPath" => $imgPath);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Whisper  $whisper
     * @return \Illuminate\Http\Response
     */
    public function edit(Whisper $whisper)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Whisper  $whisper
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        $request->validate([
            'whisp' => 'required|max:120',
        ]);
        $update = [
            'whisp' => $request->whisp,
        ];
        Whisper::find($id)->update($update);
        return response("OK", 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Whisper  $whisper
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Whisper::find($id)->delete();
        return response("OK", 200);
    }
}
