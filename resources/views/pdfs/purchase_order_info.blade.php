<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Purchase Order Info #{{ $purchaseOrder->PONumber }}</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .header { text-align: center; margin-bottom: 30px; }
        .item-section { margin: 30px 0; page-break-inside: avoid; }
        table { width: 100%; border-collapse: collapse; margin: 10px 0; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
<div class="header">
    <h1>Purchase Order Items Information</h1>
    <h2>#{{ $purchaseOrder->PONumber }}</h2>
</div>

@foreach($items as $item)
    <div class="item-section">
        <h3>{{ $item->Description }} ({{ $item->LookupCode }})</h3>

        @if($item->infos && count($item->infos) > 0)
            <table>
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Quantity Issued</th>
                    <th>Production Date</th>
                    <th>Expire Date</th>
                    <th>Created At</th>
                </tr>
                </thead>
                <tbody>
                @foreach($item->infos as $info)
                    <tr>
                        <td>{{ $info->id }}</td>
                        <td>{{ $info->quantity_issued }}</td>
                        <td>{{ $info->production_date }}</td>
                        <td>{{ $info->expire_date }}</td>
                        <td>{{ $info->created_at }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @else
            <p>No information available for this item.</p>
        @endif
    </div>
@endforeach
</body>
</html>
