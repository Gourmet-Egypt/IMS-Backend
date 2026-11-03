@extends('commit.emails.layout')

@section('headline')
    📋 Purchase Order Notification
@endsection

@section('intro')
    <p>A new <strong>Purchase Order</strong> has been created.</p>
@endsection

@section('parties')
    {{-- Supplier / non-transfer PO has no from/to store flow. --}}
@endsection
