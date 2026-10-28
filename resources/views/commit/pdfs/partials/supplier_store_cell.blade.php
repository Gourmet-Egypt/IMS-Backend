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
