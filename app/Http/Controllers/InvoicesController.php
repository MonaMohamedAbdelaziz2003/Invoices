<?php
namespace App\Http\Controllers;

use App\Exports\InvoicesExport;
use App\Models\invoices;
use App\Models\invoices_attachments;
use App\Models\invoices_details;
use App\Models\sections;
use App\Models\User;
use App\Notifications\invoicePaid;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB as FacadesDB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class InvoicesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $invoices=invoices::all();
        return view('invoices.invoice',compact('invoices'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $sections=sections::all();
        return view('invoices.add_invoices',compact("sections"));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        invoices::create([
            'invoice_number' => $request->invoice_number,
            'invoice_Date' => $request->invoice_Date,
            'Due_date' => $request->Due_date,
            'product' => $request->product,
            'section_id' => $request->Section,
            'Amount_collection' => $request->Amount_collection,
            'Amount_Commission' => $request->Amount_Commission,
            'Discount' => $request->Discount,
            'Value_VAT' => $request->Value_VAT,
            'Rate_VAT' => $request->Rate_VAT,
            'Total' => $request->Total,
            'Status' => 'غير مدفوعة',
            'Value_Status' => 2,
            'note' => $request->note,
        ]);

        $invoice_id = invoices::latest()->first()->id;
        invoices_details::create([
            'id_Invoice' => $invoice_id,
            'invoice_number' => $request->invoice_number,
            'product' => $request->product,
            'Section' => $request->Section,
            'Status' => 'غير مدفوعة',
            'Value_Status' => 2,
            'note' => $request->note,
            'user' => (Auth::user()->name),
        ]);

        if ($request->hasFile('pic')) {

            $invoice_id = Invoices::latest()->first()->id;
            $image = $request->file('pic');
            $file_name = $image->getClientOriginalName();
            $invoice_number = $request->invoice_number;

            $attachments = new invoices_attachments();
            $attachments->file_name = $file_name;
            $attachments->invoice_number = $invoice_number;
            $attachments->Created_by = Auth::user()->name;
            $attachments->invoice_id = $invoice_id;
            $attachments->save();

            // move pic
            $imageName = $request->pic->getClientOriginalName();
            $request->pic->move(public_path('Attachments/' . $invoice_number), $imageName);
        }
// /////////////////send mail
          $user = User::first();
          Notification::send($user, new invoicePaid($invoice_id));
// ///////////////// snd notification
        $user = User::get();
        $invoices = invoices::latest()->first();
        Notification::send($user, new \App\Notifications\add_invoices($invoices));

        // event(new MyEventClass('hello world'));
        session()->flash('Add', 'تم اضافة الفاتورة بنجاح');
        return back();
    }

    public function send(){
        // Mail::to(Auth::user()->email)->send(new sendmail());
        $invoice_id = invoices::latest()->first()->id;
        $user = User::first();
        Notification::send($user, new invoicePaid($invoice_id)); //invoicePaid is notyfi conten all content you send
        // Mail::to("mona1892003@gmail.com")->send(new sendmail());//sendmail is mail conten all content you send
        return "success";
    }
    /**
     * Display the specified resource.
     */
    public function Status_Update($id, Request $request)
    {
        // $invoices=invoices::where("id",$id)->first();
        // $invoices=invoices::where("id",$id)->get();
        $invoices=invoices::findOrfail($id);
         if ($request->Status === 'مدفوعة') {

            $invoices->update([
                'Value_Status' => 1,
                'Status' => $request->Status,
                'Payment_Date' => $request->Payment_Date,
            ]);

            invoices_Details::create([
                'id_Invoice' => $request->invoice_id,
                'invoice_number' => $request->invoice_number,
                'product' => $request->product,
                'Section' => $request->Section,
                'Status' => $request->Status,
                'Value_Status' => 1,
                'note' => $request->note,
                'Payment_Date' => $request->Payment_Date,
                'user' => (Auth::user()->name),
            ]);
        }

        else {
            $invoices->update([
                'Value_Status' => 3,
                'Status' => $request->Status,
                'Payment_Date' => $request->Payment_Date,
            ]);
            invoices_Details::create([
                'id_Invoice' => $request->invoice_id,
                'invoice_number' => $request->invoice_number,
                'product' => $request->product,
                'Section' => $request->Section,
                'Status' => $request->Status,
                'Value_Status' => 3,
                'note' => $request->note,
                'Payment_Date' => $request->Payment_Date,
                'user' => (Auth::user()->name),
            ]);
        }
        session()->flash('Status_Update');
        return redirect('/invoices');
    }
    public function show($id)
    {
        $invoices=invoices::where("id",$id)->first();
        return view('invoices.status_update', compact('invoices'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $invoices=invoices::where('id',$id)->first();
        $sections=sections::all();
        return view('invoices.edit_invoices',compact('invoices','sections'));

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, invoices $invoices)
    {
        $invoices=invoices::findOrfail($request->invoice_id);
        $invoices->update([
            'invoice_number' => $request->invoice_number,
            'invoice_Date' => $request->invoice_Date,
            'Due_date' => $request->Due_date,
            'product' => $request->product,
            'section_id' => $request->Section,
            'Amount_collection' => $request->Amount_collection,
            'Amount_Commission' => $request->Amount_Commission,
            'Discount' => $request->Discount,
            'Value_VAT' => $request->Value_VAT,
            'Rate_VAT' => $request->Rate_VAT,
            'Total' => $request->Total,
            'note' => $request->note,
        ]);
        session()->flash('edit', 'تم تعديل الفاتورة بنجاح');
        return back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $id=$request->invoice_id;
        $invoice=invoices::where('id',$id)->first();
        $details=invoices_attachments::where('invoice_id',$id)->first();
        // $details=invoices_attachments::findOrfail($id);
        $id_page =$request->id_page;


        if (!$id_page==2) {

            if (!empty($details->invoice_number)) {
                Storage::disk('public_uploads')->deleteDirectory( $details->invoice_number );
            }

        $invoice->forceDelete();
        session()->flash('delete_invoice');
        return redirect('/invoices');

        }

        else {

            $invoice->delete();
            session()->flash('archive_invoice');
            return redirect('/Archive');
        }
        // $invoice->forceDelete();

        session()->flash('delete_invoice');
        return redirect('/invoices');
    }

    public function getproduct($id){
        $product=FacadesDB::table('products')->where("section_id",$id)->pluck("product_name","id");
        return json_encode($product) ;
    }


    public function Invoices_Paid(){
       $invoices=invoices::where('Value_Status',1)->get();
       return view('invoices.invoices_paid',compact('invoices'));
    }
    public function Invoices_UnPaid(){
        $invoices=invoices::where('Value_Status',2)->get();
        return view('invoices.invoices_paid',compact('invoices'));
    }
    public function Invoices_Partial(){
        $invoices=invoices::where('Value_Status',3)->get();
       return view('invoices.invoices_paid',compact('invoices'));
    }
    public function Print_invoice($id){
        $invoices=invoices::where('id',$id)->first();
       return view('invoices.print_invoices',compact('invoices'));
    }


    public function export()
    {
        return Excel::download(new InvoicesExport, 'invoices.xlsx');
    }


    public function MarkAsRead_all()
    {
        // return "mm";
        $unreadnotify= auth()->user()->unreadNotifications;
        if($unreadnotify){
            $unreadnotify->MarkAsRead();
            return back();
        }
    }


}
