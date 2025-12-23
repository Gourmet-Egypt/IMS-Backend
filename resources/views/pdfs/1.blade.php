<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Purchase Order #{{ $purchaseOrder->PONumber }}</title>

    <style>
        @page {
            size: A5 portrait;
            margin: 3mm 6mm 6mm 6mm;
        }

        body {
            font-family: DejaVu Sans, Arial, Helvetica, sans-serif;
            font-size: 9.8px;
            color: #111827;
            margin: 0;
            padding: 0;
            line-height: 1.3;
            background: #fff;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .container {
            width: 100%;
        }

        /* ===== HEADER ===== */
        .header {
            text-align: center;
            border-bottom: 2px solid #1e40af;
            padding-bottom: 6px;
            margin-bottom: 6px;
        }

        .company {
            font-size: 8.6px;
            color: #374151;
            line-height: 1.25;
        }

        .title {
            text-align: center;
            font-size: 18px;
            font-weight: 700;
            color: #1e40af;
            margin: 6px 0 4px;
            letter-spacing: 0.5px;
        }

        .date-box {
            text-align: right;
            margin: 4px 0 6px;
        }

        .date-box span {
            border: 1.5px solid #111;
            padding: 3px 10px;
            font-size: 9px;
            font-weight: 600;
        }

        /* ===== INFO BOX ===== */
        .po-box {
            border: 2px solid #1e40af;
            background: #f8faff;
            padding: 8px 10px;
            margin: 8px 0;
            font-size: 9.5px;
        }

        .po-box h3 {
            margin: 0 0 4px;
            font-size: 11px;
            font-weight: 700;
            color: #1e40af;
            border-bottom: 1px solid #1e40af;
            padding-bottom: 3px;
        }

        /* ===== SHIP TABLE ===== */
        .ship-table {
            width: 100%;
            border-collapse: collapse;
            margin: 8px 0;
        }

        .ship-table td {
            width: 50%;
            vertical-align: top;
        }

        .ship-header {
            background: #1e40af;
            color: #fff;
            font-size: 10px;
            font-weight: 700;
            padding: 6px 8px;
        }

        .ship-content {
            border: 2px solid #1e40af;
            padding: 6px 8px;
            font-size: 9px;
            line-height: 1.3;
            min-height: 80px;
        }

        /* ===== VEHICLE ===== */
        .vehicle {
            margin: 10px 0;
            padding-top: 6px;
            border-top: 1px dashed #6b7280;
        }

        .vehicle h3 {
            font-size: 11px;
            margin-bottom: 6px;
            color: #1e40af;
        }

        .vgrid {
            display: table;
            width: 100%;
            font-size: 9.5px;
        }

        .vrow {
            display: table-row;
        }

        .vcell {
            display: table-cell;
            padding: 2px 0;
        }

        .notes {
            margin-top: 6px;
            padding: 6px 8px;
            background: #fffbeb;
            border: 1.5px solid #f59e0b;
            font-size: 9px;
            color: #92400e;
            font-weight: 600;
        }

        /* ===== ITEMS TABLE ===== */
        table.items {
            width: 100%;
            border-collapse: collapse;
            margin: 8px 0;
            font-size: 9px;
        }

        table.items th {
            background: #1e40af;
            color: #fff;
            font-size: 8.5px;
            padding: 5px 4px;
            border: 1px solid #1e40af;
        }

        table.items td {
            border: 1px solid #6b7280;
            padding: 4px;
            text-align: center;
        }

        table.items td:first-child {
            text-align: left;
            padding-left: 6px;
        }

        table.items tr:nth-child(even) {
            background: #f9fafb;
        }

        /* ===== TOTALS ===== */
        .totals {
            margin-top: 6px;
            font-size: 10px;
            font-weight: 700;
            text-align: right;
        }

        /* ===== SIGNATURES ===== */
        .signatures {
            display: table;
            width: 100%;
            margin-top: 20px;
            font-size: 9.5px;
        }

        .sig {
            display: table-cell;
            width: 50%;
            text-align: center;
            padding: 0 10px;
        }

        .line {
            display: inline-block;
            width: 160px;
            border-top: 1px solid #111;
            margin-top: 28px;
        }
    </style>
</head>
<body>

<div class="container">

    <!-- HEADER -->
    <div class="header">
        <div class="company">
            L4 Yamama Center, 3 Taha Hussein St., Zamalek – Cairo<br>
            GOURMETEGYPT.COM | 19339<br>
            Landline: +202 27370617 / 19 / 21
        </div>
    </div>

    <div class="title">Purchase Order #{{ $purchaseOrder->PONumber }}</div>

    <div class="date-box">
        DATE: <span>{{ \Carbon\Carbon::parse($purchaseOrder->DateCreated)->format('Y-m-d H:i:s') }}</span>
    </div>

    <!-- PO INFO -->
    <div class="po-box">
        <h3>Purchase Order Information</h3>
        PO Title: <strong>{{ $purchaseOrder->POTitle ?? '' }}</strong><br>
        PO Type: <strong>{{ $purchaseOrder->POType ?? '' }}</strong><br>
        Created At: <strong>{{ $purchaseOrder->DateCreated }}</strong>
    </div>

    <!-- SHIP -->
    <table class="ship-table">
        <tr>
            <td>
                <div class="ship-header">SHIP TO</div>
                <div class="ship-content">
                    Store ID: <strong>{{ $purchaseOrder->StoreID }}</strong><br>
                    Store Name: <strong>{{ $purchaseOrder->currentStore->Name ?? '' }}</strong><br>
                    Address: {{ $purchaseOrder->currentStore->Address1 ?? '' }}<br>
                    Phone: {{ $purchaseOrder->currentStore->PhoneNumber ?? '' }}
                </div>
            </td>
            <td>
                <div class="ship-header">SHIP FROM</div>
                <div class="ship-content">
                    Store ID: <strong>{{ $purchaseOrder->otherStore->ID ?? '' }}</strong><br>
                    Store Name: <strong>{{ $purchaseOrder->otherStore->Name ?? '' }}</strong><br>
                    Address: {{ $purchaseOrder->otherStore->Address1 ?? '' }}<br>
                    Phone: {{ $purchaseOrder->otherStore->PhoneNumber ?? '' }}
                </div>
            </td>
        </tr>
    </table>

    <!-- VEHICLE -->
    <div class="vehicle">
        <h3>Vehicle & Condition</h3>

        <div class="vgrid">
            <div class="vrow">
                <div class="vcell">Vehicle Type: <strong>{{ $condition->vehicle_type ?? 'N/A' }}</strong></div>
                <div class="vcell">Permit #: <strong>{{ $condition->delivery_permit_number ?? 'N/A' }}</strong></div>
            </div>
            <div class="vrow">
                <div class="vcell">Temp Out: <strong>{{ $condition->vehicle_tempOut ?? 'N/A' }}°C</strong></div>
                <div class="vcell">Temp In: <strong>{{ $condition->vehicle_tempIN ?? 'N/A' }}°C</strong></div>
            </div>
        </div>

        @if($condition->notes)
            <div class="notes">
                Notes: {{ $condition->notes }}
            </div>
        @endif
    </div>

    <!-- ITEMS -->
    <table class="items">
        <thead>
        <tr>
            <th>Code</th>
            <th>Description</th>
            <th>Req</th>
            <th>Rec</th>
            <th>Diff</th>
            <th>Issued</th>
            <th>Prod</th>
            <th>Exp</th>
            <th>S/N</th>
        </tr>
        </thead>
        <tbody>
        @forelse($items as $item)
            <tr>
                <td>{{ $item->lookupcode }}</td>
                <td>{{ $item->description }}</td>
                <td>{{ number_format($item->quantity_requested,1) }}</td>
                <td>{{ number_format($item->quantity_received,1) }}</td>
                <td>{{ $item->quantity_received - $item->quantity_requested }}</td>
                <td>{{ $item->quantity_issued ?? '0.00' }}</td>
                <td>{{ optional($item->production_date)->format('d/m/Y') }}</td>
                <td>{{ optional($item->expire_date)->format('d/m/Y') }}</td>
                <td>{{ $item->sn ?? '' }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="9" style="height:40px;"></td>
            </tr>
        @endforelse
        </tbody>
    </table>

    <!-- TOTALS -->
    <div class="totals">
        Requested: {{ number_format($items->sum('quantity_requested'),1) }} |
        Received: {{ number_format($items->sum('quantity_received'),1) }}
    </div>

    <!-- SIGNATURES -->
    <div class="signatures">
        <div class="sig">
            Receiver Signature<br>
            <span class="line"></span>
        </div>
        <div class="sig">
            Manager Signature<br>
            <span class="line"></span>
        </div>
    </div>

</div>
</body>
</html>
