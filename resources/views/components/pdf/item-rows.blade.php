{{--
    Shared item rows for the purchase-order / transfer PDF templates
    (purchase_order, purchase_order_A5, purchase_order_A5_vertical).

    Renders one <tr> per batch row produced by TransformItemsStep.
    Column layout depends on the perspective:
      from_store (Transfer OUT): Ordered, Issued, Diff
      to_store   (Transfer IN) : Ordered, Issued, Received, Diff
    Diff is shown only on the last batch row of an item (null otherwise).
--}}
@props(['items', 'perspective' => null])

@foreach($items as $item)
    <tr>
        <td>{{ $item->lookupcode }}</td>
        <td>{{ $item->description }}</td>
        @if(isset($perspective) && $perspective === 'from_store')
            {{-- Transfer OUT: Show Ordered, Issued, Diff --}}
            <td>{{ $item->quantity_requested !== null ? number_format($item->quantity_requested, 1) : '' }}</td>
            <td>{{ number_format($item->quantity_issued, 1) }}</td>
            <td>{{ $item->diff !== null ? number_format($item->diff, 1) : '' }}</td>
        @else
            {{-- Transfer IN: Show Ordered, Issued, Received, Diff --}}
            <td>{{ $item->quantity_requested !== null ? number_format($item->quantity_requested, 1) : '' }}</td>
            <td>{{ number_format($item->quantity_issued, 1) }}</td>
            <td>{{ number_format($item->quantity_IN, 1) }}</td>
            <td>{{ $item->diff !== null ? number_format($item->diff, 1) : '' }}</td>
        @endif
        <td>{{ $item->production_date ? \Carbon\Carbon::parse($item->production_date)->format('d/m/Y') : '' }}</td>
        <td>{{ $item->expire_date ? \Carbon\Carbon::parse($item->expire_date)->format('d/m/Y') : '' }}</td>
    </tr>
@endforeach
