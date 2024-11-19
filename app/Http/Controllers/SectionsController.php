<?php

namespace App\Http\Controllers;

use App\Http\Requests\section;
use App\Models\sections;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SectionsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $section=sections::all();
        return view('section.section',compact('section'));
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
    public function store(section $request)
    {

        // $input=$request->all();
        // $b_exist=sections::where('section_name','=',$input['section_name'])->exists();
        // if($b_exist){
        //     session()->flash('Error','section already exist');
        // }else{
        $validations=$request->validated();
        // $validations=$request->validate([
        //     'section_name'=>'required|unique:sections|max:255',
        //     'description'=>'required',
        // ],[
        //     'section_name.required'=>' filed is required ',
        //     'section_name.unique'=>' filed is arledy exist  ',

        // ]);
       sections::create([
      'section_name'=>$request->section_name,
      'description'=>$request->description,
      'created_by'=>(Auth::user()->name),
]);
        // }
        session()->flash('Add','update success');
        return redirect('/section');
    }

    /**
     * Display the specified resource.
     */
    public function show(sections $sections)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(sections $sections)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(section $request)
    {
        $validations=$request->validated();
        $id = $request->id;
        $sections = sections::find($id);
        $sections->update([
            'section_name' => $request->section_name,
            'description' => $request->description,
        ]);

        session()->flash('edit','update success');
        return redirect('/section');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $id = $request->id;
        sections::find($id)->delete();
        session()->flash('delete','deleted success');
        return redirect('/section');
    }
}
