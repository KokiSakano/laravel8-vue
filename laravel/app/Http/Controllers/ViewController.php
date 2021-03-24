<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class ViewController extends Controller
{
    public function profile($userId)
    {
        $user = User::find($userId);
        if (is_null($user)) {
            return redirect('/');
        }
        $data['userInfo'] = $user;
        return view('profile', $data);
    }
}
