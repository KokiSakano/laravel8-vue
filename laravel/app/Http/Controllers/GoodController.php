<?php

namespace App\Http\Controllers;

use App\Models\Good;
use App\Models\User;
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
        $authGoods = [];
        foreach ($goods as $good) {
            if ($good->user_id === $authId) {
                $authGoods[] = $good->whisper_id;
            }
        }
        return $authGoods;
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
    public function store($id)
    {
        $authId = Auth::id();
        $newdata = [
            'user_id' => $authId,
            'whisper_id' => $id,
        ];
        $goods = Good::all();
        foreach ($goods as $good) {
            if ($good->user_id == $authId && $good->whisper_id == $id) {
                return response('Already Reported', 208);
            }
        }
        Good::create($newdata);
        $whisper = Whisper::find($id);
        $update = [
            'good' => $whisper->good + 1,
        ];
        $whisper->update($update);
        return response('OK', 200);
    }

    public function deStore($id)
    {
        $authId = Auth::id();
        $whisperId = Whisper::find($id)->id;
        $goods = Good::all();
        $delOrNot = 0;
        foreach ($goods as $good) {
            if ($good->user_id == $authId && $good->whisper_id == $whisperId) {
                $good->delete();
                $delOrNot = 1;
            }
        }
        if ($delOrNot == 0) {
            return response('Already Reported', 208);
        }
        $whisper = Whisper::find($id);
        $update = [
            'good' => $whisper->good - 1,
        ];
        $whisper->update($update);
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
