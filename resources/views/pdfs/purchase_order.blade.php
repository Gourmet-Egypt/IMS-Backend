<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <title>Goods Transfer Receiving Note</title>
    <style>
        @page {
            margin: 8mm 10mm;
            size: A4 portrait;
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
            width: 8.27in;
            margin: 0 auto; /* Center the page instead of using flexbox */
        }

        /* ================= HEADER ================= */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 6mm;
        }

        .logo {
            width: 120px;
            height: auto;
            margin-bottom: 4px;
            display: block;
        }

        .header-left {
            font-size: 9px;
        }

        .header-title {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            color: #1E3A8A;
        }

        .header-right {
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
            font-size: 10px;
        }

        .ship-content {
            border: 1px solid #000;
            padding: 4px;
            font-size: 9px;
            height: 8mm;
            vertical-align: top;
        }

        .bold1 {
            font-weight: bold;
            font-size: 12px;
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
            font-size: 9px;
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
            font-size: 8.5px;
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
            width: 100%;
        }

        .footer-tr td {
            width: 50%;
            display: inline-block;
            vertical-align: top;
        }

        .footer-tr td:first-child {
            padding-right: 10px;
        }


        /* ================= FOOTER ================= */
        .footer {
            font-size: 9px;
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
            width: 80mm;
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
                    alt="logo image"
                />
                L4 Yamama Center, 3 Taha Hussein st. Zamalek - Cairo<br>
                GOURMETEGYPT.COM | 19339<br>
                Landline: +202 27370617 / 19 / 21 | Ext
            </td>
            <td class="header-title">Goods Transfer Receiving Note</td>
            <td class="header-right">
                DATE
                <span class="box">
              {{
              \Carbon\Carbon::parse($purchaseOrder->DateCreated)->format('Y-m-d
              H:i:s') }} </span
                ><br/><br/>
                PO #
                <span class="box">{{ $purchaseOrder->PONumber }}</span>
            </td>
        </tr>
    </table>
    <!-- SHIP -->
    <table class="ship-table">
        <tr>
            <td class="ship-title">SHIP TO</td>
            <td class="ship-title">SHIP FROM</td>
        </tr>
        <tr>
            <td class="ship-content">
                Store ID: {{ $purchaseOrder->StoreID }}<br/>
                Contact or Department: {{ $purchaseOrder->StoreID }}<br/>
                Store Name: {{ $purchaseOrder->currentStore->Name ?? '' }}<br/>
                Address: {{ $purchaseOrder->currentStore->Address1 ?? '' }}<br/>
                Phone: {{ $purchaseOrder->currentStore->PhoneNumber ?? '' }}<br/>
                Fax: {{ $purchaseOrder->currentStore->FaxNumber ?? '' }}
            </td>
            <td class="ship-content">
                Store ID: {{ $purchaseOrder->otherStore->ID ?? '' }}<br/>
                Contact or Department: {{ $purchaseOrder->StoreID }}<br/>
                Store Name: {{ $purchaseOrder->otherStore->Name ?? '' }}<br/>
                Address: {{ $purchaseOrder->otherStore->Address1 ?? '' }}<br/>
                Phone: {{ $purchaseOrder->otherStore->PhoneNumber ?? '' }}<br/>
                Fax: {{ $purchaseOrder->otherStore->FaxNumber ?? '' }}
            </td>
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
            <td class="bold1">
                Vehicle Type: {{ $condition->vehicle_type ?? 'N/A' }}
            </td>
            <td class="bold1">
                GE Receiver name : {{ $vehicle['permit_number'] ?? '' }}
            </td>
        </tr>
        <tr>
            <td class="bold1">Item Temp: {{ $vehicle['item_temp'] ?? '' }}</td>
            <td class="bold1">
                TRF Division: {{ $vehicle['TRF Division'] ?? '' }}
            </td>
        </tr>
        <tr>
            <td class="bold1">
                Car Temperature: {{ $vehicle['temperature_out'] ?? '' }}
            </td>
            <td class="bold1">
                Driver Name: {{ $vehicle['temperature_in'] ?? '' }}
            </td>
        </tr>
        <tr>
            <td class="bold1">
                Stander Temp: {{ $vehicle['temperature_out'] ?? '' }}
            </td>
            <td class="bold1">Seel # : {{ $vehicle['temperature_in'] ?? '' }}</td>
        </tr>
    </table>
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
    <!-- ITEMS -->
    <table class="items">
        <thead>
        <tr>
            <th>ITEM</th>
            <th>DESCRIPTION</th>
            <th>Qty Received</th>
            <th>Qty Ordered</th>
            <th>Diff</th>
            <th>Qty Issued</th>
            <th>Prod. Date</th>
            <th>Exp. Date</th>
        </tr>
        </thead>
        <tbody>
        @foreach($items as $item)
            <tr>
                <td>{{ $item->lookupcode }}</td>
                <td>{{ $item->description }}</td>
                <td>{{ number_format($item->quantity_received, 1) }}</td>
                <td>{{ number_format($item->quantity_requested, 1) }}</td>
                <td>{{ $item->quantity_received - $item->quantity_requested }}</td>
                <td>{{ $item->quantity_issued ?? '0.00' }}</td>
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

















