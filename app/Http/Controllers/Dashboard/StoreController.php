<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Resources\Dashboard\StoreResource;
use App\Models\Store;
use App\Traits\Responses;
use Illuminate\Http\Response;

class StoreController extends Controller
{
    use Responses ;

    public function index ()
    {
        $stores = Store::all() ;

        return $this->success(
            status : Response::HTTP_OK,
            message : 'Stores List',
            data    : StoreResource::collection($stores),
        );

    }

}
