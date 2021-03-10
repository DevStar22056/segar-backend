<!-- Invoice -->
<table class="tab" style="font-size: 11px; margin-bottom: 50px" border="0" cellpadding="0" cellspacing="0">
    <tr>
        <th style="padding: 10px 5px;">{{$invoice->text['desc']}}</th>
        <th style="padding: 10px 5px;">{{$invoice->text['rate']}}</th>
        <th style="padding: 10px 5px;">{{$invoice->text['qty']}}</th>
        <th style="padding: 10px 5px;">{{$invoice->text['unit']}}</th>
        <th style="padding: 10px 5px;">Netto</th>
        <th style="padding: 10px 5px;">{{$invoice->text['vat2']}}</th>
        <th style="padding: 10px 5px;">{{$invoice->text['vat1']}}</th>
        <th style="padding: 10px 5px;">{{$invoice->text['gross']}}</th>
    </tr>

    @if($is_correction === true)
        <tr>
            <td colspan="8" style="text-align:center;font-weight:bold">
                Po korekcie
            </td>
        </tr>
    @endif

    @if($is_original === true)
        <tr>
            <td colspan="8" style="text-align:center;font-weight:bold">
                Przed korektą
            </td>
        </tr>
    @endif

    @if(count($invoice->invoice_contractors) !== 0)
        @foreach($invoice->invoice_contractors as $contractor)
            <tr>
                <td>Kontraktor [ID: {{$contractor->user_id}}]</td>
                <td></td>
                <td>{{number_format($contractor->hours_value, 2, ',', ' ')}}</td>
                <td>{{$invoice->text['hours']}}</td>
                <td>{{number_format($contractor->netto, 2, ',', ' ')}}</td>
                @if($invoice->eu_vat)
                    <td>0 %</td>
                    <td>0,00</td>
                    <td>{{number_format($contractor->netto, 2, ',', ' ')}}</td>
                @else
                    <td>23 %</td>
                    <td>{{number_format($contractor->vat, 2, ',', ' ')}}</td>
                    <td>{{number_format($contractor->gross, 2, ',', ' ')}}</td>
                @endif
            </tr>
        @endforeach
    @endif

    @if($invoice->hours_value)
        <tr>
            <td>{{$invoice->description}}</td>
            <td>{{number_format($invoice->user->hourly_rate, 2, ',', ' ')}}</td>
            <td>{{number_format($invoice->hours_value, 2, ',', ' ')}}</td>
            <td>{{$invoice->text['hours']}}</td>
            <td>{{number_format($invoice->hours_value_netto, 2, ',', ' ')}}</td>
            @if($invoice->eu_vat)
                <td>0 %</td>
                <td>0,00</td>
                <td>{{number_format($invoice->hours_value_netto, 2, ',', ' ')}}</td>
            @else
                <td>{{$vat}}%</td>
                <td>{{number_format($invoice->hours_value_vat, 2, ',', ' ')}}</td>
                <td>{{number_format($invoice->hours_value_gross, 2, ',', ' ')}}</td>
            @endif
        </tr>
    @endif

    @if($invoice->fixed_price)
        <tr>
            <td>{{$invoice->description}}</td>
            <td>-</td>
            <td>-</td>
            <td>{{$invoice->text['const']}}</td>
            <td>{{number_format($invoice->fixed_price, 2, ',', ' ')}}</td>
            @if($invoice->eu_vat)
                <td>0 %</td>
                <td>0,00</td>
                <td>{{number_format($invoice->fixed_price, 2, ',', ' ')}}</td>
            @else
                <td>{{$vat}}%</td>
                <td>{{number_format($invoice->fixed_price_vat, 2, ',', ' ')}}</td>
                <td>{{number_format($invoice->fixed_price_gross, 2, ',', ' ')}}</td>
            @endif
        </tr>
    @endif

    @if($invoice->overtime_value_netto > 0)
        <tr>
            <td>{{$invoice->text['overtime']}}</td>
            <td>{{number_format($invoice->user->overtime_rate, 2, ',', ' ')}}</td>
            <td>{{number_format($invoice->overtime_value, 2, ',', ' ')}}</td>
            <td>{{$invoice->text['hours']}}</td>
            <td>{{number_format($invoice->overtime_value_netto, 2, ',', ' ')}}</td>
            @if($invoice->eu_vat)
                <td>0 %</td>
                <td>0,00</td>
                <td>{{number_format($invoice->overtime_value_netto, 2, ',', ' ')}}</td>
            @else
                <td>{{$vat}}%</td>
                <td>{{number_format($invoice->overtime_value_vat, 2, ',', ' ')}}</td>
                <td>{{number_format($invoice->overtime_value_gross, 2, ',', ' ')}}</td>
            @endif
        </tr>
    @endif
    @if($invoice->discount > 0)
        <tr>
            <td>Rabat: {{$invoice->discount_description}}</td>
            <td></td>
            <td>1,00</td>
            <td>{{$invoice->text['psc']}}.</td>
            <td>-{{number_format($invoice->discount, 2, ',', ' ')}}</td>

            @if($invoice->eu_vat)
                <td>0 %</td>
                <td>0,00</td>
                <td>-{{$invoice->discount}}</td>
            @else
                <td>{{$vat}}%</td>
                <td>-{{$invoice->discount_vat}}</td>
                <td>-{{$invoice->discount_gross}}</td>
            @endif

        </tr>
    @endif
    @if($invoice->oncall_value_netto_10 > 0)
        <tr>
            <td>On-call 10%</td>
            <td>{{number_format($oncall_10_rate, 2, ',', ' ')}}</td>
            <td>{{number_format($invoice->oncall_value_10, 2, ',', ' ')}}</td>
            <td>{{$invoice->text['hours']}}</td>
            <td>{{number_format($invoice->oncall_value_netto_10, 2, ',', ' ')}}</td>

            @if($invoice->eu_vat)
                <td>0 %</td>
                <td>0,00</td>
                <td>{{number_format($invoice->oncall_value_netto_10, 2, ',', ' ')}}</td>
            @else
                <td>{{$vat}}%</td>
                <td>{{number_format($invoice->oncall_value_vat_10, 2, ',', ' ')}}</td>
                <td>{{number_format($invoice->oncall_value_gross_10, 2, ',', ' ')}}</td>
            @endif
        </tr>
    @endif
    @if($invoice->oncall_value_netto_30 > 0)
        <tr>
            <td>On-call 30%</td>
            <td>{{number_format($oncall_30_rate, 2, ',', ' ')}}</td>
            <td>{{number_format($invoice->oncall_value_30, 2, ',', ' ')}}</td>
            <td>{{$invoice->text['hours']}}</td>
            <td>{{number_format($invoice->oncall_value_netto_30, 2, ',', ' ')}}</td>

            @if($invoice->eu_vat)
                <td>0%</td>
                <td>0</td>
                <td>{{number_format($invoice->oncall_value_netto_30, 2, ',', ' ')}}</td>
            @else
                <td>{{$vat}}%</td>
                <td>{{number_format($invoice->oncall_value_vat_30, 2, ',', ' ')}}</td>
                <td>{{number_format($invoice->oncall_value_gross_30, 2, ',', ' ')}}</td>
            @endif
        </tr>
    @endif
    {{--  TRAVELS  --}}
    @if($costs['travel']['0']['cost_value'] != 0)
        <tr>
            <td>{{$invoice->text['travels']}}</td>
            <td></td>
            <td></td>
            <td></td>
            <td>{{number_format($costs['travel']['0']['cost_value'], 2, ',', ' ')}}</td>
            <td>0 %</td>
            <td>0,00</td>
            <td>{{number_format($costs['travel']['0']['gross'], 2, ',', ' ')}}</td>
        </tr>
    @endif
    @if($costs['travel']['0.23']['cost_value'] != 0)
        <tr>
            <td>{{$invoice->text['travels']}}</td>
            <td></td>
            <td></td>
            <td></td>
            <td>{{number_format($costs['travel']['0.23']['cost_value'], 2, ',', ' ')}}</td>
            <td>23 %</td>
            <td>{{number_format($costs['travel']['0.23']['cost_vat_only'], 2, ',', ' ')}}</td>
            <td>{{number_format($costs['travel']['0.23']['gross'], 2, ',', ' ')}}</td>
        </tr>
    @endif
    @if($costs['travel']['0.08']['cost_value'] != 0)
        <tr>
            <td>{{$invoice->text['travels']}}</td>
            <td></td>
            <td></td>
            <td></td>
            <td>{{number_format($costs['travel']['0.08']['cost_value'], 2, ',', ' ')}}</td>
            <td>8 %</td>
            <td>{{number_format($costs['travel']['0.08']['cost_vat_only'], 2, ',', ' ')}}</td>
            <td>{{number_format($costs['travel']['0.08']['gross'], 2, ',', ' ')}}</td>
        </tr>
    @endif
    @if($costs['travel']['Zwolnienie']['cost_value'] != 0)
        <tr>
            <td>{{$invoice->text['travels']}}</td>
            <td></td>
            <td></td>
            <td></td>
            <td>{{number_format($costs['travel']['Zwolnienie']['cost_value'], 2, ',', ' ')}}</td>
            <td>{{$invoice->text['zw']}}</td>
            <td>{{number_format($costs['travel']['Zwolnienie']['cost_vat_only'], 2, ',', ' ')}}</td>
            <td>{{number_format($costs['travel']['Zwolnienie']['gross'], 2, ',', ' ')}}</td>
        </tr>
    @endif
    @if($costs['travel']['Nie podlega']['cost_value'] != 0)
        <tr>
            <td>{$invoice->text['travels']}}</td>
            <td></td>
            <td></td>
            <td></td>
            <td>{{number_format($costs['travel']['Nie podlega']['cost_value'], 2, ',', ' ')}}</td>
            <td>{{$invoice->text['np']}}</td>
            <td>{{number_format($costs['travel']['Nie podlega']['cost_vat_only'], 2, ',', ' ')}}</td>
            <td>{{number_format($costs['travel']['Nie podlega']['gross'], 2, ',', ' ')}}</td>
        </tr>
    @endif
    @if($costs['travel']['Nie dotyczy']['cost_value'] != 0)
        <tr>
            <td>{{$invoice->text['travels']}}</td>
            <td></td>
            <td></td>
            <td></td>
            <td>{{number_format($costs['travel']['Nie dotyczy']['cost_value'], 2, ',', ' ')}}</td>
            <td>{{$invoice->text['np']}}</td>
            <td>{{number_format($costs['travel']['Nie dotyczy']['cost_vat_only'], 2, ',', ' ')}}</td>
            <td>{{number_format($costs['travel']['Nie dotyczy']['gross'], 2, ',', ' ')}}</td>
        </tr>
    @endif
    {{--  TRAVELS  --}}


    {{--  others  --}}
    @if($costs['other']['0']['cost_value'] != 0)
        <tr>
            <td>{{$invoice->text['others']}}</td>
            <td></td>
            <td></td>
            <td></td>
            <td>{{number_format($costs['other']['0']['cost_value'], 2, ',', ' ')}}</td>
            <td>0 %</td>
            <td>0,00</td>
            <td>{{number_format($costs['other']['0']['gross'], 2, ',', ' ')}}</td>
        </tr>
    @endif
    @if($costs['other']['0.23']['cost_value'] != 0)
        <tr>
            <td>{{$invoice->text['others']}}</td>
            <td></td>
            <td></td>
            <td></td>
            <td>{{number_format($costs['other']['0.23']['cost_value'], 2, ',', ' ')}}</td>
            <td>23 %</td>
            <td>{{number_format($costs['other']['0.23']['cost_vat_only'], 2, ',', ' ')}}</td>
            <td>{{number_format($costs['other']['0.23']['gross'], 2, ',', ' ')}}</td>
        </tr>
    @endif
    @if($costs['other']['0.08']['cost_value'] != 0)
        <tr>
            <td>{{$invoice->text['others']}}</td>
            <td></td>
            <td></td>
            <td></td>
            <td>{{number_format($costs['other']['0.08']['cost_value'], 2, ',', ' ')}}</td>
            <td>8 %</td>
            <td>{{number_format($costs['other']['0.08']['cost_vat_only'], 2, ',', ' ')}}</td>
            <td>{{number_format($costs['other']['0.08']['gross'], 2, ',', ' ')}}</td>
        </tr>
    @endif
    @if($costs['other']['Zwolnienie']['cost_value'] != 0)
        <tr>
            <td>{{$invoice->text['others']}}</td>
            <td></td>
            <td></td>
            <td></td>
            <td>{{number_format($costs['other']['Zwolnienie']['cost_value'], 2, ',', ' ')}}</td>
            <td>{{$invoice->text['zw']}}</td>
            <td>{{number_format($costs['other']['Zwolnienie']['cost_vat_only'], 2, ',', ' ')}}</td>
            <td>{{number_format($costs['other']['Zwolnienie']['gross'], 2, ',', ' ')}}</td>
        </tr>
    @endif
    @if($costs['other']['Nie podlega']['cost_value'] != 0)
        <tr>
            <td>{{$invoice->text['others']}}</td>
            <td></td>
            <td></td>
            <td></td>
            <td>{{number_format($costs['other']['Nie podlega']['cost_value'], 2, ',', ' ')}}</td>
            <td>{{$invoice->text['np']}}</td>
            <td>{{number_format($costs['other']['Nie podlega']['cost_vat_only'], 2, ',', ' ')}}</td>
            <td>{{number_format($costs['other']['Nie podlega']['gross'], 2, ',', ' ')}}</td>
        </tr>
    @endif
    @if($costs['other']['Nie dotyczy']['cost_value'] != 0)
        <tr>
            <td>{{$invoice->text['others']}}</td>
            <td></td>
            <td></td>
            <td></td>
            <td>{{number_format($costs['other']['Nie dotyczy']['cost_value'], 2, ',', ' ')}}</td>
            <td>{{$invoice->text['nd']}}</td>
            <td>{{number_format($costs['other']['Nie dotyczy']['cost_vat_only'], 2, ',', ' ')}}</td>
            <td>{{number_format($costs['other']['Nie dotyczy']['gross'], 2, ',', ' ')}}</td>
        </tr>
    @endif
    {{--  others  --}}
</table>
