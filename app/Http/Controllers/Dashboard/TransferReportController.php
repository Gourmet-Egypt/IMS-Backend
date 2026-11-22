<?php

namespace App\Http\Controllers\Dashboard;

use App\Exports\ReportExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\ReportRequest;
use App\Http\Resources\Dashboard\Reports\Transfers\ChecklistResource;
use App\Http\Resources\Dashboard\Reports\Transfers\NetTransferResource;
use App\Http\Resources\Dashboard\Reports\Transfers\NotReceivedResource;
use App\Http\Resources\Dashboard\Reports\Transfers\ProductionDateResource;
use App\Repositories\Reports\TransferReportRepositoryInterface;

class TransferReportController extends Controller
{
    public function __construct(private readonly TransferReportRepositoryInterface $reports)
    {
    }

    public function netTransfer(ReportRequest $request)
    {
        return ReportExport::download('Net Transfer', $this->reports->netTransfer($request->filters())
            ->flatMap(fn ($order) => (new NetTransferResource($order))->resolve($request)));
    }

    public function netTransferSummary(ReportRequest $request)
    {
        return ReportExport::download('Net Transfer Summary', $this->reports->netTransferSummary($request->filters()));
    }

    public function notReceived(ReportRequest $request)
    {
        return ReportExport::download('Transfer Issue Not Received', $this->reports->notReceived($request->filters())
            ->flatMap(fn ($order) => (new NotReceivedResource($order))->resolve($request)));
    }

    public function checklist(ReportRequest $request, string $direction)
    {
        return ReportExport::download('Transfer ' . ucfirst($direction) . ' Checklist', $this->reports->checklist($request->filters(), $direction)
            ->flatMap(fn ($order) => (new ChecklistResource($order, $direction))->resolve($request)));
    }

    public function productionDates(ReportRequest $request, string $direction)
    {
        return ReportExport::download('Production Date of Transfer ' . ucfirst($direction), $this->reports->productionDates($request->filters(), $direction)
            ->flatMap(fn ($info) => (new ProductionDateResource($info, $direction))->resolve($request)));
    }

    public function processTime(ReportRequest $request, string $direction)
    {
        return ReportExport::download(
            $direction === 'out' ? 'Transfer Issue Time' : 'Transfer Received Time',
            $this->reports->processTime($request->filters(), $direction),
        );
    }
}
