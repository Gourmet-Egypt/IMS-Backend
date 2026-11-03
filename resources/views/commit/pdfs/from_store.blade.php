@extends('commit.pdfs.layout')

@section('docTitle', 'Stock Out Transfer Note')

@section('headerTitle', 'Stock Out Transfer Note')

@section('qtyColumns')
    <th>Qty Ordered</th>
    <th>Qty Issued</th>
@endsection

@section('itemRows')
    @include('commit.pdfs.partials.rows_out')
@endsection

@section('details')
    @include('commit.pdfs.partials.transfer_details')
@endsection
