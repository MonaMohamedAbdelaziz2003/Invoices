<?php

namespace App\Http\Controllers;

use App\Http\Requests\section;
use App\Models\product;
use App\Models\sections;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use function Ramsey\Uuid\v1;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sections=sections::all();
        $product=product::all();
        return view('product.product', compact('sections','product'));
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

        product::create([
            'Product_name'=>$request->Product_name,
            'section_id'=>$request->section_id,
            'description'=>$request->description,
      ]);
              // }
              session()->flash('Add','add success');
              return redirect('/product');
        return $request;
    }

    /**
     * Display the specified resource.
     */
    public function show(product $product)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(product $product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $id=sections::where('section_name',$request->section_name)->first()->id;
        $product=product::findOrfail($request->pro_id);

        $product->update([
            'Product_name' =>$request->Product_name,
            'description'  =>$request->description,
            'section_id'   =>$id,
        ]);
        session()->flash('edit','edit update');
        return back();
        //  return $request->Product_name;

        // $id = sections::where('section_name', $request->section_name)->first()->id;
        // $Products = product::findOrFail($request->pro_id);

        // $Products->update([
        // 'product_name' => $request->Product_name,
        // 'description' => $request->description,
        // 'section_id' => $id,
        // ]);

        // session()->flash('Edit', 'تم تعديل المنتج بنجاح');
        // return back();
    }

    public function destroy(Request $request)
    {
        $id=$request->pro_id;
        product::find($id)->delete();
        return back();
    }


}
