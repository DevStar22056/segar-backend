<!-- Client & Supplier -->
<table style="border: 0;" border="0" cellpadding="0" cellspacing="0">
    <tr>
        <td width="40%" style="padding: 10px 0;">
            <strong class="margin-bottom-5">{{$invoice->text['seller']}}:</strong>
            <hr style="border-top: 0">
            <p style="font-size: 11px; line-height: 13px;">
                {{$invoice->vendor_name}}<br>
                {{$invoice->vendor_address}}<br>
                {{$invoice->text['vat']}}: {{$invoice->vendor_nip}}
            </p>
        </td>
        <td width="20%">&nbsp;</td>
        <td width="40%" style="padding: 10px 0; text-align: right">
            <strong>{{$invoice->text['buyer']}}:</strong>
            <hr style="border-top: 0">
            <p style="font-size: 11px; line-height: 13px;">
                {{$invoice->purchaser_name}} <br>
                {{$invoice->purchaser_address}}<br>
                {{$invoice->text['vat']}}: {{$invoice->purchaser_nip}}
            </p>
        </td>
    </tr>
</table>