<?php

namespace App\Http\Controllers\Admin\Import;

use Flash;
use Response;
use Illuminate\Support\Facades\Auth;
use Request;
use App\Http\Controllers\AppBaseController;
use App\Imports\TermsImport;
use App\Models\Definitions\Taxonomy;
use App\Models\Definitions\TermLang;
use Maatwebsite\Excel\Facades\Excel;

class TermsController extends AppBaseController
{
	/** @var  PartnerRepository */
	private $partnerRepository;

	public function __construct()
	{
		$this->middleware('auth');
	}

	/**
	 * Display a listing of the Service.
	 *
	 * @param ServiceDataTable $serviceDataTable
	 * @return Response
	 */
	public function index()
	{
		$taxonomies = Taxonomy::where('key', '!=', 'service_nested_categories')->where('key', '!=', 'service_nested_location')->get();

		foreach ($taxonomies as $taxonomy) {

			echo '<a href="' . route('import/terms') . '?id=' . $taxonomy->id . '">' . $taxonomy->langs[0]->name . '</a><br/>';
		}

		if (isset($_GET['id'])) {

			$id = (int) $_GET['id'];

			$taxonomy = Taxonomy::find($id);

			$fileName = 'import_taxonomy_' . $taxonomy->key . '.xlsx';

			echo '<hr/>';
			echo 'Importing: ' . $taxonomy->key . '<br/><br/>';

			$filePath = 'public/imports/' . $fileName;

			if (\Illuminate\Support\Facades\Storage::disk('local')->exists($filePath)) {
				Excel::import(new TermsImport($id), $filePath, 'local');
			} else {
				echo ' File Not Found!';
			}
		}
	}

	public function arabic()
	{
		$lang_id_en = 1;
		$lang_id_ar = 4;

		$items = \Illuminate\Support\Facades\DB::table('term_lang_arabic')->get();

		foreach ($items as $arabic) {
			$english = $arabic->english;

			$terms = TermLang::where(['name' => $english, 'lang_id' => $lang_id_en])->get();

			if ($terms) {
				foreach ($terms as $term) {
					$arabicTerms = TermLang::where(['term_id' => $term->term_id, 'lang_id' => $lang_id_ar])->exists();

					if (!$arabicTerms) {
						echo $term->term_id . ' = ' . $english . ' - ' . $arabic->name . '<br/>';

						$model = new TermLang;
						$model->term_id = $term->term_id;
						$model->lang_id = $lang_id_ar;
						$model->slug = $term->slug;
						$model->name = $arabic->name;
						$model->deleted = $term->deleted;
						$model->save();
					}
				}
			}
		}
	}
}
