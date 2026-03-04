<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Resources\Dashboard\GoodTypeResource;
use App\Models\GoodsType;
use App\Traits\Responses;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class GoodTypeController extends Controller
{
    use Responses;

    public function index()
    {
        $packages = GoodsType::paginate();
        return $this->successPaginated(
            status: Response::HTTP_OK,
            message: 'Data retrieved Successfully',
            data: GoodTypeResource::collection($packages)
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'min_temp' => 'nullable|numeric|min:-50|max:50',
            'max_temp' => 'nullable|numeric|min:-50|max:50',
        ]);

        $GoodType = GoodsType::create([
            'name' => $request->post('name'),
            'min_temp' => $request->post('min_temp'),
            'max_temp' => $request->post('max_temp'),
        ]);

        return $this->success(
            status: Response::HTTP_CREATED,
            message: 'GoodType Created Successfully',
            data: new GoodTypeResource($GoodType)
        );

    }


    public function show(GoodsType $GoodType)
    {
        return $this->success(
            status: Response::HTTP_OK,
            message: 'GoodType Retrieved Successfully',
            data: new GoodTypeResource($GoodType)
        );
    }


    public function update(Request $request, GoodsType $GoodType)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'min_temp' => 'nullable|numeric|min:-50|max:50',
            'max_temp' => 'nullable|numeric|min:-50|max:50',
        ]);

        $GoodType->update([
            'name' => $request->post('name'),
            'min_temp' => $request->post('min_temp'),
            'max_temp' => $request->post('max_temp'),
            'updated_at' => now()
        ]);

        return $this->success(
            status: Response::HTTP_OK,
            message: 'GoodType Updated Successfully',
            data: new GoodTypeResource($GoodType)
        );

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(GoodsType $GoodType)
    {
        $GoodType->delete();

        return $this->success(
            status: Response::HTTP_OK,
            message: 'GoodType Deleted Successfully',
            data: []
        );


    }
}
