<?php

namespace App\Http\Controllers;

class HomeController
{
    public function index()
    {
        $name = "MyFramework";
        return view('home', compact('name'));
    }
}
