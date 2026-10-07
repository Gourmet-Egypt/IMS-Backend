@extends('commit.pdfs.layout')

@section('docTitle', 'Purchase Order')

@section('headerTitle', 'Purchase Order')

@section('details')
    @include('commit.pdfs.partials.supplier_details')
@endsection

@section('qtyColumns')
    <th>Qty Ordered</th>
    <th>Qty Received</th>
    <th>Diff</th>
@endsection

@section('itemRows')
    @include('commit.pdfs.partials.rows_supplier')
@endsection

@section('footer')
    @include('commit.pdfs.partials.supplier_footer')
@endsection
