<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{

    protected $fillable = [
        'creator',
        'user_id',
        'approval',
        'issue_date',
        'completion_date',
        'description',
        'correction_id',
        'correction_description',
        'invoice_number',
        'hours_value',
        'hours_value_netto',
        'hours_value_gross',
        'hours_value_vat',
        'fixed_price',
        'fixed_price_gross',
        'fixed_price_vat',
        'overtime_value',
        'overtime_value_netto',
        'overtime_value_gross',
        'overtime_value_vat',
        'oncall_value_10',
        'oncall_value_netto_10',
        'oncall_value_gross_10',
        'oncall_value_vat_10',
        'oncall_value_30',
        'oncall_value_netto_30',
        'oncall_value_gross_30',
        'oncall_value_vat_30',
        'remarks',
        'internal_invoice_number',
        'is_correction',
        'invoice_type',
        'invoice_type_id',
        'rejection_type',
        'rejection_description',
        'is_accepted',
        'status',
        'payment_date',
        'discount',
        'discount_description',
        'discount_gross',
        'discount_vat',
        'eu_vat',
        'currency',
        'currency_value',
        'file',
        'language',
        'vendor',
        'purchaser',
        'vendor_name',
        'vendor_nip',
        'vendor_address',
        'purchaser_name',
        'purchaser_nip',
        'purchaser_address'
    ];
    private $id;

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function costs()
    {
        return $this->hasMany(InvoiceCost::class, 'invoice_id');
    }

    public function files()
    {
        return $this
            ->hasMany(Fileupload::class, 'source_id')
            ->where('source', '=', 1);
    }

    public function invoice_contractors()
    {
        return $this->hasMany(InvoiceContractor::class, 'invoice_id');
    }

    public function corrections()
    {
        return $this->hasMany(Invoice::class, 'correction_id');
    }

    public static function numberToText($liczba)
    {

        $separator = ' ';
        $jednosci = array('', ' jeden', ' dwa', ' trzy', ' cztery', ' pi????', ' sze????', ' siedem', ' osiem', ' dziewi????');
        $nascie = array('', ' jedena??cie', ' dwana??cie', ' trzyna??cie', ' czterna??cie', ' pi??tna??cie', ' szesna??cie', ' siedemna??cie', ' osiemna??cie', ' dziewietna??cie');
        $dziesiatki = array('', ' dziesie??', ' dwadzie??cia', ' trzydzie??ci', ' czterdzie??ci', ' pi????dziesi??t', ' sze????dziesi??t', ' siedemdziesi??t', ' osiemdziesi??t', ' dziewi????dziesi??t');
        $setki = array('', ' sto', ' dwie??cie', ' trzysta', ' czterysta', ' pi????set', ' sze????set', ' siedemset', ' osiemset', ' dziewi????set');
        $grupy = array(
            array('', '', ''),
            array(' tysi??c', ' tysi??ce', ' tysi??cy'),
            array(' milion', ' miliony', ' milion??w'),
            array(' miliard', ' miliardy', ' miliard??w'),
            array(' bilion', ' biliony', ' bilion??w'),
            array(' biliard', ' biliardy', ' biliard??w'),
            array(' trylion', ' tryliony', ' trylion??w')
        );

        $wynik = '';
        $znak = '';
        if ($liczba == 0)
            return 'zero';
        if ($liczba < 0) {
            $znak = 'minus';
            $liczba = -$liczba;
        }
        $g = 0;
        while ($liczba > 0) {


            $s = floor(($liczba % 1000) / 100);
            $n = 0;
            $d = floor(($liczba % 100) / 10);
            $j = floor($liczba % 10);


            if ($d == 1 && $j > 0) {
                $n = $j;
                $d = $j = 0;
            }

            $k = 2;
            if ($j == 1 && $s + $d + $n == 0)
                $k = 0;
            if ($j == 2 || $j == 3 || $j == 4)
                $k = 1;

            if ($s + $d + $n + $j > 0)
                $wynik = $setki[$s] . $dziesiatki[$d] . $nascie[$n] . $jednosci[$j] . $grupy[$g][$k] . $wynik;

            $g++;
            $liczba = floor($liczba / 1000);
        }
        return trim($znak . $wynik);

    }
}
