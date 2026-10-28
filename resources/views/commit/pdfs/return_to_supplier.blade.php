@extends('commit.pdfs.layout')

@section('docTitle', 'Goods Return Note (GRN)')

@section('headerTitle', 'Goods Return Note (GRN)')

@section('details')
    @include('commit.pdfs.partials.supplier_details')
@endsection

@section('qtyColumns')
    <th>Qty Ordered</th>
    <th>Qty Returned</th>
@endsection

@section('itemRows')
    @include('commit.pdfs.partials.rows_supplier')
@endsection

@section('footer')
    @include('commit.pdfs.partials.supplier_footer')
@endsection
