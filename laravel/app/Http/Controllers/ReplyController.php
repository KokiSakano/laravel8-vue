<?php

namespace App\Http\Controllers;

use App\Models\Reply;
use App\Models\User;
use App\Models\Whisper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ReplyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
        $replies = Reply::where('whisper_id', $id)->with('user')->orderBy('created_at')->get();
        $imgPath = [];
        foreach ($replies as $reply) {
            $imgPath[$reply->user->id] = Storage::disk('minio1')->temporaryUrl(
                $reply->user->thumbnail,
                now()->addhour(1)
            );
        };
        return array("replies" => $replies, "imgPath" => $imgPath);
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
        $userId = Auth::id();
        $request->validate([
            'reply' => 'required|max:120',
        ]);
        $newdata = [
            "user_id" => $userId,
            "whisper_id" => $request->id,
            "whisp" => $request->reply,
            "good" => 0,
        ];
        Reply::create($newdata);
        return Response('OK', 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Reply  $reply
     * @return \Illuminate\Http\Response
     */
    public function show(Reply $reply)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Reply  $reply
     * @return \Illuminate\Http\Response
     */
    public function edit(Reply $reply)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Reply  $reply
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
        Reply::find($id)->update($update);
        return response("OK", 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Reply  $reply
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Reply::find($id)->delete();
        return Response("OK", 200);
    }
}
