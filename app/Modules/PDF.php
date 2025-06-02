<?php

namespace App\Modules;

use Illuminate\Contracts\Foundation\Application as FoundationApplication;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\Response;
use TCPDF;

class PDF
{
    public static function viewToPdf(View|Factory $view): string
    {
        $html = $view->render();
        $pdf = new TCPDF();
        $pdf->setCreator(PDF_CREATOR);
        $pdf->AddPage();
        $pdf->SetFont('amiri', '', 16);

        if (app()->getLocale() == 'ar') {
            $pdf->setRTL(true);
        }

        $pdf->writeHTML($html, false, false, true, false, app()->getLocale() == "ar" ? 'R' : 'L');

        return $pdf->Output('', 'S');
    }

    public static function pdfResponse(string $data): Application|Response|FoundationApplication|ResponseFactory
    {
        return response($data, 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="sample.pdf"')
            ->header('Content-Length', strlen($data));
    }
}
