<?php

namespace App\Http\Controllers\Dashboard;

use App\Exports\ReportExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\ReportRequest;
use App\Http\Resources\Dashboard\Reports\Supply\MissingOnOrderResource;
use App\Http\Resources\Dashboard\Reports\Supply\SupplierOrderResource;
use App\Http\Resources\Dashboard\Reports\Supply\SupplierReturnResource;
use App\Http\Resources\Dashboard\Reports\Supply\SupplyChecklistResource;
use App\Http\Resources\Dashboard\Reports\Supply\SupplyProductionDateResource;
use App\Repositories\Reports\SupplyReportRepositoryInterface;

class SupplyReportController extends Controller
{
    public function __construct(private readonly SupplyReportRepositoryInterface $reports)
    {
    }

    public function received(ReportRequest $request)
    {
        return ReportExport::download('Purchase Received', $this->reports->received($request->filters())
            ->flatMap(fn ($order) => (new SupplierOrderResource($order))->resolve($request)));
    }

    public function returned(ReportRequest $request)
    {
        return ReportExport::download('Purchase Returned', $this->reports->returned($request->filters())
            ->flatMap(fn ($order) => (new SupplierReturnResource($order))->resolve($request)));
    }

    public function productionDates(ReportRequest $request)
    {
        return ReportExport::download('Production Date of POs', $this->reports->productionDates($request->filters())
            ->flatMap(fn ($info) => (new SupplyProductionDateResource($info))->resolve($request)));
    }

    public function missingOnOrder(ReportRequest $request)
    {
        return ReportExport::download('Item Missing on Order', $this->reports->missingOnOrder($request->filters())
            ->flatMap(fn ($order) => (new MissingOnOrderResource($order))->resolve($request)));
    }

    public function checklist(ReportRequest $request)
    {
        return ReportExport::download('POs Checklist', $this->reports->checklist($request->filters())
            ->flatMap(fn ($order) => (new SupplyChecklistResource($order))->resolve($request)));
    }

    public function netPurchase(ReportRequest $request)
    {
        return ReportExport::download('Net Purchase', $this->reports->netPurchase($request->filters()));
    }

    public function counts(ReportRequest $request)
    {
        $filters = $request->filters();

        return ReportExport::download('Count of Supply', [$this->reports->counts($filters) + $filters->toArray()]);
    }
}
