<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Requests\Dashboard\PurchaseOrderEmail\StorePurchaseOrderEmailRequest;
use App\Http\Requests\Dashboard\PurchaseOrderEmail\UpdatePurchaseOrderEmailRequest;
use App\Http\Resources\Dashboard\PurchaseOrderEmailResource;
use App\Models\PurchaseOrderEmail;
use App\Traits\Responses;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

class PurchaseOrderEmailController extends Controller
{
    use Responses;

    public function index()
    {
        $emails = PurchaseOrderEmail::paginate(10);

        return $this->successPaginated(
            status: Response::HTTP_OK,
            message: 'Purchase order emails retrieved successfully',
            data: PurchaseOrderEmailResource::collection($emails)
        );
    }

    public function store(StorePurchaseOrderEmailRequest $request)
    {
        $email = PurchaseOrderEmail::create($request->validated());

        return $this->success(
            status: Response::HTTP_CREATED,
            message: 'Purchase order email created successfully',
            data: new PurchaseOrderEmailResource($email)
        );
    }

    public function show($id)
    {
        $email = PurchaseOrderEmail::find($id);

        if (!$email) {
            return $this->error(
                status: Response::HTTP_NOT_FOUND,
                message: 'Purchase order email not found'
            );
        }

        return $this->success(
            status: Response::HTTP_OK,
            message: 'Purchase order email retrieved successfully',
            data: new PurchaseOrderEmailResource($email)
        );
    }

    public function update(UpdatePurchaseOrderEmailRequest $request, $id)
    {
        $email = PurchaseOrderEmail::find($id);

        if (!$email) {
            return $this->error(
                status: Response::HTTP_NOT_FOUND,
                message: 'Purchase order email not found'
            );
        }

        $updateData = array_filter($request->validated(), fn($value) => !is_null($value));

        $email->update($updateData);

        return $this->success(
            status: Response::HTTP_OK,
            message: 'Purchase order email updated successfully',
            data: new PurchaseOrderEmailResource($email->fresh())
        );
    }

    public function destroy($id)
    {
        $email = PurchaseOrderEmail::find($id);

        if (!$email) {
            return $this->error(
                status: Response::HTTP_NOT_FOUND,
                message: 'Purchase order email not found'
            );
        }

        $email->delete();

        return $this->success(
            status: Response::HTTP_OK,
            message: 'Purchase order email deleted successfully',
            data: new PurchaseOrderEmailResource($email)
        );
    }
}
