<?php

namespace App\Http\Controllers;

use App\Http\Resources\ItemResource;
use App\Models\Item;
use App\Traits\Responses;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ItemController extends Controller
{
    use Responses ;
    public function show($lookup)
    {
        $item = Item::search($lookup)->first();

        if(!$item){
            return $this->error(
                status: Response::HTTP_UNPROCESSABLE_ENTITY,
                message: 'Item not found'
            );
        }

        return $this->success(
            status:  Response::HTTP_OK,
            message: 'Item Retrieved Successfully',
            data: new ItemResource($item),
        );
    }
}
