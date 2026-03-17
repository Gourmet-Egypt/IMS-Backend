<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8"/>
    <title>@if(isset($perspective) && $perspective === 'from_store')
            Transfer OUT
        @elseif(isset($perspective) && $perspective === 'to_store')
            Transfer IN
        @else
            Transfer
        @endif</title>
    <style>
        @page {
            margin: 0.5in;
            size: A4 portrait;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: Helvetica, Arial, sans-serif;
            font-size: 10px;
            margin: 0;
            padding: 0;
            color: #000;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
            /* Removed display: flex; justify-content: center; align-items: center; */
        }

        .page {
            width: 100%;
            margin: 0 auto;
            /* Center the page instead of using flexbox */
        }

        /* ================= HEADER ================= */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 6mm;
        }

        .logo {
            width: 50%;
            height: auto;
            margin-bottom: 4px;
            display: block;
        }

        .header-left {
            width: 30%;
            text-align: left;
            font-size: 9px;
        }

        .header-title {
            width: 40%;
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            color: #1E3A8A;
        }

        .header-right {
            width: 30%;
            text-align: right;
            font-size: 9px;
        }

        .box {
            border: 1px solid #000;
            padding: 3px 6px;
            display: inline-block;
            min-width: 30mm;
            text-align: center;
        }

        /* ================= SHIP ================= */
        .ship-table {
            width: 100%;
            border-collapse: collapse;
            margin: 6mm 0;
        }

        .ship-title {
            background: #1E3A8A;
            color: #fff;
            font-weight: bold;
            padding: 4px;
            text-align: left;
            font-size: 12px;
        }

        .ship-content {
            border: 1px solid #000;
            padding: 4px;
            font-size: 10px;
            height: 8mm;
            vertical-align: top;
        }

        .bold1 {
            font-weight: bold;
            font-size: 10px;
        }

        /* ================= VEHICLE ================= */
        .section-title {
            font-weight: bold;
            margin-top: 6mm;
            padding-top: 2mm;
            font-size: 16px;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
            margin-top: 2mm;
        }

        .info-table td {
            padding: 2px 0;
        }

        /* ================= ITEMS ================= */
        table.items {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5mm;
            font-size: 10px;
        }

        table.items th {
            background: #1E3A8A;
            color: #fff;
            padding: 4px;
            border: 1px solid #000;
        }

        table.items td {
            border: 1px solid #000;
            padding: 4px;
            text-align: center;
        }

        table.items td:first-child {
            text-align: left;
        }

        .footer-tr {
            /* Changed from display: flex to table layout */
            width: 50%;
        }

        .footer-tr td {
            width: 50%;
        }

        .footer-tr td:first-child {
            padding-right: 10px;
        }


        /* ================= FOOTER ================= */
        .footer {
            font-size: 10px;
            font-weight: bold;
            margin-bottom: 20px;
            margin-top: 50px;
        }

        .footer-table {
            width: 100%;
            border-collapse: collapse;
        }

        .signature-line {
            border-top: 1px solid #000;
            margin-top: 6mm;
            width: 50%;
        }
    </style>
</head>

<body>
<div class="page">
    <!-- ================= HEADER ================= -->
    <table class="header-table">
        <tr>
            <td class="header-left">
                <img
                    class="logo"
                    src="{{public_path('assets/images/logo.png')}}"
                    alt="logo image"/><br>
                L4 Yamama Center, 3 Taha Hussein st. Zamalek - Cairo<br>
                GOURMETEGYPT.COM | 19339<br>
                Landline: +202 27370617 / 19 / 21 | Ext
            </td>
            <td class="header-title">
                @if(isset($perspective) && $perspective === 'from_store')
                    Transfer OUT
                @elseif(isset($perspective) && $perspective === 'to_store')
                    Transfer IN
                @else
                    Transfer
                @endif
            </td>
            <td class="header-right">
                DATE
                <span class="box">
                        {{
              \Carbon\Carbon::parse($purchaseOrder->DateCreated)->format('Y-m-d
              H:i:s') }} </span><br/><br/>
                PO #
                <span class="box">{{ $purchaseOrder->PONumber }}</span>
            </td>
        </tr>
    </table>
    <!-- SHIP -->
    <table class="ship-table">
        <tr>
            <td class="ship-title">SHIP FROM</td>
            <td class="ship-title">SHIP TO</td>
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
        <tr>
            <td class="ship-content"></td>
            <td class="ship-content"></td>
            <td class="ship-content"></td>
        </tr>
    </table>
    <!-- VEHICLE -->
    <div class="section-title">Vehicle Info</div>
    <table class="info-table">
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
            </td>
        </tr>
        <tr>
            <td>
                <span class="bold1">Item Temp: </span>
                {{ $condition->item_temp ?? '' }}
            </td>
            <td>
                <span class="bold1">GE Receiver name: </span>
                {{ $condition->receiver_name ?? '' }}
            </td>
        </tr>
        <tr>
            <td>
                <span class="bold1">Car Temperature (Out): </span>
                {{ $condition->vehicle_tempOut ?? 'N/A' }}
            </td>
            <td>
                <span class="bold1">Car Temperature (In): </span>
                {{ $condition->vehicle_tempIN ?? 'N/A' }}
            </td>
        </tr>
        <tr>
            <td>
                <span class="bold1">Delivery Permit Number: </span>
                {{ $condition->delivery_permit_number ?? '' }}
            </td>
            <td>
                <span class="bold1">Seal # : </span>
                {{ $condition->seal_number ?? '' }}
            </td>
        </tr>
        <tr>

            <td>
                <span class="bold1">TRF Division: </span>
                {{ $condition->trf_division ?? '' }}
            </td>
        </tr>
    </table>
    <!-- ITEMS -->
    <table class="items">
        <thead>
        <tr>
            <th>ITEM</th>
            <th>DESCRIPTION</th>
            @if(isset($perspective) && $perspective === 'from_store')
                <th>Qty Ordered</th>
                <th>Qty Issued</th>
                <th>Diff</th>
            @else
                <th>Qty Ordered</th>
                <th>Qty Received</th>
                <th>Diff</th>
            @endif
            <th>Prod. Date</th>
            <th>Exp. Date</th>
        </tr>
        </thead>
        <tbody>
        @foreach($items as $item)
            <tr>
                <td>{{ $item->lookupcode }}</td>
                <td>{{ $item->description }}</td>
                @if(isset($perspective) && $perspective === 'from_store')
                    {{-- Transfer OUT: Show Ordered, Issued, Diff (Ordered - Issued) --}}
                    <td>{{ number_format($item->quantity_requested, 1) }}</td>
                    <td>{{ $item->quantity_issued }}</td>
                    <td>{{ number_format($item->quantity_requested - ($item->quantity_issued ), 1) }}</td>
                @else
                    {{-- Transfer IN: Show Ordered, Received, Diff (Received - Ordered) --}}
                    <td>{{ number_format($item->quantity_requested, 1) }}</td>
                    <td>{{ number_format($item->quantity_received, 1) }}</td>
                    <td>{{ number_format($item->quantity_received - $item->quantity_requested, 1) }}</td>
                @endif
                <td>
                    {{ $item->production_date ?
                \Carbon\Carbon::parse($item->production_date)->format('d/m/Y') :
                '' }}
                </td>
                <td>
                    {{ $item->expire_date ?
                \Carbon\Carbon::parse($item->expire_date)->format('d/m/Y') : '' }}
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <!-- FOOTER -->
    <div class="footer">
        <table class="footer-table">
            <tr class="footer-tr">
                <td>
                    Receiver/WMS Operator Signature
                    <div class="signature-line"></div>
                </td>
                <td>
                    Store/WMS Manager Signature
                    <div class="signature-line"></div>
                </td>
            </tr>
        </table>
    </div>
</div>
</body>

</html>
