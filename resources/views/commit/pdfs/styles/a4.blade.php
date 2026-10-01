<style>
    @page {
        size: 210mm 297mm; /* A4 portrait: 210mm x 297mm */
        margin: 12mm; /* Standard margin for A4 */
    }

    * {
        box-sizing: border-box;
    }

    body {
        font-family: Helvetica, Arial, sans-serif;
        font-size: 10px;
        margin: 0;
        padding: 0;
        color: #000;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }

    .page {
        width: 100%;
        margin: 0 auto;
        /* Center the page instead of using flexbox */
    }

    /* ================= HEADER ================= */
    .header-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 6mm;
    }

    .logo {
        width: 50%;
        height: auto;
        margin-bottom: 4px;
        display: block;
    }

    .header-left {
        width: 30%;
        text-align: left;
        font-size: 9px;
    }

    .header-title {
        width: 40%;
        text-align: center;
        font-size: 16px;
        font-weight: bold;
        color: #1E3A8A;
    }

    .header-right {
        width: 30%;
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
        font-size: 12px;
    }

    .ship-content {
        border: 1px solid #000;
        padding: 4px;
        font-size: 10px;
        height: 8mm;
        vertical-align: top;
    }

    .bold1 {
        font-weight: bold;
        font-size: 10px;
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
        font-size: 10px;
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
        font-size: 10px;
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
        width: 50%;
    }

    .footer-tr td {
        width: 50%;
    }

    .footer-tr td:first-child {
        padding-right: 10px;
    }


    /* ================= FOOTER ================= */
    .footer {
        font-size: 10px;
        font-weight: bold;
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
        width: 50%;
    }
</style>
