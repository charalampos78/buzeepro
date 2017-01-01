@extends('layouts.application')


@section('content')
	<h2 style="text-align: center;">
		{{ $code }} : {{ $exception->getMessage() }}
	</h2>
@stop