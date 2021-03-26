@extends('layouts.app')

@section('content')
@if ($userInfo->id==Auth::id())
<myprofile-component></myprofile-component>
@else
@if (Auth::check())
<profile-component :watch-user="{{$userInfo}}" :auth-user="{{Auth::user()}}"></profile-component>
@else
<profile-component :watch-user="{{$userInfo}}" :auth-user="{{'null'}}"></profile-component>
@endif
@endif
@endsection