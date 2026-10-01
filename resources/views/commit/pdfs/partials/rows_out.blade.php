{{-- Transfer OUT (from_store) item rows: Ordered, Issued, Diff. --}}
@foreach($items as $item)
    <tr>
        <td>{{ $item->lookupcode }}</td>
        <td>{{ $item->description }}</td>
        <td>{{ $item->quantity_requested !== null ? number_format($item->quantity_requested, 1) : '' }}</td>
        <td>{{ number_format($item->quantity_issued, 1) }}</td>
        <td>{{ $item->diff !== null ? number_format($item->diff, 1) : '' }}</td>
        <td>{{ $item->production_date ? \Carbon\Carbon::parse($item->production_date)->format('d/m/Y') : '' }}</td>
        <td>{{ $item->expire_date ? \Carbon\Carbon::parse($item->expire_date)->format('d/m/Y') : '' }}</td>
    </tr>
@endforeach
