<?php

namespace App\Http\Controllers;

use App\Http\Resources\InvoiceResource;
use App\Invoice;
use App\Mail\ClientInvoiceMail;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use JWTAuth;
use PDF;

class InvoiceController extends Controller
{

    /**
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        $user = JWTAuth::parseToken()->authenticate();
        $invoices = Invoice::with(['user', 'costs', 'files', 'corrections'])
            ->where('creator', $user->id)
            // ->where('is_correction', NULL)
            ->orderBy('created_at', 'desc')
            ->paginate(10);
//            ->get();

        $invoices->getCollection()->transform(function ($value) {

            // from:to
            $vendor = User::find($value->vendor);
            $purchaser = User::find($value->purchaser);

            if ($vendor) {
                $value->vendor_extended = [
                    "name" => $vendor->name,
                    "surname" => $vendor->surname
                ];
            }

            if ($purchaser) {
                $value->purchaser_extended = [
                    "name" => $purchaser->name,
                    "surname" => $purchaser->surname
                ];
            }

            return $value;
        });

        return response()->json($invoices, 200);
    }

    /**
     * @param Invoice $invoice
     * @return InvoiceResource
     */
    public function show(Invoice $invoice, Request $request)
    {

        $invoice = Invoice::with(['costs', 'user', 'files', 'invoice_contractors', 'corrections'])->find($request->id);

        // check if invoice is correction
        if ($invoice->is_correction === 1) {
            // invoice for correct id
            if (!empty($invoice->correction_id)) {
                $id = $invoice->correction_id;
                $invoice->original = Invoice::with(['costs', 'user', 'files', 'invoice_contractors', 'corrections'])->find($id);
                $invoice->is_correction = true;
                $invoice->correction_invoice_id = $request->id;
            }
        } else {
            $invoice->is_correction = false;
        }

        // user
        $payment_dues = [
            0 => ($invoice->user->first_payment_number !== NULL) ? $invoice->user->first_payment_number : 0,
            1 => ($invoice->user->second_payment_number !== NULL) ? $invoice->user->second_payment_number : 0,
            2 => ($invoice->user->third_payment_number !== NULL) ? $invoice->user->third_payment_number : 0,
            3 => ($invoice->user->other_payment_number !== NULL) ? $invoice->user->other_payment_number : 0,
        ];

        // get invoices count - for payment due calc
        $invoices_sum = Invoice::where('user_id', $invoice->user->id)
            ->where('is_correction', NULL)
            ->count();

        if ($invoices_sum < 0) {
            $invoices_sum = $invoices_sum;
        } elseif ($invoices_sum > 2) {
            $invoices_sum = 3;
        }

        // pass info about payment due
        $invoice->profile_due = $payment_dues[$invoices_sum];

        // pass translations
        if (isset($request->lang)) {
            $lang = $request->lang;
        } else {
            $lang = ($invoice->user->language) ? $invoice->user->language : 'pl_PL';
        }

        // langs
        $invoice->text = $this->dictionary($lang);

        switch ($invoice->correction_description) {
            case '0':
                $invoice->correction_description = 'Błędna stawka';
                break;
            case '1':
                $invoice->correction_description = 'Błędna ilość godzin';
                break;
            case '2':
                $invoice->correction_description = 'Błędna wartość netto';
                break;
        }

        if ($request->type === "html" || $request->type === "pdf") {

            // ivoice from:to
            $vendor = User::find($invoice->vendor);
            $purchaser = User::find($invoice->purchaser);

            $invoice->vendor = $vendor;
            $invoice->purchaser = $purchaser;
        } else {
            $vendor = User::find($invoice->vendor);
            $purchaser = User::find($invoice->purchaser);

            $invoice->vendor_extended = $vendor;
            $invoice->purchaser_extended = $purchaser;
        }

        $pdfInvoice = new InvoicePdfController();

        return $pdfInvoice->render($invoice, $request);
    }

    /**
     * @param Request $request
     * @param Invoice $invoice
     * @return InvoiceResource
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request, Invoice $invoice)
    {
        
        $this->validate($request, [
            'id' => 'required',
            'invoice_number' => 'unique:invoices'
        ]);

        // handle fields
        $request_array = $request->toArray();

        // update invoice number month based on completion date month
        if (array_key_exists('completion_date', $request_array)) {

            $date_string = $request_array['completion_date'] . ' 00:00:00';

            if ($invoice->completion_date !== $date_string) {

                $completion_month = explode('-', $request_array['completion_date'])[1];
                $completion_year = explode('-', $request_array['completion_date'])[0];

                // recheck order
                $is_correction = ($invoice->is_correction === NULL) ? false : true;
                $rechekced_order_number = $this->invoiceNumberParser($completion_year, $completion_month, $is_correction);

                $internal_invoice_number_arr = explode('/', $invoice['internal_invoice_number']);
                $internal_invoice_number_arr[0] = $rechekced_order_number;
                $internal_invoice_number_arr[1] = $completion_month;

                // create new number
                $request_array['internal_invoice_number'] = implode('/', $internal_invoice_number_arr);
            }
        }


        $invoice->update($request_array);

        // handle expenses update
        $this->handleExpensesChange($request, $invoice);

        return response()->json(['status' => 'success'], 200);
    }

    /**
     * @param $request
     * @param $invoice
     */
    public function handleExpensesChange($request, $invoice)
    {

        if (!empty($request->travels) || !empty($request->others)) {
            //  remove all related costs
            DB::table('invoice_costs')->where('invoice_id', '=', $invoice->id)->delete();
        }

        $this->handleInvoiceExpenses($request, $invoice);
    }

    /**
     * @param $request
     * @param $invoice
     */
    public function handleInvoiceExpenses($request, $invoice)
    {
        $travels = (empty($request->travels)) ? [] : $request->travels;
        $others = (empty($request->others)) ? [] : $request->others;
        $expenses = array_merge($travels, $others);
        if (!empty($expenses)) {
            foreach ($expenses as $expense) {
                $request = new Request;
                $request['cost_value'] = $expense['value'];
                $request['cost_vat'] = $expense['vat'];
                $request['cost_vat_value'] = $expense['gross'];
                $request['cost_vat_only'] = $expense['cost_vat_only'];
                $request['cost_description'] = $expense['description'];
                $request['cost_files'] = 'files ID';
                $request['invoice_id'] = $invoice->id;
                $request['user_id'] = $invoice->user_id;
                $request['cost_type'] = $expense['type'];
                app(InvoiceCostController::class)->store($request);
            }
        }
    }

    /**
     * @param Invoice $invoice
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy(Invoice $invoice)
    {

        $user = JWTAuth::parseToken()->authenticate();
        if ($user->role === '1') {
            $invoice->delete();
            return response()->json(array('data' => 'success'), 200);
        }
        return response()->json(null, 404);
    }

    /**
     * @param Invoice $invoice
     */
    public
    function single(Invoice $invoice)
    {
        // return single invoice
    }

    /**
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function getAllInvoices()
    {
        $user = JWTAuth::parseToken()->authenticate();
        $invoices = Invoice::with(['user', 'corrections'])
            ->where('creator', '!=', $user->id)
//            ->where('is_correction', NULL)
            ->where('status', '!=', 0)
            ->orderByDesc('created_at')
            ->paginate(10);
//            ->get();

        $invoices->getCollection()->transform(function ($value) {

            // from:to
            $vendor = User::find($value->vendor);
            $purchaser = User::find($value->purchaser);

            if ($vendor) {
                $value->vendor_extended = $vendor;
            }

            if ($purchaser) {
                $value->purchaser_extended = $purchaser;
            }

            return $value;
        });

        return response()->json($invoices, 200);
    }

    /**
     * @param Request $request
     * @return InvoiceResource
     */
    public function store(Request $request)
    {
        $invoice = new Invoice();
        $invoice->creator = $request->creator;
        $invoice->user_id = $request->user_id;
        $invoice->payment_date = $request->payment_date;
        $invoice->approval = $request->approval;
        $invoice->issue_date = $request->issue_date;
        $invoice->completion_date = $request->completion_date;
        $invoice->description = $request->description;
        $invoice->remarks = $request->remarks;
        $invoice->invoice_number = $request->invoice_number;
        $invoice->hours_value = $request->hours_value;
        $invoice->hours_value_netto = $request->hours_value_netto;
        $invoice->hours_value_gross = $request->hours_value_gross;
        $invoice->hours_value_vat = $request->hours_value_vat;
        $invoice->fixed_price = $request->fixed_price;
        $invoice->fixed_price_gross = $request->fixed_price_gross;
        $invoice->fixed_price_vat = $request->fixed_price_vat;
        $invoice->overtime_value = $request->overtime_value;
        $invoice->overtime_value_netto = $request->overtime_value_netto;
        $invoice->overtime_value_gross = $request->overtime_value_gross;
        $invoice->overtime_value_vat = $request->overtime_value_vat;
        $invoice->oncall_value_10 = $request->oncall_value_10;
        $invoice->oncall_value_netto_10 = $request->oncall_value_netto_10;
        $invoice->oncall_value_gross_10 = $request->oncall_value_gross_10;
        $invoice->oncall_value_vat_10 = $request->oncall_value_vat_10;
        $invoice->oncall_value_30 = $request->oncall_value_30;
        $invoice->oncall_value_netto_30 = $request->oncall_value_netto_30;
        $invoice->oncall_value_gross_30 = $request->oncall_value_gross_30;
        $invoice->oncall_value_vat_30 = $request->oncall_value_vat_30;
        $invoice->internal_invoice_number = $request->internal_invoice_number;
        $invoice->rejection_type = $request->rejection_type;
        $invoice->rejection_description = $request->rejection_description;
        $invoice->invoice_type = $request->invoice_type;
        $invoice->invoice_type_id = $request->invoice_type_id;
        $invoice->status = $request->status;
        $invoice->vendor_name = "test";

        //        $invoice->language = $request->language;
        $invoice->save();

        // invoice number
        //        $new_number = $invoice->id . '/' . date('m') . '/' . date('Y') . '/SEARGIN';
        //        $invoice->update(['internal_invoice_number' => $new_number]);

        // extra invoice expenses
        $this->handleInvoiceExpenses($request, $invoice);

        return new InvoiceResource([$invoice->id]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public
    function storeDraft(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();

        $date = new \DateTime('now');
        $last_day = new \DateTime('now');
        $last_day = $last_day->modify('last day of this month');

        $invoice = new Invoice();
        $invoice->creator = $request->creator;
        $invoice->vendor = $request->user_id;
        $invoice->user_id = $request->user_id;
        $invoice->status = 0;
        $invoice->issue_date = $date;
        $invoice->completion_date = $last_day->format('Y-m-d');
        $invoice->payment_date = $last_day->format('Y-m-d');
        $invoice->currency = $this->getUserCurrency($user);

        $invoice->language = $user->language;


        if ($user->role != 1) {
            $invoice->vendor_name = $user->invoice_company_name;
            $invoice->vendor_address = $user->company_postal_code . ' ' . $user->company_city . ', ' . $user->company_street;
            $invoice->vendor_nip = $user->company_nip;
            $invoice->purchaser_name = 'Seargin Sp. z o.o.';
            $invoice->purchaser_address = '80-266 Gdańsk, Al. Grunwaldzka 163';
            $invoice->purchaser_nip = '5833165868';
        }

        $invoice->save();


        $month = date('m');
        $year = date('Y');

        // invoice number
        $invoice_unique_number = $this->invoiceNumberParser($year, $month);
        $new_number = $invoice_unique_number . '/' . $month . '/' . $year . '/SEARGIN/' . 'ID' . $user->id;
        $invoice->update(['internal_invoice_number' => $new_number]);

        return response()->json(["id" => $invoice->id], 201);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function storeCorrection(Request $request)
    {
        // set date
        $date = new \DateTime('now');
        $last_day = new \DateTime('now');
        $last_day = $last_day->modify('last day of this month');

        // set invoice
        $invoice = new Invoice();
        $invoice->creator = $request->creator;
        $invoice->user_id = $request->user_id;
        $invoice->status = 0;
        $invoice->issue_date = $date;
        $invoice->completion_date = $last_day->format('Y-m-d');
        $invoice->payment_date = $last_day->format('Y-m-d');
        $invoice->currency = 'PLN';
        $invoice->correction_id = $request->correction_id;
        $invoice->is_correction = 1;
        $invoice->save();

        $month = date('m');
        $year = date('Y');

        // invoice number
        $invoice_unique_number = $this->invoiceNumberParser($year, $month, true);
        $new_number = 'KOR/' . $invoice_unique_number . '/' . $month . '/' . $year . '/SEARGIN';
        $invoice->update(['internal_invoice_number' => $new_number]);

        return response()->json(["id" => $invoice->id], 201);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllClients(Request $request)
    {
        $clients = DB::connection('searger')
            ->table('clients')
            //            ->rightJoin('addresses', 'clients.address_id', '=', 'addresses.id')
            //            ->rightJoin('cities', 'addresses.city_id', '=', 'cities.id')
            //            ->rightJoin('cities_translations', 'cities.id', '=', 'cities_translations.city_id')
            //            ->take(10)
            ->get();
        return response()->json(array('data' => $clients));
    }


    /**
     * @param $lang
     * @return mixed
     */
    public function dictionary($lang)
    {
        $dictionary = [
            'pl_PL' => [
                'lang' => 'pl',
                'internal_invoice_number' => 'Numer faktury wew.',
                'invoice_number' => 'Numer faktury',
                'issue_date' => 'Data wystawienia',
                'issue_date_due' => 'Data płatności',
                'completion_date' => 'Data sprzedaży',
                'seller' => 'Sprzedawca',
                'vat' => 'Numer VAT',
                'vat1' => 'VAT',
                'vat2' => 'Stawka VAT',
                'buyer' => 'Nabywca',
                'remarks' => 'Uwagi',
                'summary' => 'RAZEM',
                'gross' => 'Wartość brutto',
                'netto' => 'Wartość netto',
                'currency' => 'Waluta',
                'currency_val' => 'Kurs',
                'invoice_bank_no' => 'Konto bankowe',
                'bank_name' => 'Nazwa banku',
                'payment_info' => 'Informacje o płatności',
                'vat_value' => 'Wartość VAT',
                'nd' => 'Nie dotyczy',
                'np' => 'Nie podlega',
                'zw' => 'Zwolnienie',
                'others' => 'Inne',
                'travels' => 'Podróże',
                'hours' => 'godziny',
                'psc' => 'szt',
                'overtime' => 'Nadgodziny',
                'unit' => 'Jednostka',
                'qty' => 'Ilość',
                'rate' => 'Stawka',
                'eu_vat' => 'Odwrotne obciążenie',
                'const' => 'Stała',
                'desc' => 'Opis',
                'correction_description' => 'Przyczyna korekty',
                'correction_number' => 'Numer faktury korygującej',
                'curr_error' => 'Błąd - nie można pobrać wartości waluty dla daty',
                'before_corr' => 'Wartość faktury przed',
                'after_corr' => 'Wartość faktury po korekcie',
                'corr_summary' => 'Podsumowanie korekty',
                'corr_options' => [
                    0 => "Do dopłaty",
                    1 => "Do zwrotu"
                ],
                'in_words' => 'Słownie',
            ],
            'en_GB' => [
                'lang' => 'en',
                'currency_val' => 'Exchange rate',
                'np' => 'Is not subject to',
                'zw' => 'Discharge',
                'internal_invoice_number' => 'Internal number',
                'invoice_number' => 'Invoice number',
                'issue_date' => 'Issue date',
                'issue_date_due' => 'Due date',
                'completion_date' => 'Completion date',
                'seller' => 'Vendor',
                'vat' => 'VAT number',
                'vat1' => 'VAT',
                'vat2' => 'VAT rate',
                'buyer' => 'Contractors',
                'remarks' => 'Remarks',
                'summary' => 'SUMMARY',
                'gross' => 'Gross',
                'netto' => 'Netto',
                'eu_vat' => 'Reverse charge',
                'currency' => 'Currency',
                'invoice_bank_no' => 'Bank account',
                'bank_name' => 'Bank name',
                'payment_info' => 'Payment details',
                'vat_value' => 'VAT rate',
                'nd' => 'Not applicable',
                'others' => 'Others',
                'travels' => 'Travels',
                'hours' => 'hours',
                'psc' => 'psc',
                'const' => 'Constant',
                'overtime' => 'Overtime',
                'unit' => 'Unit',
                'qty' => 'Qty',
                'rate' => 'rate',
                'desc' => 'Description',
                'corr_summary' => 'corr_summary',
                'before_corr' => 'before_corr ',
                'after_corr' => 'after_corr ',
                'corr_options' => 'corr_options ',
                'in_words' => 'in_words ',
                'correction_description' => 'Correction description',
                'correction_number' => 'Number of original invoice',
                'curr_error' => 'Error - unable to get currency value for date'
            ]
        ];

        return $dictionary[$lang];
    }

    /**
     * @param $user
     * @return mixed
     */
    public function getUserCurrency($user)
    {
        $field = User::find($user->id)->invoice_payment_currency;
        return $field;
    }


    /**
     * Handle and parse invoice number based on requirements
     */
    public function invoiceNumberParser($year, $month, $is_correction = false)
    {

        if ($is_correction) {
            $invoices = DB::table('invoices')->where('is_correction', '!=', NULL)->orderBy('internal_invoice_number', 'asc')->get();
        } else {
            $invoices = DB::table('invoices')->where('is_correction', '=', NULL)->orderBy('internal_invoice_number', 'asc')->get();
        }


        $tree = [];
        foreach ($invoices as $item) {
            if (!empty($item->internal_invoice_number)) {
                $item_arr = explode('/', $item->internal_invoice_number);
                if ($is_correction) {
                    $tree[$item_arr[3]][$item_arr[2]][$item_arr[1]] = $item_arr[4];
                } else {
                    $tree[$item_arr[2]][$item_arr[1]][$item_arr[0]] = $item_arr[4];
                }
            }
        }

        $target_peroid = [];
        // peroid we're looking for
        if (key_exists($year, $tree)) {
            if (key_exists($month, $tree[$year])) {
                $target_peroid = $tree[$year][$month];
            }
        }

        if ($target_peroid) {
            // sort array
            ksort($target_peroid);
            $last_key = array_key_last($target_peroid);
            return $last_key + 1;
        }

        // nothing found earlier, return 1 as first item in this peroid (year, month)
        return 1;
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendEmail(Request $request)
    {

        // email
        $invoice = Invoice::findOrFail($request->id);

        try {
            Mail::to($request->to)->send(new ClientInvoiceMail($invoice, $request->subject, $request->body));
            return response()->json(array('data' => 'success'), 200);
        } catch (Exception $ex) {
            return response()->json(array('data' => 'fail'), 404);
        }
    }
}
