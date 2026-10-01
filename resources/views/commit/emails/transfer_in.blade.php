@extends('commit.emails.layout')

@section('headline')
    🔄 Transfer IN Notification
@endsection

@section('intro')
    <p>A new <strong>Transfer IN</strong> has been created from <strong>{{ $fromStore }}</strong> to
        <strong>{{ $toStore }}</strong>.</p>
@endsection
