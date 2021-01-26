<?php

namespace App\Http\Controllers;

use App\Exports\InvoicesExport;
use App\Invoices;
use App\InvoicesAttachment;
use App\InvoicesDetails;
use App\Notifications\AddInvoice;
use App\Notifications\InvoiceTask;
use App\Sections;
use App\User;
use Carbon\Cli\Invoker;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification as FacadesNotification;
use Illuminate\Support\Facades\Storage;

class InvoicesController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $invoices = Invoices::all();
        return  view('invoices.invoices',compact('invoices'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $sections = Sections::all();
        return view('invoices.add_invoices',compact('sections'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request,[
            'invoice_number'     => 'required',
            'Due_date'           => 'required',
            'Section'         => 'required',
            'Amount_collection'  => 'required',
            'Amount_Commission'  => 'required',
            'Value_VAT'          => 'required',
        ],[
            'invoice_number.required' =>'يرجي ادخال اسم المنتج',
            'Due_date.required' =>'يرجي ادخال تاريخ استحقاق الفاتورة',
            'Section.required' =>'يرجي ادخال اسم القسم',
            'Amount_collection.required' =>'يرجي ادخال مبلغ التحصيل',
            'Amount_Commission.required' =>'يرجي ادخال مبلغ العموله',
            'Value_VAT.required' =>'يرجي ادخال النسبة المئويه',
        ]);

        Invoices::create([
            'invoice_number' => $request->invoice_number,
            'invoice_date' => $request->invoice_Date,
            'due_date' => $request->Due_date,
            'product' => $request->product,
            'section_id' => $request->Section,
            'amount_collection' => $request->Amount_collection,
            'amount_commission' => $request->Amount_Commission,
            'discount' => $request->Discount,
            'value_vat' => $request->Value_VAT,
            'rate_vat' => $request->Rate_VAT,
            'total' => $request->Total,
            'status' => 'غير مدفوعة',
            'value_status' => 2,
            'note' => $request->note,
        ]);
        // Get id for last id inside Table Invoices
        $invoice_id = Invoices::latest()->first()->id;
        // insert into Second Table InvoicesDetails Table 
        InvoicesDetails::create([
            'id_invoice'         => $invoice_id ,
            'invoice_number'     => $request->invoice_number,
            'product'            => $request->product,
            'section'            => $request->Section,
            'status'             => 'غير مدفوعة',
            'value_status'       => 2,
            'note'      => $request->note,
            'user'      => auth()->user()->name,
        ]);
        // Add image into third table 
        if ($request->hasFile('pic')) {
            $this->validate($request,[
                'pic' => 'required|image'
            ]);

            $invoice_id = Invoices::latest()->first()->id; // get latest invoice_id for relation
            $image = $request->file('pic');
            $file_name = $image->getClientOriginalName();
            $invoice_number = $request->invoice_number;

            //insert request data into table then Save()
            $attachments = new InvoicesAttachment();
            $attachments->file_name = $file_name; 
            $attachments->invoice_number = $invoice_number;
            $attachments->created_by = auth()->user()->name;
            $attachments->invoice_id = $invoice_id;
            $attachments->save();

            // move pic to public/Attachment/
            $imageName = $request->pic->getClientOriginalName();
            $request->pic->move(public_path('Attachments/' . $invoice_number), $imageName);
        }

        // Send notifcation mail 
        $user = User::first();
        FacadesNotification::send($user,new AddInvoice($invoice_id));
        
        // Save and Send notifcation 

        $user = User::get(); // send notfication for all users
        //$user = User::find(auth()->user()->id); // send notfication for only user who create invoice
        $invoice = Invoices::latest()->first(); // get latest invoice_id for relation
        // $user->each->notify(new \App\Notifications\InvoiceTask($invoice)); //SAve notfication too 
        FacadesNotification::send($user,new InvoiceTask($invoice)); //SAve notfication 
        
        session()->flash('Add', 'تم اضافة الفاتورة بنجاح');
        return back();

        //end of Store()
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Invoices  $invoices
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        
        $invoices =Invoices::where('id',$id)->first();
        return view('invoices.status_update',compact('invoices'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Invoices  $invoices
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $invoices = Invoices::where('id',$id)->first();
        
        $sections =Sections::all();
        return view('invoices.edit_invoices',compact('sections','invoices'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Invoices  $invoices
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $invoices = invoices::findOrFail($request->invoice_id);
        $invoices->update([
            'invoice_number' => $request->invoice_number,
            'invoice_date' => $request->invoice_date,
            'due_date' => $request->due_date,
            'product' => $request->product,
            'section_id' => $request->Section,
            'amount_collection' => $request->amount_collection,
            'amount_commission' => $request->amount_commission,
            'discount' => $request->discount,
            'value_vat' => $request->value_vat,
            'rate_vat' => $request->rate_vat,
            'total' => $request->total,
            'note' => $request->note,
        ]);

        session()->flash('edit', 'تم تعديل الفاتورة بنجاح');
        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Invoices  $invoices
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $id = $request->invoice_id;
        $invoices = Invoices::where('id', $id)->first();
        $Details = InvoicesAttachment::where('invoice_id', $id)->first();
        
        $id_page = $request->id_page;

        if(!$id_page  == 2)
        {
            // Delete the attachment + recored invoice from db
            if (!empty($Details->invoice_number)){Storage::disk('public_uploads')->deleteDirectory($Details->invoice_number); }
            $invoices->forceDelete();
            session()->flash('delete_invoice');
            return redirect('/invoices');
        }else{
            //Archive the invoices
            $invoices->delete();
            session()->flash('archive_invoice');
            return redirect('Archive');
        }
            
            
            
    }

    // method to return data of products as Json by ID http://laravel.local/section/3
    public function getProducts($id)
    {
        // will show Products_name by section_name ($id)
        $data = DB::table('products')->where('section_id','=',$id)->pluck('Product_name','id');
        return json_encode($data);
    }

    //method to Update Payment Status 
    public function Status_Update($id,Request $request)
    {
        $invoices = invoices::findOrFail($id);

        if ($request->status === 'مدفوعة') 
        {
            $invoices->update([
                'value_status' => 1,
                'status' => $request->status,
                'payment_date' => $request->payment_date,
            ]);

            InvoicesDetails::create([
                'id_invoice' => $request->invoice_id,
                'invoice_number' => $request->invoice_number,
                'product' => $request->product,
                'section' => $request->section,
                'status' => $request->status,
                'value_status' => 1,
                'note' => $request->note,
                'payment_date' => $request->payment_date,
                'user' => (auth()->user()->name),
            ]);
        }

        else {
            $invoices->update([
                'value_status' => 3,
                'Status' => $request->Status,
                'Payment_Date' => $request->Payment_Date,
            ]);
            InvoicesDetails::create([
                'id_invoice' => $request->invoice_id,
                'invoice_number' => $request->invoice_number,
                'product' => $request->product,
                'section' => $request->section,
                'status' => $request->status,
                'Value_Status' => 3,
                'note' => $request->note,
                'payment_date' => $request->Payment_Date,
                'user' => (auth()->user()->name),
            ]);
        }
        session()->flash('Status_Update');
        return redirect('/invoices');

    }

    public function Invoice_Paid()
    {
        $invoices = Invoices::where('value_status', 1)->get();
        return view('invoices.invoices_paid',compact('invoices'));
    }

    public function Invoice_unPaid()
    {
        $invoices = Invoices::where('value_status',2)->get();
        return view('invoices.invoices_unpaid',compact('invoices'));
    }

    public function Invoice_Partial()
    {
        $invoices = Invoices::where('value_status',3)->get();
        return view('invoices.invoices_Partial',compact('invoices'));
    }
    // print method
    public function Print_invoice($id)
    {
        $invoices = Invoices::where('id',$id)->first() ;
        return view('invoices.print_invoice',compact('invoices'));
    }
    
    // Export Method
    public function export() 
    {
        //https://docs.laravel-excel.com/3.1/exports/
        return Excel::download(new InvoicesExport, 'invoices.html');
    }
    // Make notfication Read
    public function MarkAsRead_all (Request $request)
    {
        $userUnreadNotification= auth()->user()->unreadNotifications;
        if($userUnreadNotification) {
            $userUnreadNotification->markAsRead();
            return back();
        }
    }

    // Get Count
    public function unreadNotifications_count()
    {
        return auth()->user()->unreadNotifications->count();
    }

    public function unreadNotifications()
    {
        foreach (auth()->user()->unreadNotifications as $notification)
        {
        return $notification->data['title'];
        }
    }



    
}
