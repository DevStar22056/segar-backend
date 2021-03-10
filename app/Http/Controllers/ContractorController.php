<?php

namespace App\Http\Controllers;

use App\Contractor;
use Illuminate\Http\Request;
use JWTAuth;
use App\Agreeement;
use App\BankAccount;

class ContractorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $contractors = Contractor::with(['BankAccounts', 'Contacts', 'Agreements'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);
//            ->get();
        return response()->json($contractors, 200);
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
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $contractor = new Contractor();
        $contractor->nip = $request->nip;
        $contractor->company_name = $request->company_name;
        $contractor->street = $request->street;
        $contractor->address1 = $request->address1;
        $contractor->address2 = $request->address2;
        $contractor->postal_code = $request->postal_code;
        $contractor->city = $request->city;
        $contractor->country = $request->country;
        $contractor->regon = $request->regon;

        $contractor->bank_account = $request->bank_account;
        $contractor->currency = $request->currency;
        $contractor->account_manager = $request->account_manager;

        $contractor->shipping_type = $request->shipping_type;
        $contractor->shipping_email = $request->shipping_email;
        $contractor->shipping_post = $request->shipping_post;
        $contractor->is_b2b = $request->is_b2b;
        $contractor->is_uop = $request->is_uop;
        $contractor->is_margin = $request->is_margin;
        $contractor->is_inne = $request->is_inne;
        $contractor->terms_uop = $request->terms_uop;
        $contractor->terms_currency_type = $request->terms_currency_type;
        $contractor->terms_payment_deadline = $request->terms_payment_deadline;
        $contractor->invoicing_type = $request->invoicing_type;
        $contractor->invoicing_invoice = $request->invoicing_invoice;
        $contractor->invoicing_process = $request->invoicing_process;

        // save
        $contractor->save();

        return response()->json(["id" => $contractor->id], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $contractor = Contractor::with(['BankAccounts', 'Contacts', 'Agreements'])->find($id);
        return response()->json(['data' => $contractor], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * @param Request $request
     * @param $id
     * @param Contractor $contractor
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function update(Request $request, $id)
    {
        // handle fields
        $contractor = Contractor::find($id);
        $contractor->nip = $request->nip;
        $contractor->company_name = $request->company_name;
        $contractor->street = $request->street;
        $contractor->address1 = $request->address1;
        $contractor->address2 = $request->address2;
        $contractor->postal_code = $request->postal_code;
        $contractor->city = $request->city;
        $contractor->country = $request->country;
        $contractor->regon = $request->regon;

//        $contractor->bank_account = $request->bank_account;
        $contractor->currency = $request->currency;
        $contractor->account_manager = $request->account_manager;

        $contractor->shipping_type = $request->shipping_type;
        $contractor->shipping_email = $request->shipping_email;
        $contractor->shipping_post = $request->shipping_post;
        $contractor->is_b2b = $request->is_b2b;
        $contractor->is_uop = $request->is_uop;
        $contractor->is_margin = $request->is_margin;
        $contractor->is_inne = $request->is_inne;
        $contractor->terms_uop = $request->terms_uop;
        $contractor->terms_currency_type = $request->terms_currency_type;
        $contractor->terms_payment_deadline = $request->terms_payment_deadline;
        $contractor->invoicing_type = $request->invoicing_type;
        $contractor->invoicing_invoice = $request->invoicing_invoice;
        $contractor->invoicing_process = $request->invoicing_process;

        $contractor->save();

        return response()->json(['status' => 'success'], 200);
    }

    /**
     * @param Contractor $contractor
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy(Contractor $contractor)
    {
        $user = JWTAuth::parseToken()->authenticate();
        if ($user->role === '1') {
            // Agreeement::where('resource_id', $contractor->id)->delete();
            // BankAccount::where('resource_id', $contractor->id)->delete();
            $contractor->delete();
            return response()->json(array('data' => 'success'), 200);
        }
        return response()->json(null, 404);
    }

    /**
     * @param Request $request
     * @param Change $change
     * @return \Illuminate\Http\JsonResponse
     */
    public function contractorChangeRequestDelete(Request $request, Change $change)
    {
        Change::where('id', $request->id)->delete();
        return response()->json(null, 204);
    }
}
