<?php

namespace App\Http\Controllers;

use App\InvoicesAttachment;
use Illuminate\Http\Request;

class InvoicesAttachmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if($request->has('file_name')) 
        {
            
            $this->validate($request,['file_name' =>'mimes:pdf,jpeg,png,gif,jpg', ],['file_name.mimes' => 'يرجى ادخال صورة او ملف PDF' ]);
            
            $image = $request->file('file_name');
            $file_name = $image->getClientOriginalName();
            $attachments = new InvoicesAttachment();
            $attachments->file_name      = $file_name;
            $attachments->invoice_number = $request->invoice_number;
            $attachments->created_by     = auth()->user()->name;
            $attachments->invoice_id     = $request->invoice_id;
            $attachments->save();

            // move pic to public/Attachment/
            $imageName = $request->file_name->getClientOriginalName();
            $request->file_name->move(public_path('Attachments/' . $request->invoice_number), $imageName);
        
            session()->flash('Add', 'تم اضافة المرفق بنجاح');
            return back();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\InvoicesAttachment  $invoicesAttachment
     * @return \Illuminate\Http\Response
     */
    public function show(InvoicesAttachment $invoicesAttachment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\InvoicesAttachment  $invoicesAttachment
     * @return \Illuminate\Http\Response
     */
    public function edit(InvoicesAttachment $invoicesAttachment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\InvoicesAttachment  $invoicesAttachment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, InvoicesAttachment $invoicesAttachment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\InvoicesAttachment  $invoicesAttachment
     * @return \Illuminate\Http\Response
     */
    public function destroy(InvoicesAttachment $invoicesAttachment)
    {
        //
    }
}
