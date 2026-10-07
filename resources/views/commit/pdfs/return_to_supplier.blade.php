@extends('commit.pdfs.layout')

@section('docTitle', 'Return to Supplier')

@section('headerTitle', 'Return to Supplier')

@section('details')
    @include('commit.pdfs.partials.supplier_details')
@endsection

@section('qtyColumns')
    <th>Qty Ordered</th>
    <th>Qty Returned</th>
    <th>Diff</th>
@endsection

@section('itemRows')
    @include('commit.pdfs.partials.rows_supplier')
@endsection

@section('footer')
    @include('commit.pdfs.partials.supplier_footer')
@endsection
