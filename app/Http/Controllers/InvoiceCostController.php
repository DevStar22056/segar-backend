<?php

namespace App\Http\Controllers;

use App\InvoiceCost;
use Illuminate\Http\Request;

class InvoiceCostController extends Controller
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
     * @param Request $request
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {

        $this->validate($request, [
            'cost_value' => 'required',
            'cost_vat' => 'required',
            'cost_vat_value' => 'required',
            'cost_vat_only' => 'required',
            'invoice_id' => 'required',
            'user_id' => 'required',
            'cost_type' => 'required'
        ]);

        $cost = new InvoiceCost;

        $cost->cost_value = $request->cost_value;
        $cost->cost_vat = $request->cost_vat;
        $cost->cost_vat_only = $request->cost_vat_only;
        $cost->cost_vat_value = $request->cost_vat_value;
        $cost->cost_description = $request->cost_description;
        $cost->cost_files = $request->cost_files;
        $cost->invoice_id = $request->invoice_id;
        $cost->user_id = $request->user_id;
        $cost->cost_type = $request->cost_type;

        $cost->save();

//        return new InvoiceCostResource($cost);

    }

    /**
     * Display the specified resource.
     *
     * @param \App\InvoiceCost $invoiceCost
     * @return \Illuminate\Http\Response
     */
    public function show(InvoiceCost $invoiceCost)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\InvoiceCost $invoiceCost
     * @return \Illuminate\Http\Response
     */
    public function edit(InvoiceCost $invoiceCost)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\InvoiceCost $invoiceCost
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, InvoiceCost $invoiceCost)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\InvoiceCost $invoiceCost
     * @return \Illuminate\Http\Response
     */
    public function destroy(InvoiceCost $invoiceCost)
    {
        //
    }
}
