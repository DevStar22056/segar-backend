<table>
    <tr>
        <th style="padding: 5px;" colspan="2">{{$invoice->text['corr_summary']}}</th>
    </tr>
    <tr>
        <td style="padding: 5px;" width="200"> {{$invoice->text['before_corr']}}</td>
        <td style="padding: 5px; text-align: left">
            {{number_format($sum_gross_original, 2, ',', ' ')}} {{$invoice->currency}}
        </td>
    </tr>
    <tr>
        <td style="padding: 5px;" width="200">{{$invoice->text['after_corr']}}</td>
        <td style="padding: 5px; text-align: left">
            {{number_format($sum_gross, 2, ',', ' ')}}
            {{$invoice->currency}}
        </td>
    </tr>
    <tr>
        <td style="padding: 5px;" width="200"><strong>{{$invoice->text['corr_options'][$sum_gross_dif_text]}}</strong>
        </td>
        <td style="padding: 5px; text-align: left"><strong>{{number_format($sum_gross_dif, 2, ',', ' ')}}</strong> {{$invoice->currency}}</td>
    </tr>
    <tr>
        <td style="padding: 5px;" width="200">{{$invoice->text['in_words']}}</td>
        <td style="padding: 5px; text-align: left">{{App\Invoice::numberToText($sum_gross_dif)}} {{$invoice->currency}} {{$fraction}}
            /100
        </td>
    </tr>
    <tr>
        <td style="padding: 5px;" width="200">{{$invoice->text['invoice_bank_no']}}</td>
        <td style="padding: 5px; text-align: left">{{$invoice->user->invoice_bank_no}}</td>
    </tr>
    <tr>
        <td style="padding: 5px;" width="200">{{$invoice->text['correction_description']}}</td>
        <td style="padding: 5px; text-align: left;">
            {{$invoice->correction_description}}
        </td>
    </tr>
</table>
