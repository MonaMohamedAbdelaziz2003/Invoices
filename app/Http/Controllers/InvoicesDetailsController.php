<?php

namespace App\Http\Controllers;

use App\Models\invoices;
use App\Models\invoices_attachments;
use App\Models\invoices_details;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class InvoicesDetailsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(invoices_details $invoices_details)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $invoices = invoices::where('id',$id)->first();
        $details  = invoices_details::where('id_Invoice',$id)->get();
        $attachments  = invoices_attachments::where('invoice_id',$id)->get();
        $unreadNotifications = auth()->user()->unreadNotifications;
        if ($unreadNotifications->isNotEmpty()) {
            foreach ($unreadNotifications as $notification) {
                if($notification["notifiable_id"]==auth()->user()->id&&$notification["data"]["id"]==$id){
                    $notification->markAsRead();
                }
            }
        }
       return view('invoices.invoices_details',compact('invoices','details','attachments'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, invoices_details $invoices_details)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $invp=invoices_attachments::findOrfail($request->id_file)->delete();
        Storage::disk('public_uploads')->delete($request->invoice_number . '/' . $request->file_name);
        session()->flash('delete',"تم حذف المرفق بنجاح");
        return back();
    }

    public function open_file($invoices_number , $file_name)
    {
        $path = $invoices_number . '/' . $file_name;
        if (Storage::disk('public_uploads')->exists($path)) {
            $fullPath = Storage::disk('public_uploads')->path($path);
            return response()->file($fullPath);
        } else {
            abort(404, 'File not found');
        }
    }


    public function download($invoices_number , $file_name)
    {
        $path = $invoices_number . '/' . $file_name;
        if (Storage::disk('public_uploads')->exists($path)) {
            $fullPath = Storage::disk('public_uploads')->path($path);
            return response()->download($fullPath);
        } else {
            abort(404, 'File n found');
        }
    }


}
