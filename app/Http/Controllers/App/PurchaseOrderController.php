<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Http\Requests\App\PurchaseOrder\CommitOrderRequest;
use App\Http\Requests\App\PurchaseOrderEntry\UpdatePurchaseOrderEntryInfosRequest;
use App\Http\Resources\App\Offline\PurchaseOrderEntryResource;
use App\Http\Resources\App\Offline\PurchaseOrderResource;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderEmail;
use App\Models\PurchaseOrderEntry;
use App\Models\PurchaseOrderProcessStart;
use App\Notifications\PurchaseOrderNotification;
use App\Services\CommitOrderService;
use App\Services\PurchaseOrderPdfService;
use App\Services\PurchaseOrderPrintService;
use App\Services\UpdateInfosService;
use App\Traits\Responses;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Mail;

class PurchaseOrderController extends Controller
{
    use Responses;

    public function index(): \Illuminate\Http\JsonResponse
    {
        $purchaseOrders = PurchaseOrder::storeFilter()->get();

        return $this->success(
            status: Response::HTTP_OK,
            message: 'Purchase Orders Retrieved Successfully',
            data: PurchaseOrderResource::collection($purchaseOrders)
        );
    }

    public function show(PurchaseOrder $purchaseOrder): \Illuminate\Http\JsonResponse
    {
        $purchaseOrder = $purchaseOrder->load(['condition', 'entries', 'entries.infos', 'entries.itemById']);

        return $this->success(
            status: Response::HTTP_OK,
            message: 'PurchaseOrder retrieved successfully',
            data: new PurchaseOrderResource($purchaseOrder),
        );
    }

    public function offline(Request $request): \Illuminate\Http\JsonResponse
    {
        $query = PurchaseOrder::Type()
            ->with(['condition', 'entries', 'entries.infos', 'entries.itemById']);

        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        } else {
            $query->where('status', '!=', 2);
        }

        $purchaseOrders = $query->paginate(15);

        return $this->AppSuccessPaginated(
            status: Response::HTTP_OK,
            message: 'Purchase Orders Retrieved Successfully',
            data: PurchaseOrderResource::collection($purchaseOrders),
        );
    }

    public function startProcess(PurchaseOrder $purchaseOrder): \Illuminate\Http\JsonResponse
    {
        $existingStart = PurchaseOrderProcessStart::where('purchase_order_id', $purchaseOrder->ID)->first();

        if ($existingStart) {
            return $this->error(
                status: Response::HTTP_BAD_REQUEST,
                message: 'Process already started'
            );
        }

        $processStart = PurchaseOrderProcessStart::create([
            'purchase_order_id' => $purchaseOrder->ID,
            'start_date' => now(),
        ]);

        return $this->success(
            status: Response::HTTP_OK,
            message: 'Process started successfully',
            data: [
                'purchase_order_id' => $processStart->purchase_order_id,
                'start_date' => $processStart->start_date,
            ]
        );
    }

    public function commitOrder(
        PurchaseOrder $purchaseOrder,
        CommitOrderRequest $request,
        CommitOrderService $service
    ): \Illuminate\Http\JsonResponse {
        return $service->commit($purchaseOrder, $request);
    }

    public function partialCommitOrder(
        PurchaseOrder $purchaseOrder,
        CommitOrderRequest $request,
        CommitOrderService $service
    ): \Illuminate\Http\JsonResponse {
        return $service->commit($purchaseOrder, $request);
    }

    public function allInfos(PurchaseOrderEntry $purchaseOrderEntry): \Illuminate\Http\JsonResponse
    {
        $purchaseOrderEntry = $purchaseOrderEntry->load(['infos', 'itemById']);

        return $this->success(
            status: Response::HTTP_OK,
            message: 'Infos retrieved successfully',
            data: new PurchaseOrderEntryResource($purchaseOrderEntry),
        );
    }

    public function updateInfos(
        UpdatePurchaseOrderEntryInfosRequest $request,
        PurchaseOrderEntry $purchaseOrderEntry,
        UpdateInfosService $service
    ): \Illuminate\Http\JsonResponse {
        return $service->update($purchaseOrderEntry, $request->validated());
    }

    public function testPdfEmailPrint(
        Request $request,
        PurchaseOrderPdfService $pdfService,
        PurchaseOrderPrintService $printerService
    ): \Illuminate\Http\JsonResponse {
        $validated = $request->validate([
            'purchase_order_id' => ['required', 'integer'],
        ]);


        $purchaseOrder = PurchaseOrder::with([
            'condition', 'entries', 'entries.infos', 'currentStore', 'otherStore'
        ])->find($validated['purchase_order_id']);

        if (!$purchaseOrder) {
            return response()->json([
                'message' => "Purchase Order {$validated['purchase_order_id']} not found"
            ], 404);
        }

        try {
            $users = PurchaseOrderEmail::whereIn('id', [80, 81, 82])
                ->where('is_active', 1)
                ->pluck('email')
                ->toArray();

            Mail::to($users)->send(
                new PurchaseOrderNotification($purchaseOrder, [], 'default')
            );

            $pdfService->generatePdf($purchaseOrder);

            $printerService->printPdf($purchaseOrder, 1);

            return response()->json([
                'message' => 'PDF created, email sent to specific users, and print queued successfully',
                'purchase_order_id' => $purchaseOrder->ID,
                'email_recipients' => $users
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error: '.$e->getMessage()
            ], 500);
        }
    }

}
