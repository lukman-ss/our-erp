<?php

namespace App\Http\Controllers\Frontend\Sales;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        return view('page.sales.product.index');
    }
    
    public function create ()
    {
        return view('page.sales.product.create');
    }
}
