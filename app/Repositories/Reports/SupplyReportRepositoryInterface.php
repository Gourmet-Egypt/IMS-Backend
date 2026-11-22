<?php

namespace App\Repositories\Reports;

use Illuminate\Support\Collection;

interface SupplyReportRepositoryInterface
{
    public function received(ReportFilters $filters): Collection;

    public function returned(ReportFilters $filters): Collection;

    public function productionDates(ReportFilters $filters): Collection;

    public function missingOnOrder(ReportFilters $filters): Collection;

    public function checklist(ReportFilters $filters): Collection;

    public function netPurchase(ReportFilters $filters): Collection;

    public function counts(ReportFilters $filters): array;
}
