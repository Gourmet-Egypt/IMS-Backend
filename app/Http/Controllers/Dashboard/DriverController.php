<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Requests\Dashboard\Driver\StoreDriverRequest;
use App\Http\Requests\Dashboard\Driver\UpdateDriverRequest;
use App\Http\Resources\Dashboard\DriverResource;
use App\Models\Driver;
use App\Traits\Responses;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

class DriverController extends Controller
{
    use Responses;

    public function index()
    {
        $drivers = Driver::paginate(10);

        return $this->successPaginated(
            status: Response::HTTP_OK,
            message: 'Drivers retrieved successfully',
            data: DriverResource::collection($drivers)
        );
    }

    public function store(StoreDriverRequest $request)
    {
        $data = $request->validated();

        $driver = Driver::create($data);

        return $this->success(
            status: Response::HTTP_CREATED,
            message: 'Driver created successfully',
            data: new DriverResource($driver)
        );
    }

    public function show(Driver $driver)
    {
        return $this->success(
            status: Response::HTTP_OK,
            message: 'Driver retrieved successfully',
            data: new DriverResource($driver)
        );
    }

    public function update(Driver $driver, UpdateDriverRequest $request)
    {
        $driver->update($request->validated());

        return $this->success(
            status: Response::HTTP_OK,
            message: 'Driver updated successfully',
            data: new DriverResource($driver->fresh())
        );
    }

    public function destroy(Driver $driver)
    {
        $driver->delete();

        return $this->success(
            status: Response::HTTP_OK,
            message: 'Driver deleted successfully',
            data: []
        );
    }
}
