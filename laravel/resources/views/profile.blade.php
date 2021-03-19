@extends('layouts.app')

@section('content')
@if ($userInfo->id==Auth::id())
<myprofile-component></myprofile-component>
@else
<profile-component :watch-user="{{$userInfo}}"></profile-component>
@endif
@endsection