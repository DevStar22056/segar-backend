<table style="font-size: 11px; border: 0; margin-top: 30px;" border="0" cellpadding="0" cellspacing="0">
    <tr>
        <td style="border: 0; padding: 10px 0;" width="40%">
            <table>
                <tr>
                    <td style="padding: 10px">
                        <strong>{{$invoice->text['payment_info']}}</strong>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 0 10px 5px 10px;">
                        <strong>{{$invoice->text['bank_name']}}</strong> {{$invoice->user->bank_name}} <br>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 0 10px 5px 10px;">
                        <strong>{{$invoice->text['invoice_bank_no']}}</strong> {{$invoice->user->invoice_bank_no}}
                    </td>
                </tr>
                <tr>
                    <td style="padding: 0 10px 5px 10px;">
                        <strong>IBAN:</strong> {{$invoice->user->bank_iban}}
                    </td>
                </tr>
                <tr>
                    <td style="padding: 0 10px 5px 10px;">
                        <strong>SWIFT:</strong> {{$invoice->user->bank_swift_bic}}
                    </td>
                </tr>
                <tr>
                    <td style="padding: 0 10px 10px 10px;">
                        <strong>Waluta:</strong> {{$invoice->currency}}
                    </td>
                </tr>
                @if($invoice->eu_vat)
                    <tr>
                        <td style="padding:10px 5px;">
                            <strong>{{$invoice->text['eu_vat']}}</strong>
                        </td>
                    </tr>
                @endif
                @if($invoice->currency !== 'PLN')
                    <tr>
                        <td style="padding: 10px;">
                            <table border="0" style="border: 0; margin: 0">
                                <tr>
                                    <td>
                                        <strong>{{$invoice->text['currency']}}:</strong> {{$invoice->currency}}
                                    </td>
                                </tr>
                                @if($curr_error)
                                    <tr>
                                        <td style="padding: 10px 0">
                                            {{$invoice->text['curr_error']}}
                                            : {{date('Y-m-d', strtotime($completion_date))}}
                                        </td>
                                    </tr>
                                @endif
                                <tr>
                                    <td style="padding: 10px 0">
                                        <strong>{{$invoice->text['currency_val']}}:</strong> <br> {{$curr_number}}
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>{{----}}
                @endif
            </table>
        </td>
        <td width="20%">&nbsp;</td>
        <td style="border: 0; padding: 10px 0;" width="40%">
            <table style="font-size: 11px; margin: 0;" border="0" cellpadding="0" cellspacing="0">
                <tr>
                    <th style="padding: 5px; text-align: right;">-</th>
                    <th style="padding: 5px; text-align: right;">{{$invoice->text['netto']}}:</th>
                    <th style="padding: 5px; text-align: right;">{{$invoice->text['vat_value']}}:</th>
                    <th style="padding: 5px; text-align: right;">{{$invoice->text['gross']}}:</th>
                </tr>
                <tr>
                    <td style="padding: 5px;">{{$invoice->text['summary']}}</td>
                    <td style="padding: 5px; text-align: right;">{{number_format($sum_netto, 2, ',', ' ')}} {{$invoice->currency}}</td>
                    <td style="padding: 5px; text-align: right;">{{number_format($sum_vat, 2, ',', ' ')}} {{$invoice->currency}}</td>
                    <td style="padding: 5px; text-align: right; font-weight: bold;">{{number_format($sum_gross, 2, ',', ' ')}} {{$invoice->currency}}</td>
                </tr>
            </table>
            @if($invoice->currency !== 'PLN')
                <br>
                <hr>
                <br>
                <table style="font-size: 11px;" border="0" cellpadding="0" cellspacing="0">
                    <tr>
                        <td style="padding:10px 5px;" colspan="4"><strong>Po
                                przeliczeniu: {{$invoice->currency}}
                                / PLN </strong></td>
                    </tr>
                    <tr>
                        <th style="padding: 5px; text-align: right;">-</th>
                        <th style="padding: 5px; text-align: right;">{{$invoice->text['netto']}}:</th>
                        <th style="padding: 5px; text-align: right;">{{$invoice->text['vat_value']}}:</th>
                        <th style="padding: 5px; text-align: right;">{{$invoice->text['gross']}}:</th>
                    </tr>
                    <tr>
                        <td style="padding: 5px;">{{$invoice->text['summary']}}</td>
                        <td style="padding: 5px; text-align: right; font-weight: bold;">{{number_format($sum_netto * $curr_val, 2, ',', ' ')}}
                            PLN
                        </td>
                        <td style="padding: 5px; text-align: right; font-weight: bold;">{{number_format($sum_vat * $curr_val, 2, ',', ' ')}}
                            PLN
                        </td>
                        <td style="padding: 5px; text-align: right; font-weight: bold;">{{number_format($sum_gross * $curr_val, 2, ',', ' ')}}
                            PLN
                        </td>
                    </tr>
                </table>
            @endif
        </td>
    </tr>
</table>