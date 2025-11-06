<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Purchase Order #{{ $purchaseOrder->PONumber }}</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .header { text-align: center; margin-bottom: 30px; }
        .info-box { margin: 20px 0; }
        .label { font-weight: bold; }
    </style>
</head>
<body>
<div class="header">
    <h1>Purchase Order</h1>
    <h2>#{{ $purchaseOrder->PONumber }}</h2>
</div>

<div class="info-box">
    <p><span class="label">Title:</span> {{ $purchaseOrder->POTitle }}</p>
    <p><span class="label">Type:</span> {{ $purchaseOrder->POType }}</p>
    <p><span class="label">From Store:</span> {{ $purchaseOrder->currentStore->Name ?? 'N/A' }}</p>
    <p><span class="label">To Store:</span> {{ $purchaseOrder->otherStore->Name ?? 'N/A' }}</p>
    <p><span class="label">Created At:</span> {{ $purchaseOrder->DateCreated }}</p>
</div>
</body>
</html>
