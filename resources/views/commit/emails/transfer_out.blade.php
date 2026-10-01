@extends('commit.emails.layout')

@section('headline')
    📤 Transfer OUT Notification
@endsection

@section('intro')
    <p>A new <strong>Transfer OUT</strong> has been created from <strong>{{ $fromStore }}</strong> to
        <strong>{{ $toStore }}</strong>.</p>
@endsection
