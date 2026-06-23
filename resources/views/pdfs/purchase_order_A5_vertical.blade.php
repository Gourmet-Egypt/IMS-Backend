<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>@if(isset($perspective) && $perspective === 'from_store')
            Transfer OUT
        @elseif(isset($perspective) && $perspective === 'to_store')
            Transfer IN
        @else
            Transfer
        @endif</title>

    <style>
        @page {
            size: 210mm 297mm; /* A4 size - content in left half only */
            margin: 8mm 115mm 8mm 8mm; /* top, right (leaves 105mm for content), bottom, left */
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: Helvetica, Arial, sans-serif;
            font-size: 7px;
            margin: 0;
            padding: 0;
            color: #000;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .page {
            width: 100%;
            margin: 0 auto;
        }

        /* ================= HEADER ================= */
        .header-table {
            width: 100%;
            height: auto;
            border-collapse: collapse;
            margin-bottom: 2mm;
        }

        .logo {
            width: 60%;
            height: auto;
            margin-bottom: 2px;
            display: block;
        }

        .header-left {
            width: 50%;
            text-align: left;
            font-size: 5px;
        }

        .header-right {
            width: 50%;
            text-align: right;
            font-size: 6px;
        }

        .header-title {
            text-align: center;
            font-size: 12px;
            font-weight: bold;
            color: #1E3A8A;
            margin: 2mm 0;
        }

        .box {
            border: 1px solid #000;
            padding: 1px 3px;
            display: inline-block;
            min-width: 18mm;
            text-align: center;
            font-size: 6px;
        }

        /* ================= SHIP ================= */
        .ship-table {
            width: 100%;
            border-collapse: collapse;
            margin: 2mm 0;
        }

        .ship-title {
            background: #1E3A8A;
            color: #fff;
            font-weight: bold;
            padding: 2px;
            text-align: left;
            font-size: 7px;
        }

        .ship-content {
            border: 1px solid #000;
            padding: 2px;
            font-size: 6px;
            vertical-align: top;
        }

        .bold1 {
            font-weight: bold;
            font-size: 6px;
        }

        /* ================= VEHICLE ================= */
        .section-title {
            font-weight: bold;
            margin-top: 2mm;
            padding-top: 1mm;
            font-size: 9px;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 6px;
            margin-top: 1mm;
        }

        .info-table td {
            padding: 1px 0;
        }

        /* ================= ITEMS ================= */
        table.items {
            width: 100%;
            border-collapse: collapse;
            margin-top: 2mm;
            font-size: 6px;
        }

        table.items th {
            background: #1E3A8A;
            color: #fff;
            padding: 2px;
            border: 1px solid #000;
            font-size: 5px;
        }

        table.items td {
            border: 1px solid #000;
            padding: 1px 2px;
            text-align: center;
            font-size: 5px;
        }

        table.items td:first-child {
            text-align: left;
        }

        /* ================= FOOTER ================= */
        .footer {
            font-size: 6px;
            font-weight: bold;
            margin-bottom: 3px;
            margin-top: 8px;
        }

        .footer-table {
            width: 100%;
            border-collapse: collapse;
        }

        .footer-tr td {
            width: 50%;
            font-size: 6px;
        }

        .signature-line {
            border-top: 1px solid #000;
            margin-top: 3mm;
            width: 80%;
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
                Zamalek - Cairo<br>
                GOURMETEGYPT.COM<br>
                19339
            </td>
            <td class="header-right">
                DATE<br>
                <span class="box">{{ \Carbon\Carbon::parse($purchaseOrder->DateCreated)->format('Y-m-d') }}</span><br>
                PO #<br>
                <span class="box">{{ $purchaseOrder->PONumber }}</span>
            </td>
        </tr>
    </table>

    <div class="header-title">
        @if(isset($perspective) && $perspective === 'from_store')
            Transfer OUT
        @elseif(isset($perspective) && $perspective === 'to_store')
            Transfer IN
        @else
            Transfer
        @endif
    </div>

    <!-- SHIP FROM -->
    <table class="ship-table">
        <tr>
            <td class="ship-title">SHIP FROM</td>
        </tr>
        <tr>
            @if($purchaseOrder->POType == 2)
                <td class="ship-content">
                    <span class="bold1">Store: </span>{{ $purchaseOrder->otherStore->Name ?? '' }}<br/>
                    <span class="bold1">Address: </span>{{ $purchaseOrder->otherStore->Address1 ?? '' }}<br/>
                    <span class="bold1">Phone: </span>{{ $purchaseOrder->otherStore->PhoneNumber ?? '' }}
                </td>
            @else
                <td class="ship-content">
                    <span class="bold1">Store: </span>{{ $purchaseOrder->currentStore->Name ?? '' }}<br/>
                    <span class="bold1">Address: </span>{{ $purchaseOrder->currentStore->Address1 ?? '' }}<br/>
                    <span class="bold1">Phone: </span>{{ $purchaseOrder->currentStore->PhoneNumber ?? '' }}
                </td>
            @endif
        </tr>
    </table>

    <!-- SHIP TO -->
    <table class="ship-table">
        <tr>
            <td class="ship-title">SHIP TO</td>
        </tr>
        <tr>
            @if($purchaseOrder->POType == 2)
                <td class="ship-content">
                    <span class="bold1">Store: </span>{{ $purchaseOrder->currentStore->Name ?? '' }}<br/>
                    <span class="bold1">Address: </span>{{ $purchaseOrder->currentStore->Address1 ?? '' }}<br/>
                    <span class="bold1">Phone: </span>{{ $purchaseOrder->currentStore->PhoneNumber ?? '' }}
                </td>
            @else
                <td class="ship-content">
                    <span class="bold1">Store: </span>{{ $purchaseOrder->otherStore->Name ?? '' }}<br/>
                    <span class="bold1">Address: </span>{{ $purchaseOrder->otherStore->Address1 ?? '' }}<br/>
                    <span class="bold1">Phone: </span>{{ $purchaseOrder->otherStore->PhoneNumber ?? '' }}
                </td>
            @endif
        </tr>
    </table>

    <!-- VEHICLE -->
    <div class="section-title">Vehicle Info</div>
    <table class="info-table">
        <tr>
            <td><span class="bold1">Driver: </span>{{ $condition->Driver_name ?? '' }}</td>
        </tr>
        <tr>
            <td><span class="bold1">Vehicle #: </span>{{ $condition->Vehicle_number ?? '' }}</td>
        </tr>
        <tr>
            <td><span class="bold1">Seal #: </span>{{ $condition->seal_number ?? '' }}</td>
        </tr>
        <tr>
            <td><span class="bold1">Temp In: </span>{{ $condition->vehicle_tempIN ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td><span class="bold1">Temp Out: </span>{{ $condition->vehicle_tempOut ?? 'N/A' }}</td>
        </tr>
    </table>

    <!-- ITEMS -->
    <table class="items">
        <thead>
        <tr>
            <th>ITEM</th>
            <th>DESC</th>
            @if(isset($perspective) && $perspective === 'from_store')
                <th>Ord</th>
                <th>Iss</th>
            @else
                <th>Ord</th>
                <th>Iss</th>
                <th>Rcv</th>
            @endif
            <th>Diff</th>
        </tr>
        </thead>
        <tbody>
        <x-pdf.item-rows :items="$items" :perspective="$perspective ?? null" />
        </tbody>
    </table>

    <!-- FOOTER -->
    <div class="footer">
        <table class="footer-table">
            <tr class="footer-tr">
                <td>
                    Receiver Signature
                    <div class="signature-line"></div>
                </td>
                <td>
                    Manager Signature
                    <div class="signature-line"></div>
                </td>
            </tr>
        </table>
    </div>
</div>
</body>

</html>
