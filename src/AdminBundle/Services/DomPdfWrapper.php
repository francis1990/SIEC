<?php
/**
 * Created by PhpStorm.
 * User: Mildrey
 * Date: 2/14/16
 * Time: 6:28 PM
 */

namespace AdminBundle\Services;

use Symfony\Component\Filesystem\Exception\FileNotFoundException;

class DomPdfWrapper{

    private $pdf;

    public function __construct($root_dir){
        $config_file = $root_dir.'dompdf_config.inc.php';
        if(!file_exists($config_file))
            throw new FileNotFoundException('Debe tener configurado el Bundle DomPdfBundle');

        require_once $config_file;

        $this->pdf = new \DOMPDF();
    }

    public function outputPDF($html, $filename, $orientation = "portrait", $size = "A4"){
        $this->pdf->set_paper($size, $orientation);
        $this->pdf->load_html($html);
        $this->pdf->render();

        $this->pdf->stream($filename);

        return $this->pdf->output();
    }

    public function getpdf($html)
    {
        $this->pdf->set_paper(DOMPDF_DEFAULT_PAPER_SIZE);
        $this->pdf->load_html($html);
        $this->pdf->render();
    }

    public function stream($filename)
    {
        $this->pdf->stream($filename);
    }

    /**
     * get the raw pdf output
     */
    public function output()
    {
        return $this->pdf->output();
    }
} 