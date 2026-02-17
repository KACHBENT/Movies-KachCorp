<?php

namespace App\Libraries;

use Dompdf\Dompdf;
use Dompdf\Options;

class PDF
{
    public static function load($html, $paper = 'A4', $orientation = 'portrait')
    {
        $options = new Options();
        $options->set('isRemoteEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper($paper, $orientation);
        $dompdf->render();

        return $dompdf;
    }
}
