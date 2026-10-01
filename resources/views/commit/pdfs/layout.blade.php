<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8"/>
    <title>@yield('docTitle')</title>
    @include($styleView)
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
            <td class="header-title">@yield('headerTitle')</td>
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
    <!-- SHIP (physical goods flow — driven by POType, not perspective) -->
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
                <span class="bold1">GE Receiver name: </span>
                {{ $condition->receiver_name ?? '' }}
            </td>
        </tr>
        <tr>
            <td>
                <span class="bold1">Item Temp: </span>
                {{ $condition->item_temp ?? '' }}
            </td>
            <td>
                <span class="bold1">Car Temperature (In): </span>
                {{ $condition->vehicle_tempIN ?? 'N/A' }}
            </td>
        </tr>
        <tr>
            <td>
                <span class="bold1">Car Temperature (Out): </span>
                {{ $condition->vehicle_tempOut ?? 'N/A' }}
            </td>
            <td>
                <span class="bold1">Seal # : </span>
                {{ $condition->seal_number ?? '' }}
            </td>
        </tr>
        <tr>
            <td>
                <span class="bold1">Delivery Permit Number: </span>
                {{ $condition->delivery_permit_number ?? '' }}
            </td>
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
            @yield('qtyColumns')
            <th>Prod. Date</th>
            <th>Exp. Date</th>
        </tr>
        </thead>
        <tbody>
        @yield('itemRows')
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
