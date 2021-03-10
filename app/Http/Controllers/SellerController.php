<?php

namespace App\Http\Controllers;

use App\Seller;
use Illuminate\Http\Request;

class SellerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $sellers = Seller::with(['BankAccounts', 'logo'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);
//            ->get();
        return response()->json($sellers, 200);
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
        $seller = new Seller();
        $seller->title = $request->title;
        $seller->nip = $request->nip;
        $seller->company_name = $request->company_name;
        $seller->street = $request->street;
        $seller->address1 = $request->address1;
        $seller->address2 = $request->address2;
        $seller->postal_code = $request->postal_code;
        $seller->city = $request->city;
        $seller->regon = $request->regon;

        $seller->bank_account = $request->bank_account;
        $seller->currency = $request->currency;

        // save
        $seller->save();

        return response()->json(["id" => $seller->id], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $seller = Seller::with(['BankAccounts', 'logo'])->find($id);
        return response()->json(['data' => $seller], 200);
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
     * @param Seller $seller
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function update(Request $request, $id)
    {
        // handle fields
        $seller = Seller::find($id);
        $seller->title = $request->title;
        $seller->nip = $request->nip;
        $seller->company_name = $request->company_name;
        $seller->street = $request->street;
        $seller->address1 = $request->address1;
        $seller->address2 = $request->address2;
        $seller->postal_code = $request->postal_code;
        $seller->city = $request->city;
        $seller->regon = $request->regon;

//        $seller->bank_account = $request->bank_account;
        $seller->currency = $request->currency;

        $seller->save();

        return response()->json(['status' => 'success'], 200);
    }

    /**
     * @param Seller $seller
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy(Seller $seller)
    {
        $user = JWTAuth::parseToken()->authenticate();
        if ($user->role === '1') {
            $seller->delete();
            return response()->json(array('data' => 'success'), 200);
        }
        return response()->json(null, 404);
    }
}
