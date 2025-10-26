<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Resources\Dashboard\VehicleTypeResource;
use App\Models\VehicleType;
use App\Traits\Responses;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class VehicleController extends Controller
{
    use Responses ;
    public function index()
    {
        $packages = VehicleType::paginate();
        return $this->successPaginated(
            status: Response::HTTP_OK,
            message: 'Data retrieved Successfully',
            data: VehicleTypeResource::collection($packages)
        );
    }

    public function store(Request $request)
    {
        $VehicleType = VehicleType::create([
            'name' => $request->post('name'),
        ]);

        return $this->success(
            status: Response::HTTP_CREATED,
            message: 'VehicleType Created Successfully',
            data: new VehicleTypeResource($VehicleType)
        );

    }


    public function show(VehicleType $VehicleType)
    {
        return $this->success(
            status: Response::HTTP_OK,
            message: 'VehicleType Retrieved Successfully',
            data: new VehicleTypeResource($VehicleType)
        );
    }


    public function update(Request $request , VehicleType $VehicleType )
    {
        $VehicleType->update([
            'name'=> $request->post('name') ,
            'updated_at' => now()
        ]);

        return $this->success(
            status: Response::HTTP_OK,
            message: 'VehicleType Updated Successfully',
            data: new VehicleTypeResource($VehicleType)
        );


    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(VehicleType $VehicleType)
    {
        $VehicleType->delete();

        return $this->success(
            status: Response::HTTP_OK,
            message: 'VehicleType Deleted Successfully',
            data: []
        );

    }
}
