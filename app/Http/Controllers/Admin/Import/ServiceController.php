<?php

namespace App\Http\Controllers\Admin\Import;

use Flash;
use Response;
use Illuminate\Support\Facades\Auth;
use Request;
use App\Http\Controllers\AppBaseController;
use App\Imports\LocationTermImport;
use App\Imports\ServiceImport;
use Maatwebsite\Excel\Facades\Excel;

class ServiceController extends AppBaseController
{
	public function __construct()
	{
		$this->middleware('auth');
	}

	public function index()
	{
		$time = time();

		if (!isset($_GET['type'])) {
			echo 'Need Type';
		}

		$type = $_GET['type'];

		if ($type == 'active') {
			$fileName = 'import_services.xlsx';
		} else {
			if (!isset($_GET['index'])) {
				//				echo 'Need Index';
				$index = 1;
			} else {
				$index = $_GET['index'];
			}

			$fileIndex = $index;
			$fileName = 'services_expired/files/' . $fileIndex . '.xlsx';
		}

		Excel::import(new ServiceImport, 'public/imports/' . $fileName, 'local');

		if ($type == 'expired') {
			$nextIndex = ($fileIndex + 1);

			if ($nextIndex < 20) {
				$nextLocation = '/admin/import/services?type=' . $type . '&index=' . $nextIndex;
				echo '<br/> <a href="' . $nextLocation . '">Next : ' . $nextIndex . '</a>';

				echo '<script type="text/javascript">
			setTimeout(function(){
				window.location.href = "' . $nextLocation . '";
			}, 5000);
		</script>';
			}
		}

		echo '<br/><br/> Time:' . (time() - $time) . ' sec';
	}
}
