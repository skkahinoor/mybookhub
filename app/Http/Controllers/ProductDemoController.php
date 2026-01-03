<?php

namespace App\Http\Controllers;

use App\Models\ProductDemo;
use Illuminate\Http\Request;

class ProductDemoController extends Controller
{
    public function store(Request $request)
    {

        $request->validate([
            'p_name'=>'required',
            'sku'=>'required',
            'qty'=>'required'


        ]);

        ProductDemo::create($request->all());
        return response()->json([
            'message' => 'Product added successfully!'
        ], 201);
    }
    public function update(Request $request,$id)
    {
        //
        // $product=ProductDemo::find($id);
        $product = $request->input();
        // dd($product);
        ProductDemo::where('id', isset($id) ? $id : $product['id'])->update([
            'p_name'     => $product['p_name'],
            'sku'    => $product['sku'],
            'qty' =>   $product['qty'],
        ]);

        // $product->update($request->all());
        // return $product;
        return response()->json([
            'message' => 'Product details updated successfully!'
        ], 202);

    }
    public function destroy($id)
    {
        //
         ProductDemo::destroy($id);
         return response()->json([
            'message' => 'Product deleted successfully!'
        ], 202);
        // return ProductDemo::destroy($id);
    }
}
