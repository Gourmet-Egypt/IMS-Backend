@php($supplier = $purchaseOrder->supplier)
<table class="ship-table">
    <tr>
        <td class="ship-title" style="width: 50%">VENDOR</td>
        <td class="ship-title" style="width: 50%">SHIP TO</td>
    </tr>
    <tr>
        <td class="ship-content">
            <span class="bold1">Supplier Code: </span>
            {{ $supplier->Code ?? '' }}<br/>
            <span class="bold1">Contact or Department: </span>
            {{ $supplier->ContactName ?? '' }}<br/>
            <span class="bold1">Supplier Name: </span>
            {{ $supplier->SupplierName ?? '' }}<br/>
            <span class="bold1">Address: </span>
            {{ collect([$supplier->Address1 ?? '', $supplier->Address2 ?? '', $supplier->City ?? '', $supplier->Country ?? ''])->map(fn ($v) => trim($v, " ,"))->filter()->implode(', ') }}<br/>
            <span class="bold1">Phone: </span>
            {{ $supplier->PhoneNumber ?? '' }}<br/>
            <span class="bold1">Fax: </span>
            {{ $supplier->FaxNumber ?? '' }}
        </td>
        <td class="ship-content">
            <span class="bold1">Store ID: </span>
            {{ $purchaseOrder->StoreID }}<br/>
            <span class="bold1">Contact or Department: </span><br/>
            <span class="bold1">Store Name: </span>
            {{ $purchaseOrder->currentStore->Name ?? '' }}<br/>
            <span class="bold1">Address: </span>
            {{ $purchaseOrder->currentStore->Address1 ?? '' }}<br/>
            <span class="bold1">Phone: </span>
            {{ $purchaseOrder->currentStore->PhoneNumber ?? '' }}<br/>
            <span class="bold1">Fax: </span>
            {{ $purchaseOrder->currentStore->FaxNumber ?? '' }}
        </td>
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
        <td class="date-cell"></td>
        <td class="date-cell">{{ $condition->supplier_invoice_number ?? '' }}</td>
    </tr>
</table>
<table class="info-table">
    <tr>
        <td><span class="bold1">Vehicle Type: </span>{{ $condition->vehicle_type ?? '' }}</td>
        <td><span class="bold1">Supplier Return Policy: </span></td>
        <td><span class="bold1">Start Time: </span></td>
    </tr>
    <tr>
        <td><span class="bold1">Item Temp: </span>{{ $condition->item_temp ?? '' }}</td>
        <td><span class="bold1">Payment Term: </span>{{ $supplier->Terms ?? '' }}</td>
        <td><span class="bold1">End of Time: </span></td>
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
