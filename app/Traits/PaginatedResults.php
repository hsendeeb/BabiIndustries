<?php

namespace App\Traits;

use Illuminate\Http\Request;

trait PaginatedResults
{
    protected function getPerPage(Request $request): int
    {
        return max(1, min(100, $request->integer('per_page', 15)));
    }
}
