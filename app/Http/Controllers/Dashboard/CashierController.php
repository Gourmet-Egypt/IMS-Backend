<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Resources\Dashboard\CashierResource;
use App\Models\Cashier;
use App\Traits\Responses;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CashierController extends Controller
{
    use Responses ;

    public function index(Request $request)
    {
        $cashiers = Cashier::search($request->store_id)->get();

        return $this->success(
            status : Response::HTTP_OK,
            message : 'Cashier List',
            data    : CashierResource::collection($cashiers),
        );
    }

}
