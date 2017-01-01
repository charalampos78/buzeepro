@extends('emails.layout')

@section('content')
    <h2>Password Reset</h2>
    <p>Hi {{ $user->profile->first_name }},</p>
    <div>
        <p>It seems you've requested to reset your password.</p>
        <p>To do so <a href="{{ URL::route('recover', ["token" => $token]) }}">please click here</a></p>
        <p>This link will expire in {{ number_format(Config::get('auth.reminder.expire', 60)) / 60 }} hours.</p>
    </div>
@stop