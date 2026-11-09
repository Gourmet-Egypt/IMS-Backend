@if($perspective === 'supplier')
    @php($received = $items->sum(fn ($i) => $i->quantity_received_to_date ?? 0))
    @php($missing = $items->filter(fn ($i) => $i->quantity_requested !== null)->sum(fn ($i) => max(0, $i->quantity_requested - ($i->quantity_received_to_date ?? 0))))
    <table class="info-table totals">
        <tr>
            <td><span class="bold1">Total count of Received: </span>{{ number_format($received, 1) }}</td>
        </tr>
        <tr>
            <td><span class="bold1">Total count of Missing items: </span>{{ number_format($missing, 1) }}</td>
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
