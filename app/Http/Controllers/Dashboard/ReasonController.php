<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\Reason\StoreReasonRequest;
use App\Http\Requests\Dashboard\Reason\UpdateReasonRequest;
use App\Http\Resources\Dashboard\ReasonResource;
use App\Models\Reason;
use App\Traits\Responses;
use Illuminate\Http\Response;

class ReasonController extends Controller
{
    use Responses ;

    public function index()
    {
        $packages = Reason::paginate();
        return $this->successPaginated(
            status: Response::HTTP_OK,
            message: 'Data retrieved Successfully',
            data: ReasonResource::collection($packages)
        );
    }

    public function store(StoreReasonRequest $request)
    {
        $Reason = Reason::create([
            'reason_type' => $request->post('reason_type'),
            'description' => $request->post('description'),
        ]);

        return $this->success(
            status: Response::HTTP_CREATED,
            message: 'Reason Created Successfully',
            data: new ReasonResource($Reason)
        );

    }


    public function show(Reason $Reason)
    {
        return $this->success(
            status: Response::HTTP_OK,
            message: 'Reason Retrieved Successfully',
            data: new ReasonResource($Reason)
        );
    }


    public function update(UpdateReasonRequest $request , Reason $Reason )
    {
        $Reason->update($request->validated());

        return $this->success(
            status: Response::HTTP_OK,
            message: 'Reason Updated Successfully',
            data: new ReasonResource($Reason)
        );


    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Reason $Reason)
    {
        $Reason->delete();

        return $this->success(
            status: Response::HTTP_OK,
            message: 'Reason Deleted Successfully',
            data: []
        );


    }
}
