<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TestController extends Controller
{
    public function index()
    {
        return response()->json([
            'status' => 'success',
            'message' => 'Dashboard controller está funcionando!',
            'timestamp' => now(),
            'method' => request()->method()
        ]);
    }
    
    public function simple()
    {
        return 'Dashboard funcionando - Teste simples';
    }
}
