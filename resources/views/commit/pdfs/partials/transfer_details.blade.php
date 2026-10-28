<!-- SHIP (physical goods flow — driven by POType, not perspective) -->
<table class="ship-table">
    <tr>
        <td class="ship-title" style="width: 50%">SHIP FROM</td>
        <td class="ship-title" style="width: 50%">SHIP TO</td>
    </tr>
    <tr>
        @if($purchaseOrder->POType == 2)
            <td class="ship-content">
                <span class="bold1">Store ID: </span>
                {{ $purchaseOrder->otherStore->ID ?? '' }}<br/>
                <span class="bold1">Contact or Department: </span>
                <!-- {{ $purchaseOrder->OtherStoreID }} --><br/>
                <span class="bold1">Store Name: </span>
                {{ $purchaseOrder->otherStore->Name ?? '' }}<br/>
                <span class="bold1">Address: </span>
                {{ $purchaseOrder->otherStore->Address1 ?? '' }}<br/>
                <span class="bold1">Phone: </span>
                {{ $purchaseOrder->otherStore->PhoneNumber ?? '' }}<br/>
                <span class="bold1">Fax: </span>
                {{ $purchaseOrder->otherStore->FaxNumber ?? '' }}
            </td>
            <td class="ship-content">
                <span class="bold1">Store ID: </span>
                {{ $purchaseOrder->StoreID }}<br/>
                <span class="bold1">Contact or Department: </span>
                <!-- {{ $purchaseOrder->StoreID }} --><br/>
                <span class="bold1">Store Name: </span>
                {{ $purchaseOrder->currentStore->Name ?? '' }}<br/>
                <span class="bold1">Address: </span>
                {{ $purchaseOrder->currentStore->Address1 ?? '' }}<br/>
                <span class="bold1">Phone: </span>
                {{ $purchaseOrder->currentStore->PhoneNumber ?? '' }}<br/>
                <span class="bold1">Fax: </span>
                {{ $purchaseOrder->currentStore->FaxNumber ?? '' }}
            </td>
        @else
            <td class="ship-content">
                <span class="bold1">Store ID: </span>
                {{ $purchaseOrder->StoreID }}<br/>
                <span class="bold1">Contact or Department: </span>
                <!-- {{ $purchaseOrder->StoreID }} --><br/>
                <span class="bold1">Store Name: </span>
                {{ $purchaseOrder->currentStore->Name ?? '' }}<br/>
                <span class="bold1">Address: </span>
                {{ $purchaseOrder->currentStore->Address1 ?? '' }}<br/>
                <span class="bold1">Phone: </span>
                {{ $purchaseOrder->currentStore->PhoneNumber ?? '' }}<br/>
                <span class="bold1">Fax: </span>
                {{ $purchaseOrder->currentStore->FaxNumber ?? '' }}
            </td>
            <td class="ship-content">
                <span class="bold1">Store ID: </span>
                {{ $purchaseOrder->otherStore->ID ?? '' }}<br/>
                <span class="bold1">Contact or Department: </span>
                <!-- {{ $purchaseOrder->OtherStoreID }} --><br/>
                <span class="bold1">Store Name: </span>
                {{ $purchaseOrder->otherStore->Name ?? '' }}<br/>
                <span class="bold1">Address: </span>
                {{ $purchaseOrder->otherStore->Address1 ?? '' }}<br/>
                <span class="bold1">Phone: </span>
                {{ $purchaseOrder->otherStore->PhoneNumber ?? '' }}<br/>
                <span class="bold1">Fax: </span>
                {{ $purchaseOrder->otherStore->FaxNumber ?? '' }}
            </td>
        @endif
    </tr>
</table>
<!-- SHIP -->
<table class="ship-table">
    <tr>
        <td class="ship-title">GTRN Stare Time</td>
        <td class="ship-title">GTRN End of Time</td>
        <td class="ship-title">Lead Time</td>
    </tr>
    @php($process = $purchaseOrder->processStart)
    <tr>
        <td class="ship-content gtrn-time">{{ $process?->startLabel() }}</td>
        <td class="ship-content gtrn-time">{{ $process?->endLabel() }}</td>
        <td class="ship-content gtrn-time">{{ $process?->leadTimeLabel() }}</td>
    </tr>
</table>
<!-- VEHICLE -->
<div class="section-title">Vehicle Info</div>
<table class="info-table vehicle-info">
    <tr>
        <td>
            <span class="bold1">Vehicle Type: </span>
            {{ $condition->vehicle_type ?? 'N/A' }}
        </td>
        <td>
            <span class="bold1">Driver Name: </span>
            {{ $condition->Driver_name ?? '' }}
        </td>
    </tr>
    <tr>
        <td>
            <span class="bold1">Vehicle Number: </span>
            {{ $condition->Vehicle_number ?? '' }}
        </td>
        <td>
            <span class="bold1">GE Receiver name: </span>
            {{ $condition->receiver_name ?? '' }}
        </td>
    </tr>
    <tr>
        <td>
            <span class="bold1">Item Temp: </span>
            {{ $condition->item_temp ?? '' }}
        </td>
        <td>
            <span class="bold1">Car Temperature (In): </span>
            {{ $condition->vehicle_tempIN ?? 'N/A' }}
        </td>
    </tr>
    <tr>
        <td>
            <span class="bold1">Car Temperature (Out): </span>
            {{ $condition->vehicle_tempOut ?? 'N/A' }}
        </td>
        <td>
            <span class="bold1">Seal # : </span>
            {{ $condition->seal_number ?? '' }}
        </td>
    </tr>
    <tr>
        <td>
            <span class="bold1">Delivery Permit Number: </span>
            {{ $condition->delivery_permit_number ?? '' }}
        </td>
        <td>
            <span class="bold1">TRF Division: </span>
            {{ $condition->trf_division ?? '' }}
        </td>
    </tr>
</table>
