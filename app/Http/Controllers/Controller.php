<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public const DEFAULT_SORT           = 'id|asc';
    public const DEFAULT_ITEMS_PER_PAGE = 25;
    public const MIN_ITEMS_PER_PAGE     = 1;
    public const MAX_ITEMS_PER_PAGE     = 100;
}
