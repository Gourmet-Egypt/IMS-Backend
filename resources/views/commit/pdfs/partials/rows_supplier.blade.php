{{-- Supplier PO / Return to Supplier item rows: Ordered, Received/Returned (quantity_issued). --}}
@foreach($items as $item)
    <tr>
        <td>{{ $item->lookupcode }}</td>
        <td>{{ $item->description }}</td>
        <td>{{ $item->quantity_requested !== null ? number_format($item->quantity_requested, 1) : '' }}</td>
        @if(($perspective ?? null) === 'supplier')
            <td>{{ $item->quantity_received_to_date !== null ? number_format($item->quantity_received_to_date, 1) : '' }}</td>
        @else
            <td>{{ number_format($item->quantity_issued, 1) }}</td>
        @endif
        <td>{{ $item->production_date ? \Carbon\Carbon::parse($item->production_date)->format('d/m/Y') : '' }}</td>
        <td>{{ $item->expire_date ? \Carbon\Carbon::parse($item->expire_date)->format('d/m/Y') : '' }}</td>
    </tr>
@endforeach
