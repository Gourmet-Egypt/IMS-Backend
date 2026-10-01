@extends('commit.pdfs.layout')

@section('docTitle', 'Transfer')

@section('headerTitle', 'Transfer')

@section('qtyColumns')
    <th>Qty Ordered</th>
    <th>Qty Issued</th>
    <th>Qty Received</th>
    <th>Diff</th>
@endsection

@section('itemRows')
    @include('commit.pdfs.partials.rows_in')
@endsection
