<?php

namespace App\Listeners;

use App\Events\TransferRequestCommitted;
use App\Services\TransferRequestPdfService;

class GeneratePdfsListener
{
    protected $pdfService;

    public function __construct(TransferRequestPdfService $pdfService)
    {
        $this->pdfService = $pdfService;
    }

    public function handle(TransferRequestCommitted $event)
    {
        $transferRequest = $event->transferRequest;


        $pdfs = $this->pdfService->generateAllPdfs($transferRequest);

    }



}
