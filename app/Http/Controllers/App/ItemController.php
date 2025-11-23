<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Http\Resources\App\Item\ItemResource;
use App\Http\Resources\App\Item\ShowItemResource;
use App\Models\Item;
use App\Traits\Responses;
use Illuminate\Http\Response;

class ItemController extends Controller
{
    use Responses;

    public function index()
    {
        $last_updated = request('last_updated');
        $items = Item::IndexSearch($last_updated)->with('aliases')->paginate(2000);
        return $this->appSuccessPaginated(
            status: Response::HTTP_OK,
            message: 'Items retrieved successfully',
            data: ItemResource::collection($items)
        );
    }

    public function show($lookup)
    {
        $item = Item::ShowSearch($lookup)->first();
        return $this->success(
            status: Response::HTTP_OK,
            message: 'Item Retrieved Successfully',
            data: new ShowItemResource($item),
        );
    }


}
