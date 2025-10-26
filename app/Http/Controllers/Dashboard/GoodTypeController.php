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
    use Responses ;

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
        $GoodType = GoodsType::create([
            'name' => $request->post('name'),
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


    public function update(Request $request , GoodsType $GoodType )
    {
        $GoodType->update(['name'=> $request->post('name') , 'updated_at' => now()]);

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
