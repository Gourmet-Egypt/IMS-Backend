<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Purchase Order Condition #{{ $purchaseOrder->PONumber }}</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .header { text-align: center; margin-bottom: 30px; }
        .info-box { margin: 20px 0; padding: 15px; border: 1px solid #ddd; }
        .label { font-weight: bold; display: inline-block; width: 200px; }
    </style>
</head>
<body>
<div class="header">
    <h1>Purchase Order Condition</h1>
    <h2>#{{ $purchaseOrder->PONumber }}</h2>
</div>

@if($condition)
    <div class="info-box">
        <p><span class="label">Vehicle Type:</span> {{ $condition->vehicle_type ?? 'N/A' }}</p>
        <p><span class="label">Purchase Order ID:</span> {{ $condition->purchase_order_id }}</p>
        <p><span class="label">Vehicle Temp Out:</span> {{ $condition->vehicle_tempOut }}°C</p>
        <p><span class="label">Vehicle Temp IN:</span> {{ $condition->vehicle_tempIN }}°C</p>
        <p><span class="label">Delivery Permit Number:</span> {{ $condition->delivery_permit_number }}</p>
        <p><span class="label">Status:</span> {{ $condition->status }}</p>
        <p><span class="label">Created At:</span> {{ $condition->created_at }}</p>
    </div>

    @if($condition->notes)
        <div class="info-box">
            <h3>Notes</h3>
            <p>{{ $condition->notes }}</p>
        </div>
    @endif
@else
    <p>No condition information available.</p>
@endif
</body>
</html>

