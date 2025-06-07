<?php
namespace App\Controllers\Erp;
use App\Controllers\BaseController;

require_once APPPATH . 'ThirdParty/dompdf/autoload.inc.php';
use Dompdf\Dompdf;

class Pdf extends BaseController {

	public function index() 
	{
        return view('erp/dompdf/employee_profile');
    }

    function htmlToPDF(){
        $dompdf = new Dompdf(); 
		$options = $dompdf->getOptions();
		$options->setIsRemoteEnabled(true);
		$options->setDefaultFont('Courier');
		$dompdf->setOptions($options);
        $dompdf->loadHtml(view('erp/dompdf/employee_profile'));
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $dompdf->stream();
		//$domPdf->output();
    }
}
