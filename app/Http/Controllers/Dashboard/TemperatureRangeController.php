<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Requests\Dashboard\TemperatureRange\StoreTempRequest;
use App\Http\Requests\Dashboard\TemperatureRange\UpdateTempRequest;
use App\Http\Resources\Dashboard\TemperatureRangeResource;
use App\Models\TemperatureRange;
use App\Traits\Responses;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

class TemperatureRangeController extends Controller
{
    use Responses ;
    public function index()
    {
        $packages = TemperatureRange::paginate();

        return $this->successPaginated(
            status: Response::HTTP_OK,
            message: 'Data retrieved Successfully',
            data: TemperatureRangeResource::collection($packages)
        );
    }

    public function store(StoreTempRequest $request)
    {
        $TemperatureRange = TemperatureRange::create([
            'department' => $request->post('department'),
            'min_temp' => $request->post('min_temp'),
            'max_temp' => $request->post('max_temp'),
        ]);

        return $this->success(
            status: Response::HTTP_CREATED,
            message: 'TemperatureRange Created Successfully',
            data: new TemperatureRangeResource($TemperatureRange)
        );

    }


    public function show(TemperatureRange $TemperatureRange)
    {
        return $this->success(
            status: Response::HTTP_OK,
            message: 'TemperatureRange Retrieved Successfully',
            data: new TemperatureRangeResource($TemperatureRange)
        );
    }


    public function update(UpdateTempRequest $request, TemperatureRange $TemperatureRange)
    {
        $TemperatureRange->update($request->validated());

        return $this->success(
            status: Response::HTTP_OK,
            message: 'TemperatureRange Updated Successfully',
            data: new TemperatureRangeResource($TemperatureRange)
        );
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TemperatureRange $TemperatureRange)
    {
        $TemperatureRange->delete();

        return $this->success(
            status: Response::HTTP_OK,
            message: 'TemperatureRange Deleted Successfully',
            data: []
        );


    }
}
