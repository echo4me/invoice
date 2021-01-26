<?php

namespace App\Http\Controllers;

use App\Invoices;
use App\InvoicesAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class InvoiceAchiveController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $invoices = Invoices::onlyTrashed()->get(); // use softDelete
        return view('invoices.archive_invoices',compact('invoices'));
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $id = $request->invoice_id;
        // show rows that have date in deleted_at (SoftDelete)
        $invoices = Invoices::withTrashed()->where('id',$id)->first();
        $invoices->restore(); // method for softDelete
        session()->flash('restore_invoice');
        return redirect('invoices');
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $id = $request->invoice_id;
        $invoices = Invoices::withTrashed()->where('id',$id)->first(); // first to get one row only
        $Details = InvoicesAttachment::where('invoice_id', $id)->first();
        
        // Delete the attachment + recored invoice from db
        if (!empty($Details->invoice_number)){Storage::disk('public_uploads')->deleteDirectory($Details->invoice_number); }
        $invoices->forceDelete();// use Delete on SoftDelete()
        session()->flash('delete_invoice'); //
        return redirect('Archive');
        return $request;
    }
}
