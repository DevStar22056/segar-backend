<?php

namespace App\Http\Controllers;

use App\Device;
use App\InvoiceContractor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use JWTAuth;

class InvoiceContractorController extends Controller
{
    public function index()
    {

    }

    public function show(Request $request, $id)
    {
        // invoice contracotrs
        $invoiceContractor = DB::table('invoice_contractors')
            ->where('invoice_id', '=', $id)
            ->join('users', 'users.id', '=', 'invoice_contractors.user_id')
            ->get(['name', 'surname', 'internal_hour_rate', 'users.id', 'hours_value', 'netto', 'gross', 'vat']);
        return response()->json(array('data' => $invoiceContractor), 200);
    }

    public function update(Request $request, Device $device)
    {
        $this->validate($request, [
            'invoice_id' => 'required',
            'user_id' => 'required',
            'hours_value' => 'required',
            'netto' => 'required',
            'vat' => 'required',
            'gross' => 'required',
        ]);

        // handle fields
        $device->update($request->toArray());

        return response()->json(array('data' => $device), 200);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'invoice_id' => 'required',
            'id' => 'required',
            'hours_value' => 'required',
            'netto' => 'required',
            'vat' => 'required',
            'gross' => 'required',
        ]);

        $invoiceContractor = InvoiceContractor::updateOrCreate([
            'invoice_id' => $request->invoice_id,
            'user_id' => $request->id,
        ], [
            'hours_value' => $request->hours_value,
            'netto' => $request->netto,
            'vat' => $request->vat,
            'gross' => $request->gross,
        ]);

        return response()->json(array('data' => $invoiceContractor->id), 200);
    }

    public function destroy(Request $request, Device $device)
    {
        $this->validate($request, [
            'id' => 'required'
        ]);
        $device->delete();

        return response()->json(null, 204);
    }

}
