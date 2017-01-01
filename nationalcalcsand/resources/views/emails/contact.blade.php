@extends('emails.layout')

@section('content')
    
    <h2>Contact Form</h2>

        <div>
            <b>From:</b> {{ $name }}
        </div>
        @if ($username)
            <div>
                <b>Username:</b> {{ $username }}
            </div>
        @endif
        <div>
            <b>Email:</b> {{ $email }}
        </div>
        <div>
            <b>Subject:</b> {{ $subject }}
        </div>
        <div>
            <b>Body:</b>
            <div style="white-space: pre-wrap">{{ $body }}</div>
        </div>

@stop