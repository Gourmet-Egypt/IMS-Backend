<style>
    @page {
        size: 210mm 297mm; /* A4 size - content in top half only */
        margin: 10mm 10mm 159mm 10mm; /* top, right, bottom (leaves 148mm for content), left */
    }

    * {
        box-sizing: border-box;
    }

    body {
        font-family: Helvetica, Arial, sans-serif;
        font-size: 9px;
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
        margin-bottom: 3mm;
    }

    .logo {
        width: 45%;
        height: auto;
        margin-bottom: 2px;
        display: block;
    }

    .header-left {
        width: 30%;
        text-align: left;
        font-size: 8px;
    }

    .header-title {
        width: 40%;
        text-align: center;
        font-size: 14px;
        font-weight: bold;
        color: #1E3A8A;
    }

    .header-right {
        width: 30%;
        text-align: right;
        font-size: 8px;
    }

    .box {
        border: 1px solid #000;
        padding: 2px 5px;
        display: inline-block;
        min-width: 28mm;
        text-align: center;
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
        padding: 2px 4px;
        text-align: left;
        font-size: 10px;
    }

    .ship-content {
        border: 1px solid #000;
        padding: 2px 4px;
        font-size: 8px;
        vertical-align: top;
    }

    .bold1 {
        font-weight: bold;
        font-size: 8px;
    }

    /* ================= VEHICLE ================= */
    .section-title {
        font-weight: bold;
        margin-top: 2mm;
        padding-top: 1mm;
        font-size: 12px;
    }

    .info-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 8px;
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
        font-size: 8px;
    }

    table.items th {
        background: #1E3A8A;
        color: #fff;
        padding: 2px 3px;
        border: 1px solid #000;
        font-size: 8px;
    }

    table.items td {
        border: 1px solid #000;
        padding: 2px 3px;
        text-align: center;
    }

    table.items td:first-child {
        text-align: left;
    }

    .footer-tr {
        width: 50%;
    }

    .footer-tr td {
        width: 50%;
    }

    .footer-tr td:first-child {
        padding-right: 8px;
    }

    /* ================= FOOTER ================= */
    .footer {
        font-size: 9px;
        font-weight: bold;
        margin-bottom: 5px;
        margin-top: 15px;
    }

    .footer-table {
        width: 100%;
        border-collapse: collapse;
    }

    .signature-line {
        border-top: 1px solid #000;
        margin-top: 3mm;
        width: 50%;
    }
</style>
