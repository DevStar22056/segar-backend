@if(!empty($invoice))

@php

    $sum_netto = 0;
    $sum_vat = 0;
    $vat = (!$invoice->eu_vat) ? $invoice->user->vat_value : 0;
    $sum_gross = 0;

    $sum_netto_original = 0;
    $sum_vat_original = 0;
    $sum_gross_original = 0;

    $sum_netto = $invoice->hours_value_netto;
    $sum_netto += $invoice->fixed_price;
    $sum_netto += $invoice->overtime_value_netto;
    $sum_netto += $invoice->oncall_value_netto_10;
    $sum_netto += $invoice->oncall_value_netto_30;

    $sum_vat = $invoice->hours_value_vat;
    $sum_vat += $invoice->fixed_price_vat;
    $sum_vat += $invoice->overtime_value_vat;
    $sum_vat += $invoice->oncall_value_vat_10;
    $sum_vat += $invoice->oncall_value_vat_30;

    $sum_gross = $invoice->hours_value_gross;
    $sum_gross += $invoice->fixed_price_gross;
    $sum_gross += $invoice->overtime_value_gross;
    $sum_gross += $invoice->oncall_value_gross_10;
    $sum_gross += $invoice->oncall_value_gross_30;

    $oncall_10_rate = ($invoice->user->hourly_rate * 0.1) + $invoice->user->hourly_rate;
    $oncall_30_rate = ($invoice->user->hourly_rate * 0.3) + $invoice->user->hourly_rate;

    $curr_number = '';
    $curr_error = false;
    $invoice->issue_date_due = strtotime($invoice->issue_date . " +".$invoice->profile_due." days");

    // discount
    if($invoice->discount > 0) {
        $sum_netto -= $invoice->discount;
        $sum_vat -= $invoice->discount_vat;
        $sum_gross -= $invoice->discount_gross;
    }

    if($invoice->currency !== 'PLN'){
        $currency = $invoice->currency;
        $completion_date = date('Y-m-d', strtotime($invoice->completion_date . '-1 days'));
        $url = "http://api.nbp.pl/api/exchangerates/rates/a/" . $currency . "/" . $completion_date . "/?format=json";
        $json = @file_get_contents($url);

        if($json) {
            $curr_obj = json_decode($json);
            // curr val
            $curr_val = $curr_obj->rates[0]->mid;
            // curr numberŁ
            $curr_number = $curr_val . 'zł / 1 ' . $currency . ' ' . $curr_obj->rates[0]->no . ' / ' . $curr_obj->rates[0]->effectiveDate;
        } else {
            $curr_val = 1;
            $curr_error = true;
        }
    }

    // costs
    $costs = [
        "travel" => [
            "0" => [
                "cost_value" => 0,
                "cost_vat_only" => 0,
                "gross" => 0
            ],
            "0.23" => [
                "cost_value" => 0,
                "cost_vat_only" => 0,
                "gross" => 0
            ],
            "0.08" => [
                "cost_value" => 0,
                "cost_vat_only" => 0,
                "gross" => 0
            ],
            "Zwolnienie" => [
                "cost_value" => 0,
                "cost_vat_only" => 0,
                "gross" => 0
            ],
            "Nie podlega" => [
                "cost_value" => 0,
                "cost_vat_only" => 0,
                "gross" => 0
            ],
            "Nie dotyczy" => [
                "cost_value" => 0,
                "cost_vat_only" => 0,
                "gross" => 0
            ],
        ],
        "other" => [
             "0" => [
                "cost_value" => 0,
                "cost_vat_only" => 0,
                "gross" => 0
            ],
            "0.23" => [
                "cost_value" => 0,
                "cost_vat_only" => 0,
                "gross" => 0
            ],
            "0.08" => [
                "cost_value" => 0,
                "cost_vat_only" => 0,
                "gross" => 0
            ],
            "Zwolnienie" => [
                "cost_value" => 0,
                "cost_vat_only" => 0,
                "gross" => 0
            ],
            "Nie podlega" => [
                "cost_value" => 0,
                "cost_vat_only" => 0,
                "gross" => 0
            ],
            "Nie dotyczy" => [
                "cost_value" => 0,
                "cost_vat_only" => 0,
                "gross" => 0
            ],
        ]
    ];

    //costs
    $costs_orig = [
        "travel" => [
            "0" => [
                "cost_value" => 0,
                "cost_vat_only" => 0,
                "gross" => 0
            ],
            "0.23" => [
                "cost_value" => 0,
                "cost_vat_only" => 0,
                "gross" => 0
            ],
            "0.08" => [
                "cost_value" => 0,
                "cost_vat_only" => 0,
                "gross" => 0
            ],
            "Zwolnienie" => [
                "cost_value" => 0,
                "cost_vat_only" => 0,
                "gross" => 0
            ],
            "Nie podlega" => [
                "cost_value" => 0,
                "cost_vat_only" => 0,
                "gross" => 0
            ],
            "Nie dotyczy" => [
                "cost_value" => 0,
                "cost_vat_only" => 0,
                "gross" => 0
            ],
        ],
        "other" => [
             "0" => [
                "cost_value" => 0,
                "cost_vat_only" => 0,
                "gross" => 0
            ],
            "0.23" => [
                "cost_value" => 0,
                "cost_vat_only" => 0,
                "gross" => 0
            ],
            "0.08" => [
                "cost_value" => 0,
                "cost_vat_only" => 0,
                "gross" => 0
            ],
            "Zwolnienie" => [
                "cost_value" => 0,
                "cost_vat_only" => 0,
                "gross" => 0
            ],
            "Nie podlega" => [
                "cost_value" => 0,
                "cost_vat_only" => 0,
                "gross" => 0
            ],
            "Nie dotyczy" => [
                "cost_value" => 0,
                "cost_vat_only" => 0,
                "gross" => 0
            ],
        ]
    ];

    foreach ($invoice->costs as $cost) {

        $costs[$cost['cost_type']][$cost['cost_vat']]['cost_value'] += $cost['cost_value'];
        $costs[$cost['cost_type']][$cost['cost_vat']]['cost_vat_only'] += $cost['cost_vat_only'];
        $costs[$cost['cost_type']][$cost['cost_vat']]['gross'] += $cost['cost_vat_value'];

        $sum_netto += $cost['cost_value'];
        $sum_vat += $cost['cost_vat_only'];
        $sum_gross += $cost['cost_vat_value'];
    }

    // invoice for project with contractors
    foreach ($invoice->invoice_contractors as $contractor) {
        $sum_netto += $contractor['netto'];
        $sum_vat += $contractor['vat'];
        $sum_gross += $contractor['gross'];
    }

    // correction
    if($invoice->is_correction) {
        $invoice_org = $invoice->original;

        $sum_netto_original = $invoice_org->hours_value_netto;
        $sum_netto_original += $invoice_org->fixed_price;
        $sum_netto_original += $invoice_org->overtime_value_netto;
        $sum_netto_original += $invoice_org->oncall_value_netto_10;
        $sum_netto_original += $invoice_org->oncall_value_netto_30;

        $sum_vat_original = $invoice_org->hours_value_vat;
        $sum_vat_original += $invoice_org->fixed_price_vat;
        $sum_vat_original += $invoice_org->overtime_value_vat;
        $sum_vat_original += $invoice_org->oncall_value_vat_10;
        $sum_vat_original += $invoice_org->oncall_value_vat_30;

        $sum_gross_original = $invoice_org->hours_value_gross;
        $sum_gross_original += $invoice_org->fixed_price_gross;
        $sum_gross_original += $invoice_org->overtime_value_gross;
        $sum_gross_original += $invoice_org->oncall_value_gross_10;
        $sum_gross_original += $invoice_org->oncall_value_gross_30;

        $oncall_10_rate_original = ($invoice_org->user->hourly_rate * 0.1) + $invoice_org->user->hourly_rate;
        $oncall_30_rate_original = ($invoice_org->user->hourly_rate * 0.3) + $invoice_org->user->hourly_rate;

        // calculate diff
        $sum_gross_dif = $sum_gross - $sum_gross_original;
        $sum_gross_dif_formated = number_format($sum_gross_dif, 2, ',', ' ');
        $sum_gross_dif_text = 0;
        if($sum_gross_dif < 0) $sum_gross_dif_text = 1;

        // numbers after , comma
        $n = explode(',', $sum_gross_dif_formated);
        $fraction = (count($n) > 1) ? $n[1] : 0;

        // costs

            foreach ($invoice->original->costs as $cost) {

                $costs_orig[$cost['cost_type']][$cost['cost_vat']]['cost_value'] += $cost['cost_value'];
                $costs_orig[$cost['cost_type']][$cost['cost_vat']]['cost_vat_only'] += $cost['cost_vat_only'];
                $costs_orig[$cost['cost_type']][$cost['cost_vat']]['gross'] += $cost['cost_vat_value'];

            }

    }


    if($invoice->eu_vat) {
        $sum_vat = 0;
        $sum_gross = $sum_netto;
    }
@endphp

        <!DOCTYPE html>
<html lang="pl-PL">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{$invoice->title}}</title>
    <style>
        html {
            text-transform: none;
            font-size: 10px;
            color: #000;
            font-family: "dejavu sans", sans-serif;
            /*font-family: 'Source Sans Pro', sans-serif;*/
        }

        .headerc {
            font-size: 20px;
        }

        strong {
            font-weight: 600;
            color: #333;
            display: inline-block;
        }

        body {
            /*background: #fff;*/
            color: #000;
            font-weight: 400;
            line-height: 12px;
            font-size: 10px !important;
            /*font-family: 'Source Sans Pro', sans-serif;*/
            /*font-family: "Arial", sans-serif;*/
            /*font-family: Helvetica, Arial, sans-serif;*/
            /*font-family: "dejavu sans", sans-serif;*/
        }

        #invoice {
            background: white;
            width: 100%;
        }

        p {
            margin: 0;
            padding-bottom: 20px;
            clear: both;
            font-size: 11px;
            color: #000;
        }

        #logo {
            text-align: right;
        }

        #logo img {
            max-height: 60px;
        }

        table {
            width: 100%;
            margin: 0 0 10px;
            padding: 0;
            border-spacing: 0;
            border: 1px solid #ddd;
            color: #000;

        }

        th,
        td {
            padding: 1px;
            font-size: 11px;
            /*border: 1px solid #ddd;*/
            text-align: left;
            vertical-align: top;
            color: #000;
        }

        .tab td {
            border: 1px solid #ccc;
            padding: 5px;
            vertical-align: middle;
            color: #000;
        }

        th {
            /*border-bottom: 1px solid #ddd;*/
            font-size: 11px;
            font-weight: bold;
            background: #3f51b5;
            color: #fff;
            padding: 2px;
            border: 1px solid #ffffff;
        }

        th:last-child,
        td:last-child {
            /*text-align: right;*/
        }

        td:first-child {
            text-align: left;
        }

        td {
            text-align: right;
            color: #000;
        }

        th span {
            position: relative;
            display: inline-block;
            color: #000;
            height: 100%;
        }

        th span::after {
            content: '';
            width: 100%;
            bottom: -16px;
            position: absolute;
            right: 0;
            border-bottom: 1px solid #2a41e8;
        }

    </style>
</head>
<body>

{{-- HEAD --}}
@include('invoices.parts.header', ['invoice' => $invoice])
{{-- HEAD --}}

{{-- DETAILS --}}
@include('invoices.parts.details', ['invoice' => $invoice])
{{-- DETAILS --}}

@if(!$invoice->is_correction)
    {{-- NORMAL TABLE --}}
    //@include('invoices.parts.table', ['invoice' => $invoice, 'costs' =>$costs, 'is_original' => false, 'is_correction' => false])
    {{-- NORMAL TABLE --}}

    {{-- SUMMARY --}}
    @include('invoices.parts.summary', ['invoice' => $invoice])
    {{-- SUMMARY --}}
@else
    {{-- ORIGINAL --}}
    @php
        $invoice_corr = $invoice;
        $invoice_temp = $invoice->original;
        $invoice_temp_text = $invoice->text;
        $invoice = $invoice_temp;
        $invoice->text = $invoice_temp_text;
    @endphp
    @include('invoices.parts.table', ['invoice' => $invoice, 'costs' => $costs_orig, 'is_original' => true, 'is_correction' => false])
    {{-- ORIGINAL --}}

    {{-- CORRECTION --}}
    @php
        $invoice = $invoice_corr;
        $invoice->text = $invoice_temp_text;
    @endphp
    @include('invoices.parts.table', ['invoice' => $invoice, 'costs' =>$costs, 'is_original' => false, 'is_correction' => true])
    {{-- CORRECTION --}}

    {{-- SUMMARY --}}
    @include('invoices.parts.summary', ['invoice' => $invoice, 'curr_error' => $curr_error, 'completion_date' => $invoice->completion_date])
    {{-- SUMMARY --}}

    {{--  CORRECTION SUMMARY  --}}
    @include('invoices.parts.correction_summary', ['invoice' => $invoice, 'invoice_original' => $invoice_temp])
    {{--  CORRECTION SUMMARY  --}}

@endif


@if(!empty($invoice->correction_description))
    <table style="font-size: 11px; margin-top: 50px;" border="0" cellpadding="0" cellspacing="0">
        <tr>
            <th style="padding: 5px; text-align: left;">{{$invoice->text['correction_description']}}</th>
        </tr>
        <tr>
            <td style="padding: 5px; text-align: left;">
                {{$invoice->correction_description}}
            </td>
        </tr>
    </table>
@endif

@if(!empty($invoice->remarks))
    <table style="font-size: 11px; margin-top: 50px;" border="0" cellpadding="0" cellspacing="0">
        <tr>
            <th style="padding: 5px; text-align: left;">{{$invoice->text['remarks']}}</th>
        </tr>
        <tr>
            <td style="padding: 5px; text-align: left;">
                {{$invoice->remarks}}
            </td>
        </tr>
    </table>
@endif


</body>
</html>
@endif
