@php($supplier = $purchaseOrder->supplier)
@php($process = $purchaseOrder->processStart)
<table class="ship-table">
    <tr>
        <td class="ship-title" style="width: 50%">STORE</td>
        <td class="ship-title" style="width: 50%">SUPPLIER</td>
    </tr>
    <tr>
        @include('commit.pdfs.partials.supplier_store_cell')
        @include('commit.pdfs.partials.supplier_vendor_cell')
    </tr>
</table>
<table class="ship-table">
    <tr>
        <td class="ship-title center">Po Date</td>
        <td class="ship-title center">Require date</td>
        <td class="ship-title center">Lead Time</td>
        <td class="ship-title center">Supplier invoice #</td>
    </tr>
    <tr>
        <td class="date-cell">{{ $purchaseOrder->DateCreated ? \Carbon\Carbon::parse($purchaseOrder->DateCreated)->format('d/m/Y') : '' }}</td>
        <td class="date-cell">{{ $purchaseOrder->RequiredDate ? \Carbon\Carbon::parse($purchaseOrder->RequiredDate)->format('d/m/Y') : '' }}</td>
        <td class="date-cell">{{ $process?->leadTimeLabel() }}</td>
        <td class="date-cell">{{ $condition->supplier_invoice_number ?? '' }}</td>
    </tr>
</table>
<table class="info-table">
    <tr>
        <td><span class="bold1">Vehicle Type: </span>{{ $condition->vehicle_type ?? '' }}</td>
        <td><span class="bold1">Supplier Return Policy: </span></td>
        <td><span class="bold1">Start Time: </span>{{ $process?->startLabel() }}</td>
    </tr>
    <tr>
        <td><span class="bold1">Item Temp: </span>{{ $condition->item_temp ?? '' }}</td>
        <td><span class="bold1">Payment Term: </span>{{ $supplier->Terms ?? '' }}</td>
        <td><span class="bold1">End of Time: </span>{{ $process?->endLabel() }}</td>
    </tr>
    <tr>
        <td><span class="bold1">Car Temperature: </span>{{ $condition->vehicle_tempIN ?? '' }}</td>
        <td><span class="bold1">GE Receiver name: </span>{{ $condition->receiver_name ?? '' }}</td>
        <td><span class="bold1">GRN Time: </span></td>
    </tr>
    <tr>
        <td><span class="bold1">Standard Temp: </span></td>
        <td><span class="bold1">Supply Division: </span>{{ $condition->trf_division ?? '' }}</td>
        <td></td>
    </tr>
</table>
