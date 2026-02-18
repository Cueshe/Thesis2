<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PDFTestController extends Controller
{
    // PDF test controller removed. This stub returns a 404 for safety.
    public function __call($name, $args)
    {
        abort(404);
    }
}
