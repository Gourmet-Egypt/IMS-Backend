@php($received = $items->sum('quantity_issued'))
@php($missing = $items->filter(fn ($i) => $i->diff !== null && $i->diff < 0)->sum(fn ($i) => -$i->diff))
<table class="info-table totals">
    <tr>
        <td><span class="bold1">Total count of {{ $perspective === 'return_to_supplier' ? 'Returned' : 'Received' }}: </span>{{ number_format($received, 1) }}</td>
    </tr>
    <tr>
        <td><span class="bold1">Total count of Missing items: </span>{{ number_format($missing, 1) }}</td>
    </tr>
</table>
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
