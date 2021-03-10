<h1>{{$subject}}</h1>

<table border="4">
    <tr>
        <td colspan="2">dane z faktury</td>
    </tr>
    <tr>
        <td>description</td>
        <td>{{ $invoice->description }}</td>
    </tr>
    <tr>
        <td>hours_value</td>
        <td>{{ $invoice->hours_value }}</td>
    </tr>
    <tr>
        <td>wiadomosc</td>
        <td>{{$body}}</td>
    </tr>
</table>

