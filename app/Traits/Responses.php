<?php

namespace  App\Traits;

trait Responses{


    public function success(int $status = 200 , string $message  , array|object $data = null ){
        return response()->json([
            'status' => $status,
            'message' => $message,
            'data' => $data
        ] , $status);
    }

    public function error(int $status = 500 , string $message , $data = null ){
        return response()->json([
            'status' => $status,
            'message' => $message,
            'data' => $data
        ] , $status);
    }

    public function successPaginated(int $status = 200, string $message = 'Success', array|object $data)
    {
        $currentPage = $data->currentPage();
        $perPage = $data->perPage();
        $total = $data->total();
        $lastPage = $data->lastPage();

        $pagination = [
            'current_page' => $currentPage,
            'per_page' => $perPage,
            'total' => $total,
            'last_page' => $lastPage,
            'next_page_url' => $data->nextPageUrl(),
            'prev_page_url' => $data->previousPageUrl(),
            'current_page_url' => $data->url($currentPage),
        ];

        return response()->json([
            'status' => $status,
            'message' => $message,
            'data' => $data->items(),
            'pagination' => $pagination,
        ]);
    }

    public function appSuccessPaginated(int $status = 200, string $message = 'Success', array|object $data)
    {
        $currentPage = $data->currentPage();
        $perPage = $data->perPage();
        $total = $data->total();
        $lastPage = $data->lastPage();

        $pagination = [
            'current_page' => $currentPage,
            'per_page' => $perPage,
            'total' => $total,
            'last_page' => $lastPage,
            'next_page_url' => $data->nextPageUrl(),
            'prev_page_url' => $data->previousPageUrl(),
            'current_page_url' => $data->url($currentPage),
        ];

        return response()->json([
            'status' => $status,
            'message' => $message,
            'data' => ['page'=> $data->items() , 'pagination' => $pagination],
        ]);
    }



}
