<?php

use Dompdf\Dompdf;
use Dompdf\Options;


class Domtopdf extends Dompdf {


  public function renderPdf($html, $filename = null, $paper = null, $orientation = null) {

    if (!@exists($filename)) $filename = 'file';
    if (!@exists($paper)) $paper = 'letter';
    if (!@exists($orientation)) $orientation = 'landscape';

    $options = new Options();
    $options->setIsRemoteEnabled(true);

    $this->setOptions($options);
    $this->loadHtml($html);
    $this->setPaper($paper, $orientation);
    $this->render();
  }


  public function createPdfString($html, $filename = null, $paper = null, $orientation = null) {

    $this->renderPdf($html, $filename, $paper, $orientation);

    return $this->output();
  }


  public function createPdfAttachment($html, $filename = null, $paper = null, $orientation = null) {

    $this->renderPdf($html, $filename, $paper, $orientation);

    $attachment['file_string'] = $this->output();
    $attachment['name'] = $filename . '.pdf';

    return $attachment;
  }


  public function downloadPdf($html, $filename = null, $paper = null, $orientation = null) {

    $this->renderPdf($html, $filename, $paper, $orientation);

    $this->stream($filename . '.pdf');
  }

}
?>