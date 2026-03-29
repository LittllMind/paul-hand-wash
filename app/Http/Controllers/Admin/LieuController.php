<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lieu;
use Illuminate\Http\Request;

class LieuController extends Controller
{
    public function index(Request $request)
    {
        $lieux = Lieu::latest()->paginate(10);

        return view('admin.lieux.index', compact('lieux'));
    }
}
