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
    @yield('details')
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
    @section('footer')
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
                <td class="circle-cell">
                    <div class="signature-circle"></div>
                </td>
            </tr>
        </table>
    </div>
    @show
</div>
</body>

</html>
