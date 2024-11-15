<?php

namespace App\Http\Controllers;

use App\Models\Proveedores;
use Illuminate\Http\Request;

class ProvedorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $proveedor=Proveedores::all();
        return response()->json($proveedor);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $proveedor = Proveedores::create($request->post());
        return response()->json([
            'proveedor'=>$proveedor
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Proveedores $proveedor)
    {
        return response()->json($proveedor);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Proveedores $proveedor)
    {
        $proveedor->fill($request->post())->save();
        return response()->json([
            'proveedor'=>$proveedor
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Proveedores $proveedor)
    {
        $proveedor->delete();
        return response()->json([
           'mensaje'=>'Proveedor Eliminado'
        ]);
    }
}
