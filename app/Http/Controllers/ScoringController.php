<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lead;

class ScoringController extends Controller
{
    public function index()
    {
        return view('scoring.index');
    }
}
