<!-- Header -->
<table style="border: 0;" border="0" cellpadding="0" cellspacing="0">
    <tr>
        <td width="50%">
            <table style="border: 0; width: 350px" border="0" cellpadding="0" cellspacing="0">
                <tr>
                    <td><strong>{{$invoice->text['internal_invoice_number']}}:</strong></td>
                    <td>{{$invoice->internal_invoice_number}}</td>
                </tr>
                <tr>
                    <td><strong>{{$invoice->text['issue_date']}}:</strong></td>
                    <td>{{date('Y-m-d', strtotime($invoice->issue_date))}}</td>
                </tr>
                <tr>
                    <td><strong>{{$invoice->text['issue_date_due']}}:</strong></td>
                    {{-- + termin platnosci z panelu usera --}}
                    <td>{{date('Y-m-d', $invoice->issue_date_due)}}</td>
                </tr>
                <tr>
                    <td><strong>{{$invoice->text['completion_date']}}:</strong></td>
                    <td>{{date('Y-m-d', strtotime($invoice->completion_date))}}</td>
                </tr>
                @if($invoice->is_correction)
                    <tr>
                        <td><strong>{{$invoice->text['correction_number']}}:</strong></td>
                        <td>{{$invoice->original->invoice_number}}</td>
                    </tr>
                @endif
            </table>
        </td>
        <td>
            <div id="logo"><img src="{{ public_path() . '/images/logo.png' }}" alt="SEARGIN LOGO"></div>
        </td>
    </tr>
    <tr>
        <td colspan="2" style="text-align: center;">
            <h1 class="headerc">
                <strong>{{$invoice->text['invoice_number']}}</strong> - {{$invoice->invoice_number}}
            </h1>
        </td>
    </tr>
</table>
