@extends('commit.emails.layout')

@section('headline')
    🔄 Stock Transfer Note
@endsection

@section('intro')
    <p>A new <strong>Stock Transfer Note</strong> has been created from <strong>{{ $fromStore }}</strong> to
        <strong>{{ $toStore }}</strong>.</p>
@endsection
