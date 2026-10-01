<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase Order Notification</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .email-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #ffffff;
            padding: 30px 20px;
            text-align: center;
        }

        .email-header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: bold;
        }

        .email-header p {
            margin: 10px 0 0 0;
            font-size: 14px;
            opacity: 0.9;
        }

        .email-body {
            padding: 30px 20px;
        }

        .info-section {
            background-color: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }

        .info-row {
            margin: 8px 0;
        }

        .info-label {
            font-weight: bold;
            color: #555;
            display: inline-block;
            min-width: 120px;
        }

        .info-value {
            color: #333;
        }

        .email-footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #666;
            border-top: 1px solid #ddd;
        }
    </style>
</head>
<body>
<div class="email-container">
    <div class="email-header">
        <h1>@yield('headline')</h1>
        <p>Order #{{ $purchaseOrder->PONumber }}</p>
    </div>

    <div class="email-body">
        <p>Hello,</p>

        @yield('intro')

        <div class="info-section">
            <div class="info-row">
                <span class="info-label">Order Number:</span>
                <span class="info-value">#{{ $purchaseOrder->PONumber }}</span>
            </div>

            @if($purchaseOrder->POTitle)
                <div class="info-row">
                    <span class="info-label">Title:</span>
                    <span class="info-value">{{ $purchaseOrder->POTitle }}</span>
                </div>
            @endif

            @section('parties')
                <div class="info-row">
                    <span class="info-label">From Store:</span>
                    <span class="info-value">{{ $fromStore }}</span>
                </div>

                <div class="info-row">
                    <span class="info-label">To Store:</span>
                    <span class="info-value">{{ $toStore }}</span>
                </div>
            @show

            <div class="info-row">
                <span class="info-label">Created At:</span>
                <span class="info-value">{{ $purchaseOrder->DateCreated }}</span>
            </div>

            @if($purchaseOrder->condition)
                @if($purchaseOrder->condition->Driver_name)
                    <div class="info-row">
                        <span class="info-label">Driver Name:</span>
                        <span class="info-value">{{ $purchaseOrder->condition->Driver_name }}</span>
                    </div>
                @endif

                @if($purchaseOrder->condition->Vehicle_number)
                    <div class="info-row">
                        <span class="info-label">Vehicle Number:</span>
                        <span class="info-value">{{ $purchaseOrder->condition->Vehicle_number }}</span>
                    </div>
                @endif
            @endif
        </div>

        <p style="color: #666; font-size: 14px;">
            If you have any questions or concerns, please contact the relevant department.
        </p>
    </div>

    <div class="email-footer">
        <p style="margin: 5px 0;">This is an automated notification from the Purchase Order System.</p>
        <p style="margin: 5px 0;">&copy; {{ date('Y') }} Your Company Name. All rights reserved.</p>
    </div>
</div>
</body>
</html>
