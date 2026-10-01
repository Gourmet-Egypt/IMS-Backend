<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Http\Requests\App\Supplier\SearchSupplierItemRequest;
use App\Http\Resources\App\Item\ShowItemResource;
use App\Http\Resources\App\Supplier\ShowSupplierResource;
use App\Http\Resources\App\Supplier\SupplierResource;
use App\Models\Item;
use App\Models\Supplier;
use App\Traits\Responses;
use Illuminate\Http\Response;

class SupplierController extends Controller
{
    use Responses;

    public function index()
    {
        $suppliers = Supplier::select([
            'ID',
            'HQID',
            'Code',
            'SupplierName',
            'ContactName',
            'PhoneNumber',
            'EmailAddress',
            'Country',
            'State',
            'City',
            'LastUpdated',
        ])
            ->orderBy('SupplierName')
            ->get();

        return $this->success(
            status: Response::HTTP_OK,
            message: 'Suppliers retrieved successfully',
            data: SupplierResource::collection($suppliers)
        );
    }

    public function show(Supplier $supplier)
    {
        $supplier->load([
            'supplierItems' => fn ($query) => $query
                ->select(['ID', 'SupplierID', 'ItemID'])
                ->with(['item:ID,ItemLookupCode,Description'])
                ->orderBy('ItemID'),
        ]);

        return $this->success(
            status: Response::HTTP_OK,
            message: 'Supplier retrieved successfully',
            data: new ShowSupplierResource($supplier)
        );
    }

    public function searchItem(SearchSupplierItemRequest $request, Supplier $supplier)
    {
        $item = Item::select(['ID', 'HQID', 'ItemLookupCode', 'Description'])
            ->where('ItemLookupCode', $request->validated('lookupcode'))
            ->first();

        if (! $supplier->supplierItems()->where('ItemID', $item->ID)->exists()) {
            return $this->error(
                status: Response::HTTP_NOT_FOUND,
                message: 'Item is not related to this supplier',
                data: null
            );
        }

        return $this->success(
            status: Response::HTTP_OK,
            message: 'Supplier item search completed successfully',
            data: new ShowItemResource($item)
        );
    }
}
