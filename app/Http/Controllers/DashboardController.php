<?php

namespace App\Http\Controllers;

use App\Models\WebSetup\SidebarNav;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // $this->checkLogin();
        return view('welcome');
    }

    public function test()
    {
        return $navItems = SidebarNav::whereNull('parent_id')
            ->where('status', 'A')
            ->with('children')
            ->orderBy('order')
            ->get();
    }
}
