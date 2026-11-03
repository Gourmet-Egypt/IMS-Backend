<?php

namespace App\Services\Commit;

use Illuminate\Http\Response;

/**
 * A commit failure that already knows which HTTP status it maps to.
 * The commit server catches this once and renders it via the Responses trait.
 */
class CommitException extends \RuntimeException
{
    public function __construct(string $message, public int $httpStatus = Response::HTTP_INTERNAL_SERVER_ERROR)
    {
        parent::__construct($message);
    }
}
