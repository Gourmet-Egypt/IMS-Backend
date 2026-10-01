@extends('commit.pdfs.layout')

@section('docTitle', 'Transfer OUT')

@section('headerTitle', 'Transfer OUT')

@section('qtyColumns')
    <th>Qty Ordered</th>
    <th>Qty Issued</th>
    <th>Diff</th>
@endsection

@section('itemRows')
    @include('commit.pdfs.partials.rows_out')
@endsection
