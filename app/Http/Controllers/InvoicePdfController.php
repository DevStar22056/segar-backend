<?php

namespace App\Http\Controllers;

use App\Http\Resources\InvoiceResource;
use App\Invoice;
use JWTAuth;
use PDF;
use Storage;

class InvoicePdfController extends Controller
{

    /**
     * @param $invoice
     * @param $request
     * @return InvoiceResource|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function render($invoice, $request)
    {
        /***/
        return $this->collectData($invoice, $request);
    }

    /**
     * @param $invoice
     * @param $request
     * @return InvoiceResource|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function collectData($invoice, $request)
    {

        $user = $invoice->user;
//        $user = ($request->header('Authorization')) ? : JWTAuth::parseToken()->authenticate() : null ;

        if ($request->type === "html") {

            return view('invoices/pdf', ['invoice' => $invoice]);

        } else if ($request->type === "pdf") {

            // file name and path
            $file_name = $this->generateFileName($invoice, $user);
            $file_path = 'public/pdf/invoices/' . $file_name . '.pdf';

            $invoice->title = $file_name;

            // generate pdf
            $pdf = PDF::loadView('invoices/pdf', ['invoice' => $invoice]);

            // save pdf to file
            Storage::put($file_path, $pdf->output());

            // update file path
            $_inv = Invoice::find($request->id);
            $_inv->update(['file' => $file_path]);

            return $pdf->download($file_name . '.pdf');

            /**
             * Download
             */
//            return $pdf->download($file_name . '.pdf');

        }

        return new InvoiceResource($invoice);

    }

    /**
     * @param $name
     * @return string
     */
    public function generateFileName($invoice)
    {

        $name = $invoice->internal_invoice_number;
        $name_for_user = $invoice->invoice_number;
        $is_correction = $invoice->is_correction;
        $user = $invoice->user;

        // clean string
        $name = preg_replace('/\s+/', '', $name);
        $name = str_replace('/', '_', $name);

        // path year and month ex. 2019/05/
//        $path = date('Y') . '/' . date('m') . '/';

        // admin invoice name
        if ($user->role === "1") {
            $name = "Faktura_" . date('d') . '_' . date('m') . '_' . date('Y') . '_' . $user->invoice_company_name;
        } else {
//            $name = $name_for_user . '_' . date('d') . '_' . date('m') . '_' . date('Y') . '_' . $user->invoice_company_name;
            $name = $name_for_user . '_' . $user->invoice_company_name;
        }


        // correction
        if ($is_correction) {
            $name = 'KOREKTA_' . $name;
        }

        return $name;
//        return $path . $name;
    }

}