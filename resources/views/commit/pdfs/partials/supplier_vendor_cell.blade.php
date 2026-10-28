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
