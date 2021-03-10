<?php

namespace App\Http\Controllers;

use GusApi\Exception\InvalidUserKeyException;
use GusApi\Exception\NotFoundException;
use GusApi\GusApi;
use GusApi\ReportTypes;
use Illuminate\Http\Request;

class GusController extends Controller
{

    public function index(Request $request)
    {
        $gus = new GusApi(env('GUS_KEY'));

        try {
            $nipToCheck = $request->nip; //change to valid nip value
            $gus->login();

            $gusReports = $gus->getByNip($nipToCheck);

            $report = [];
            foreach ($gusReports as $gusReport) {
                $reportType = ReportTypes::REPORT_ACTIVITY_PHYSIC_PERSON;
                $report['invoice_company_name'] = $gusReport->getName();
                $report['company_street'] = $gusReport->getStreet() . ' ' . $gusReport->getPropertyNumber() . ' ' . $gusReport->getApartmentNumber();
                $report['company_city'] = $gusReport->getCity();
                $report['company_postal_code'] = $gusReport->getZipCode();
                $fullReport = $gus->getFullReport($gusReport, $reportType);

                $report['fullReport'][] = $fullReport;
                foreach (ReportTypes::REPORTS as $REPORT) {
                    $fullReport2 = $gus->getFullReport($gusReport, $REPORT);
                    $report['fullReport']['REPORTS'][] = $fullReport2;
                }

                foreach (ReportTypes::REGON_9_REPORTS as $REPORT) {
                    $fullReport2 = $gus->getFullReport($gusReport, $REPORT);
                    $report['fullReport']['REGON_9_REPORTS'][] = $fullReport2;
                }
            }

            $report['vat_value'] = $this->checkVAT($nipToCheck);

            return response()->json(array('data' => $report), 200);

        } catch (InvalidUserKeyException $e) {
            return response()->json(array('data' => 'error'), 404);

        } catch (NotFoundException $e) {
            return response()->json(array('data' => 'error', 'message' => $gus->getResultSearchMessage()), 404);
        }
    }

    /**
     * @param $nip
     * @return mixed|string
     */
    public function checkVAT($nip)
    {
        $soapUrl = "https://sprawdz-status-vat.mf.gov.pl/";

        $xml_post_string = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ns="http://www.mf.gov.pl/uslugiBiznesowe/uslugiDomenowe/AP/WeryfikacjaVAT/2018/03/01"><soapenv:Header/><soapenv:Body><ns:NIP>' . $nip . '</ns:NIP></soapenv:Body></soapenv:Envelope>';

        $headers = array(
            "Content-Type: text/xml; charset=utf-8",
            "SOAPAction: http://www.mf.gov.pl/uslugiBiznesowe/uslugiDomenowe/AP/WeryfikacjaVAT/2018/03/01/WeryfikacjaVAT/SprawdzNIP"
        );

        $url = $soapUrl;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_post_string);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $response = curl_exec($ch);
        curl_close($ch);

        $response = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $response);
        $xml = new \SimpleXMLElement($response);
        $json = json_encode($xml);
        $array = json_decode($json, TRUE);
        $res = '';

        /**
         *
         * KODY
         *
         * https://finanse-arch.mf.gov.pl/c/document_library/get_file?uuid=fba25e1b-68dc-4f59-8193-323046002134&groupId=766655
         *
         */
        if (array_key_exists('WynikOperacji', $array['sBody'])) {
            if ($array['sBody']['WynikOperacji']['Kod'] === "C") {
                $res = '23';
            } elseif ($array['sBody']['WynikOperacji']['Kod'] === "Z") {
                $res = 'zw';
            }
            return $res;
        } else {
            $array = '';
        }
        return $array;
    }
}
