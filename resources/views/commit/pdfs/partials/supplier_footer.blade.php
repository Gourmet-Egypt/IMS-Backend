@if(in_array($perspective, ['supplier', 'return_to_supplier']))
    @php($received = $items->filter(fn ($i) => ($i->quantity_received_to_date ?? 0) > 0)->count())
    @php($missing = $items->filter(fn ($i) => $i->quantity_requested !== null && ($i->quantity_received_to_date ?? 0) < $i->quantity_requested)->count())
    <table class="info-table totals">
        <tr>
            <td><span class="bold1">Total count of Received: </span>{{ $received }}</td>
        </tr>
        <tr>
            <td><span class="bold1">Total count of Missing items: </span>{{ $missing }}</td>
        </tr>
    </table>
@endif
<div class="footer">
    <table class="footer-table supplier-footer">
        <tr class="footer-tr">
            <td class="sign-cell">Supplier Signature<div class="signature-line"></div></td>
            <td class="sign-cell">Receiver/WHS Signature<div class="signature-line"></div></td>
            <td class="sign-cell">Store/WHS Manager Signature<div class="signature-line"></div></td>
            <td class="circle-cell"><div class="signature-circle"></div></td>
        </tr>
    </table>
</div>
