<?php

namespace App\Http\Controllers;

use App\Http\Resources\CashierResource;
use App\Http\Resources\StoreResource;
use App\Models\Store;
use App\Traits\Responses;
use Illuminate\Http\Request;
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
