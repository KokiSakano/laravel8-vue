<?php

namespace App\Http\Controllers;

use App\Models\Good;
use App\Models\Reply;
use App\Models\Whisper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GoodController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $goods = Good::all();
        $authId = Auth::id();
        $authWhisperGoods = [];
        $authReplyGoods = [];
        foreach ($goods as $good) {
            if ($good->user_id === $authId && $good->reply_id == null) {
                $authWhisperGoods[] = $good->whisper_id;
            } else if ($good->user_id === $authId && $good->whisper_id == null) {
                $authReplyGoods[] = $good->reply_id;
            }
        }
        return array("authWhisperGoods" => $authWhisperGoods, "authReplyGoods" => $authReplyGoods);
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
        $authId = Auth::id();
        $goods = Good::all();
        if ($request->whispType != "reply") {
            foreach ($goods as $good) {
                if ($good->user_id == $authId && $good->whisper_id == $request->id) {
                    return response('Already Reported', 208);
                }
            }
            $newdata = [
                'user_id' => $authId,
                'whisper_id' => $request->id,
                'reply_id' => null,
            ];
            $whisper = Whisper::find($request->id);
            $update = [
                'good' => $whisper->good + 1,
            ];
            $whisper->update($update);
        } else {
            foreach ($goods as $good) {
                if ($good->user_id == $authId && $good->reply_id == $request->id) {
                    return response('Already Reported', 208);
                }
            }
            $newdata = [
                'user_id' => $authId,
                'whisper_id' => null,
                'reply_id' => $request->id,
            ];
            $reply = Reply::find($request->id);
            $update = [
                'good' => $reply->good + 1,
            ];
            $reply->update($update);
        }
        Good::create($newdata);
        return response('OK', 200);
    }

    public function deStore(Request $request)
    {
        $authId = Auth::id();
        $goods = Good::all();
        if ($request->whispType != "reply") {
            $whisper = Whisper::find($request->id);
            $delOrNot = 0;
            foreach ($goods as $good) {
                if ($good->user_id == $authId && $good->whisper_id == $whisper->id) {
                    $good->delete();
                    $delOrNot = 1;
                }
            }
            if ($delOrNot == 0) return response('Already Reported', 208);
            $update = [
                'good' => $whisper->good - 1,
            ];
            $whisper->update($update);
        } else {
            $reply = Reply::find($request->id);
            $delOrNot = 0;
            foreach ($goods as $good) {
                if ($good->user_id == $authId && $good->reply_id == $reply->id) {
                    $good->delete();
                    $delOrNot = 1;
                }
            }
            if ($delOrNot == 0) return response('Already Reported', 208);
            $update = [
                'good' => $reply->good - 1,
            ];
            $reply->update($update);
        }
        return response('OK', 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Good  $good
     * @return \Illuminate\Http\Response
     */
    public function show(Good $good)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Good  $good
     * @return \Illuminate\Http\Response
     */
    public function edit(Good $good)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Good  $good
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Good $good)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Good  $good
     * @return \Illuminate\Http\Response
     */
    public function destroy(Good $good)
    {
        //
    }
}
