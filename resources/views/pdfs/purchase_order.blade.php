<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Purchase Order #{{ $purchaseOrder->PONumber }}</title>
    <style>
        @page {
            margin: 8mm 10mm;
            size: A4;
        }

        body {
            font-family: DejaVu Sans, Arial, Helvetica, sans-serif;
            font-size: 10.5px;
            color: #000;
            margin: 0;
            padding: 15px;
            line-height: 1.35;
            background: #fff;
        }

        .container {
            width: 100%;
            max-width: 760px;
            margin: 0 auto;
        }

        /* Header */
        .header {
        {
            text-align: center;
            margin-bottom: 8px;
            border-bottom: 3px solid #1e40af;
            padding-bottom: 6px;
        }

            .company {
                font-size: 10px;
                color: #444;
                margin-bottom: 4px;
            }

            .title {
                font-size: 28px;
                font-weight: bold;
                color: #1e40af;
                margin: 8px 0 4px;
            }

            .date-box {
                text-align: right;
                margin: 8px 0;
            }

            .date-box span {
                display: inline-block;
                border: 2px solid #000;
                padding: 5px 18px;
                font-weight: bold;
                font-size: 11px;
            }

            /* PO Info Box */

            .po-box {
                border: 3px solid #1e40af;
                padding: 12px 15px;
                background: #f5f8ff;
                margin: 15px 0 20px;
            }

            .po-box h3 {
                margin: 0 0 8px;
                padding-bottom: 5px;
                border-bottom: 2px solid #1e40af;
                color: #1e40af;
                font-size: 13px;
            }

            /* Ship To / From – side by side (DomPDF-safe) */

            .ship-table {
                width: 100%;
                border-collapse: collapse;
                margin: 15px 0;
            }

            .ship-table td {
                width: 50%;
                vertical-align: top;
                padding: 0;
            }

            .ship-header {
                background: #1e40af;
                color: white;
                padding: 10px 15px;
                font-weight: bold;
                font-size: 15px;
            }

            .ship-content {
                background: white;
                border: 3px solid #1e40af;
                padding: 12px 15px;
                min-height: 110px;
                font-size: 10.5px;
            }

            /* Vehicle Section */

            .vehicle {
                margin: 25px 0 15px;
                border-top: 2px solid #000;
                padding-top: 10px;
            }

            .vehicle h3 {
                color: #1e40af;
                font-size: 13.5px;
                margin: 0 0 10px;
            }

            .vgrid {
                display: table;
                width: 100%;
                font-size: 10.5px;
            }

            .vrow {
                display: table-row;
            }

            .vcell {
                display: table-cell;
                width: 50%;
                padding: 3px 0;
            }

            .notes {
                background: #fffbeb;
                border: 2px solid #f59e0b;
                padding: 10px 14px;
                margin: 12px 0;
                font-weight: bold;
                color: #92400e;
                border-radius: 6px;
            }

            /* Items Table */

            table.items {
                width: 100%;
                border-collapse: collapse;
                margin: 18px 0 12px;
                font-size: 10.5px;
            }

            table.items th {
                background: #1e40af;
                color: white;
                padding: 9px 6px;
                text-align: center;
                font-weight: bold;
            }

            table.items td {
                border: 1px solid #555;
                padding: 7px 6px;
                text-align: center;
            }

            table.items td:first-child {
                text-align: left;
                padding-left: 10px;
            }

            table.items tr:nth-child(even) {
                background: #f8f9ff;
            }

            .totals {
                margin: 12px 0;
                font-weight: bold;
                font-size: 11.5px;
            }

            .signatures {
                display: table;
                width: 100%;
                margin-top: 40px;
                font-size: 11px;
            }

            .sig {
                display: table-cell;
                width: 50%;
                padding: 0 30px;
                text-align: center;
            }

            .line {
                border-top: 1px solid #000;
                width: 260px;
                display: inline-block;
                margin-top: 35px;
            }

            /* Force exact colors in PDF */

            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>

<div class="container">

    <!-- Header -->
    <div class="header">
        <div class="company">
            L4 Yamama Center, 3 Taha Hussein st. Zamalek - Cairo<br>
            GOURMETEGYPT.COM | 19339<br>
            Landline: +202 27370617 / 19 / 21 | Ext
        </div>
    </div>

    <div class="title">Purchase Order #{{ $purchaseOrder->PONumber }}</div>

    <div class="date-box">
        DATE: <span>{{ \Carbon\Carbon::parse($purchaseOrder->DateCreated)->format('Y-m-d H:i:s') }}</span>
    </div>

    <!-- PO Info -->
    <div class="po-box">
        <h3>Purchase Order Information</h3>
        PO Title: <strong>{{ $purchaseOrder->POTitle ?? '' }}</strong><br>
        PO Type: <strong>{{ $purchaseOrder->POType ?? '' }}</strong><br>
        Created At: <strong>{{ $purchaseOrder->DateCreated }}</strong>
    </div>

    <!-- SHIP TO / SHIP FROM -->
    <table class="ship-table">
        <tr>
            <td>
                <div class="ship-header">SHIP TO</div>
                <div class="ship-content">
                    Store ID: <strong>{{ $purchaseOrder->StoreID }}</strong><br>
                    Store Name: <strong>{{ $purchaseOrder->currentStore->Name ?? 'Uptown Cairo' }}</strong><br>
                    Contact (Recipient):<br>
                    Address: {{ $purchaseOrder->currentStore->Address1 ?? 'Uptown Cairo, Mokattam' }}<br>
                    Phone: {{ $purchaseOrder->currentStore->PhoneNumber ?? '19339' }}<br>
                    Fax: {{ $purchaseOrder->currentStore->FaxNumber ?? '192.168.34.6' }}
                </div>
            </td>
            <td>
                <div class="ship-header">SHIP FROM</div>
                <div class="ship-content">
                    Store ID: <strong>{{ $purchaseOrder->otherStore->ID ?? '1' }}</strong><br>
                    Store Name: <strong>{{ $purchaseOrder->otherStore->Name ?? 'Mohandesen outlet' }}</strong><br>
                    Contact (Dispatcher):<br>
                    Address: {{ $purchaseOrder->otherStore->Address1 ?? '6 Massane Hadidya Str.' }}<br>
                    Phone: {{ $purchaseOrder->otherStore->PhoneNumber ?? '02-33050882' }}<br>
                    Fax: {{ $purchaseOrder->otherStore->FaxNumber ?? '192.168.34.6' }}
                </div>
            </td>
        </tr>
    </table>

    <!-- Vehicle & Condition -->
    <div class="vehicle">
        <h3>Vehicle & Condition Information</h3>
        <div class="vgrid">
            <div class="vrow">
                <div class="vcell">Vehicle Type: <strong>{{ $condition->vehicle_type ?? 'N/A' }}</strong></div>
                <div class="vcell">Delivery Permit Number:
                    <strong>{{ $condition->delivery_permit_number ?? 'N/A' }}</strong></div>
            </div>
            <div class="vrow">
                <div class="vcell">Vehicle Temperature Out: <strong>{{ $condition->vehicle_tempOut ?? 'N/A' }}
                        °C</strong></div>
                <div class="vcell">Status: <strong>{{ $condition->status ?? 'N/A' }}</strong></div>
            </div>
            <div class="vrow">
                <div class="vcell">Vehicle Temperature In: <strong>{{ $condition->vehicle_tempIN ?? 'N/A' }} °C</strong>
                </div>
                <div class="vcell">Created At: <strong>{{ $condition->created_at ?? 'N/A' }}</strong></div>
            </div>
        </div>

        @if($condition->notes)
            <div class="notes">
                Additional Notes<br>
                {{ $condition->notes }}
            </div>
        @endif
    </div>

    <!-- Items Table -->
    <table class="items">
        <thead>
        <tr>
            <th>ITEM CODE</th>
            <th>DESCRIPTION</th>
            <th>QTY<br>Requested</th>
            <th>QTY<br>Received</th>
            <th>Diff</th>
            <th>QTY<br>Issued</th>
            <th>Production<br>Date</th>
            <th>Expiration<br>Date</th>
            <th>S/N</th>
        </tr>
        </thead>
        <tbody>
        @forelse($items as $item)
            <tr>
                <td>{{ $item->lookupcode }}</td>
                <td>{{ $item->description }}</td>
                <td>{{ number_format($item->quantity_requested, 1) }}</td>
                <td>{{ number_format($item->quantity_received, 1) }}</td>
                <td>{{ $item->quantity_received - $item->quantity_requested }}</td>
                <td>{{ $item->quantity_issued ?? '0.00' }}</td>
                <td>{{ $item->production_date ? \Carbon\Carbon::parse($item->production_date)->format('d/m/Y') : '' }}</td>
                <td>{{ $item->expire_date ? \Carbon\Carbon::parse($item->expire_date)->format('d/m/Y') : '' }}</td>
                <td>{{ $item->sn ?? '' }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="9" style="height:70px;">&nbsp;</td>
            </tr>
        @endforelse
        </tbody>
    </table>

    <div class="totals">
        Total Units Requested: <strong>{{ number_format($items->sum('quantity_requested'), 1) }}</strong><br>
        Total Units Received: <strong>{{ number_format($items->sum('quantity_received'), 1) }}</strong>
    </div>

    <div class="signatures">
        <div class="sig">
            Receiver/WMS Operator Signature:<br><br>
            <span class="line"></span>
        </div>
        <div class="sig">
            Store/WMS Manager Signature:<br><br>
            <span class="line"></span>
        </div>
    </div>

</div>
</body>
</html>
