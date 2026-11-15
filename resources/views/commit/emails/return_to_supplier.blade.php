@extends('commit.emails.layout')

@section('headline')
    ↩️ Return to Supplier
@endsection

@section('intro')
    <p>A new <strong>Return to Supplier</strong> has been created from <strong>{{ $fromStore }}</strong> to
        <strong>{{ $toStore }}</strong>.</p>
@endsection
