<?php

namespace App\Services\Pdf;

use setasign\Fpdi\TcpdfFpdi;

/**
 * TCPDF, unlike FPDF, prints its own automatic header/footer and appends a
 * "Powered by TCPDF" credit link to the last page by default. This subclass
 * disables both so the engine swap stays invisible in the final output.
 */
class SealingPdfEngine extends TcpdfFpdi
{
    public function __construct()
    {
        parent::__construct();

        $this->setPrintHeader(false);
        $this->setPrintFooter(false);
        $this->tcpdflink = false;
    }
}
