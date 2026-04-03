<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function index()
    {
        $devices = auth()->user()->devices;

        return view('profile', compact('devices'));
    }
}
