<?php

namespace App\Http\Controllers;

use App\ExternalPersona;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExternalPersonaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $personas = ExternalPersona::all();
        return response()->json(['data' => $personas], 200);
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
        //
        $persona = new ExternalPersona();
        $persona->id = $this->setLastUUID();
        $persona->email = $request->email;
        $persona->name = $request->name;
        $persona->surname = $request->surname;
        $persona->user_phone = $request->user_phone;
        $persona->country = $request->country;
        $persona->user_street = $request->user_street;
        $persona->user_postal_code = $request->user_postal_code;
        $persona->user_city = $request->user_city;
        $persona->invoice_company_name = $request->invoice_company_name;
        $persona->company_nip = $request->company_nip;
        $persona->description = $request->description;

        $persona->save();

        $persona->id = $this->getLastUUID()->id;
        return response()->json(['data' => $persona], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $persona = ExternalPersona::findOrFail($id);
        $input = $request->all();
        $persona->fill($input)->save();
        return response()->json(['data' => $persona], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $persona = ExternalPersona::findOrFail($id);
        $persona->delete();
        return response()->json(['data' => 'success'], 204);
    }

    public function setLastUUID()
    {
        $user_id = DB::Table('users')->latest('id')->first()->id;
        $persona_id = $this->getLastUUID();
        $persona_id = $persona_id ? $persona_id->id : 1;
        return $user_id + $persona_id;
    }

    public function getLastUUID()
    {
        return DB::Table('external_personas')->orderBy('created_at', 'DESC')->first();
    }
}
