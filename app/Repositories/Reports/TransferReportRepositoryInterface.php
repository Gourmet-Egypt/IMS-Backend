<?php

namespace App\Repositories\Reports;

use Illuminate\Support\Collection;

interface TransferReportRepositoryInterface
{
    public function netTransfer(ReportFilters $filters): Collection;

    public function netTransferSummary(ReportFilters $filters): Collection;

    public function notReceived(ReportFilters $filters): Collection;

    public function checklist(ReportFilters $filters, string $direction): Collection;

    public function productionDates(ReportFilters $filters, string $direction): Collection;

    public function processTime(ReportFilters $filters, string $direction): Collection;
}
