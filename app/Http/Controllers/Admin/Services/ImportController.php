<?php

namespace App\Http\Controllers\Admin\Services;

use Flash;
use Illuminate\Http\Request;
use Excel;
use App\Http\Controllers\AppBaseController;
use App\Repositories\Definitions\TermRepository;
use App\Repositories\Services\ServiceRepository;
use App\Repositories\Settings\PartnerRepository;
use App\Repositories\Services\ServiceContactRepository;
use App\Repositories\Services\ServiceHourRepository;
use App\Repositories\Services\ServiceTermRepository;
use App\Repositories\Services\ServiceCategoryRepository;
use App\Repositories\Services\LocationRepository;
use App\Repositories\Settings\LanguageRepository;
use App\Models\Definitions\Term;
use App\Imports\ServiceImport;

use Illuminate\Support\Facades\DB;

class ImportController extends AppBaseController
{
	/** @var  ServiceRepository */
	private $serviceRepository;

	/** @var  LanguageRepository */
	private $langRepository;

	/** @var  TermRepository */
	private $termRepository;

	/** @var  PartnerRepository */
	private $partnerRepository;

	/** @var  ServiceContactRepository */
	private $serviceContactRepository;

	/** @var  ServiceHourRepository */
	private $serviceHourRepository;

	/** @var  ServiceTermRepository */
	private $serviceTermRepository;

	/** @var  ServiceCategoryRepository */
	private $serviceCategoryRepository;

	/** @var ServiceImport */
	private $serviceImport;

	public function __construct(
		ServiceRepository $serviceRepo,
		LocationRepository $locationRepo,
		TermRepository $termRepo,
		PartnerRepository $partnerRepo,
		LanguageRepository $langRepo,
		ServiceContactRepository $serviceContactRepo,
		ServiceHourRepository $serviceHourRepo,
		ServiceTermRepository $serviceTermRepo,
		ServiceCategoryRepository $serviceCategoryRepo,
		ServiceImport $serviceImport
	) {
		$this->middleware('auth');
		$this->serviceRepository = $serviceRepo;
		$this->locationRepository = $locationRepo;
		$this->termRepository = $termRepo;
		$this->partnerRepository = $partnerRepo;
		$this->langRepository = $langRepo;
		$this->serviceContactRepository = $serviceContactRepo;
		$this->serviceHourRepository = $serviceHourRepo;
		$this->serviceTermRepository = $serviceTermRepo;
		$this->serviceCategoryRepository = $serviceCategoryRepo;
		$this->serviceImport = $serviceImport;
	}

	public function index()
	{
		return view('services.import.index');
	}

	public function downloadTemp(Request $request)
	{
		$fileName = 'services_import_template.xlsx';
		return \Illuminate\Support\Facades\Storage::disk('local')->download('public/templates/' . $fileName);
	}

	public function preview(Request $request)
	{
		$maximumImportCellCount = 1000;

		$this->validate($request, [
			'select_file'  => 'required|mimes:xls,xlsx'
		]);

		$path = $request->file('select_file')->getRealPath();

		$data = Excel::toArray([], $path, null, 'Xlsx');

		$items = $data[0];
		$items = array_filter($items);
		array_shift($items);

		$itemsCount = count($items);

		if ($itemsCount <= 0 || $itemsCount > $maximumImportCellCount) {
			if ($itemsCount == 0) Flash::error(__('app.Import file is empty! Sholud have at least one service item'));
			else
				Flash::error('Maksimum services count must be 1000.');

			return redirect('admin/services/import');
		}

		$headers = $this->serviceImport->headings();

		array_walk($items, function (&$row) {

			array_walk($row, function (&$cell, $key) {
				$cell = trim($cell);

				if ($key === 0 || $key === 1) {
					if (is_numeric($cell)) {
						$UNIX_DATE = ($cell - 25569) * 86400;
						$cell = gmdate("Y-m-d", $UNIX_DATE);
					}
				}

				if (strpos($cell, ';') !== false) {
					$cellValues = explode(';', $cell);
					$cellValues = array_filter($cellValues);

					array_walk($cellValues, function (&$cellValue) {
						$cellValue = ucfirst(trim($cellValue));
					});
					$cell = implode(';', $cellValues);
				}
			});
		});

		$category_terms = $this->repoTermToArray($this->termRepository->getCategoryTerms());
		$accessibility_terms = $this->repoTermToArray($this->termRepository->getByTaxonomy(1));
		$legal_documents_required = $this->repoTermToArray($this->termRepository->getByTaxonomy(16));
		$nationality = $this->repoTermToArray($this->termRepository->getByTaxonomy(3));
		$coverage_terms = $this->repoTermToArray($this->termRepository->getByTaxonomy(5));
		$referral_method_terms = $this->repoTermToArray($this->termRepository->getByTaxonomy(14));
		$referral_next_step_terms = $this->repoTermToArray($this->termRepository->getByTaxonomy(15));
		$response_delay_terms = $this->repoTermToArray($this->termRepository->getByTaxonomy(17));
		$feedback_mechanism_terms = $this->repoTermToArray($this->termRepository->getByTaxonomy(7));
		$feedback_delay_terms = $this->repoTermToArray($this->termRepository->getByTaxonomy(6));
		$complaints_mechanism_terms = $this->repoTermToArray($this->termRepository->getByTaxonomy(4));

		$intake_criteria_terms = $this->termRepository->getByTaxonomy(8);

		$genderAndAge = [];
		$otherIntakeCriteria = [];
		foreach ($intake_criteria_terms as $intake_criteria_term) {
			if (
				strpos(strtolower($intake_criteria_term->langs[0]->name), 'only') !== false ||
				strpos(strtolower($intake_criteria_term->langs[0]->name), 'sadece') !== false
			) {
				$genderAndAge[$intake_criteria_term->id] = trim(str_replace(["only", "Only", "sadece", "Sadece"], '', $intake_criteria_term->langs[0]->name));
			} else {
				$otherIntakeCriteria[$intake_criteria_term->id] = $intake_criteria_term->langs[0]->name;
			}
		}

		$location_terms = $this->termRepository->getLocationTerms();
		$partners = $this->partnerRepository->getPartnersForSelect();

		$inputValues = array(
			'accessibility_terms' => $accessibility_terms,
			'legal_documents_required' => $legal_documents_required,
			'nationality' => $nationality,
			'gender_and_age' => $genderAndAge,
			'intake_criteria_terms' => $otherIntakeCriteria,
			'locations' => $location_terms,
			'organizations' => $partners,
			'categories' => $category_terms,
			'coverage_terms' => $coverage_terms,
			'referral_method_terms' => $referral_method_terms,
			'referral_next_step_terms' => $referral_next_step_terms,
			'response_delay_terms' => $response_delay_terms,
			'feedback_mechanism_terms' => $feedback_mechanism_terms,
			'feedback_delay_terms' => $feedback_delay_terms,
			'complaints_mechanism_terms' => $complaints_mechanism_terms,
		);

		if (count($items) > 0) {
			return view('services.import.preview', array('headers' => $headers, 'items' => $items, 'inputValues' => $inputValues));
		}
	}

	public function process(Request $request)
	{
		$minCount = 1;
		$maximumImportCellCount = 1000;

		$requestItems = $request->all();

		$items = $requestItems;

		$totalCount = count($items);

		if ($totalCount < $minCount) return response()->json([
			'status' => 0,
			'message' => __('app.Service Data Empty'),
		]);
		if ($totalCount > $maximumImportCellCount) return response()->json([
			'status' => 0,
			'message' => __('app.Up to 1000 services can be transferred at once'),
		]);

		$inputs = [];
		foreach ($items as $item) {

			$start_date 		= $item['start_date'];
			$end_date 			= $item['end_date'];
			$partner_id 		= (int) $item['partner_id'];
			$city 				= (int) $item['city'];
			$district 			= (int) $item['district'];
			$location_name 		= $item['location_name'];
			$address 			= $item['address'];
			$latitude 			= $item['latitude'];
			$longitude 			= $item['longitude'];
			$terms 		  		= $item['terms'];
			$availability 		= $item['availability'];
			$locationID	  		= null;
			$langs 		  		= [];
			$serviceHours 		= [];
			$publish_date 		= date('Y-m-d', strtotime($start_date));
			$additional_details = $item['additional_details'];
			$comments 			= $item['comments'];
			$phone 				= $item['hotline_public_phone'];
			$more_info_link		= $item['more_info_link'];

			$location = $this->locationRepository->findOrCreate($city, $district, $latitude, $longitude, $location_name, $address);

			if ($location) {
				$locationID = $location->id;
			}

			$availabilityInfo = explode(';', $availability);
			$availabilityInfo = array_filter($availabilityInfo);
			if (!empty($availabilityInfo)) {
				foreach ($availabilityInfo as $avi) {

					list($dayName, $hourStart, $hourEnd) = explode('-', trim($avi));
					$dayName = strtolower(trim($dayName));

					switch ($dayName) {
						case 'sunday':
						case 'sun':
						case 'pazar':
							$dayIndex = 0;
							break;
						case 'monday':
						case 'mon':
						case 'pazartesi':
							$dayIndex = 1;
							break;
						case 'tuesday':
						case 'tue':
						case 'salı':
							$dayIndex = 2;
							break;
						case 'wednesday':
						case 'wed':
						case 'çarşamba':
							$dayIndex = 3;
							break;
						case 'thursday':
						case 'thu':
						case 'perşembe':
							$dayIndex = 4;
							break;
						case 'friday':
						case 'fri':
						case 'cuma':
							$dayIndex = 5;
							break;
						case 'saturday':
						case 'sat':
						case 'cumartesi':
							$dayIndex = 6;
							break;
						default:
							$dayIndex = -1;
							break;
					}

					$startHour = trim(str_replace(':', '', $hourStart));
					$endHour = trim(str_replace(':', '', $hourEnd));

					$startHour = ltrim($startHour, '0');
					$endHour = ltrim($endHour, '0');

					if ($dayIndex >= 0)
						$serviceHours[] = ['day' => $dayIndex, 'start_hour' => $startHour, 'end_hour' => $endHour];
					elseif ($dayName === 'weekdays' ||  $dayName == 'haftaiçi') {
						for ($dayIndex = 1; $dayIndex <= 5; $dayIndex++) {
							$serviceHours[] = ['day' => $dayIndex, 'start_hour' => $startHour, 'end_hour' => $endHour];
						}
					}
				}
			}

			$serviceHours = json_encode($serviceHours);

			foreach ($this->langRepository->all() as $lang) {
				$langs[$lang->id] = [
					"additional" => $additional_details,
					"comments" => $comments,
					"phone" => $phone,
					"link" => $more_info_link,
					"name" => null,
					"slug" => null,
				];
			}

			$inputs[] = [
				'start_date' => $start_date,
				'end_date' => $end_date,
				'partner_id' => $partner_id,
				'terms' => $terms,
				'location_city_id' => $city,
				'location_district_id' => $district,
				'location_id' => $locationID,
				'langs' => $langs,
				'publish_date' => $publish_date,
				'service_hours' => $serviceHours,
				'published' => true,
				'backendonly' => false,
				'is_remote' => false,
			];
		}

		if (!empty($inputs)) {
			DB::beginTransaction();
			try {
				foreach ($inputs as $input) {

					$service = $this->serviceRepository->create($input);

					if ($service) {
						$this->serviceRepository->saveLangs($input, $service, $this->termRepository);

						$allTermIds = $input['terms'];
						$termIds = [];
						foreach ($allTermIds as $allTermId) {
							if ((int) $allTermId > 0) {
								$termIds[] = (int) $allTermId;
								foreach (Term::getParents($allTermId) as $_tid) {
									$termIds[] = (int) $_tid;
								}
							}
						}

						//$this->serviceContactRepository->setServiceContacts($service->id, $input);
						$this->serviceTermRepository->setServiceTermIds($service->id, $termIds);
						$this->serviceCategoryRepository->setServiceCategoryTermIds($service->id, $termIds);
						$this->serviceHourRepository->updateOrInsert($service->id, $input);
					}
				}

				DB::commit();

				return response()->json([
					'status' => 1,
					'message' => count($inputs) . ' services imported',
				]);
			} catch (\Exception $e) {
				DB::rollback();

				return response()->json([
					'status' => 0,
					'message' => $e->getMessage(),
				]);
			}
		}
	}

	private function repoTermToArray($termRepository)
	{
		$bufferItems = [];
		foreach ($termRepository as $item) {

			$id = $item->id;
			$name = isset($item->name) ? $item->name : $item->langs[0]->name;

			$bufferItems[$id] = $name;
		}
		return $bufferItems;
	}

	public function data(Request $request)
	{

		$input = $request->all();
		if ($input['name'] == 'Region') {
			$location_terms = $this->termRepository->getLocationTerms();
		}
		return 1;
	}

	private function splitCsv($inputFile)
	{
		$this->cleanTempDir();

		$outputFiles = [];
		$outputFile = 'assets/data/tmp/output';

		$splitSize = 100;

		$in = fopen($inputFile, 'r');

		$rowCount = 0;
		$fileCount = 1;
		while (!feof($in)) {
			if (($rowCount % $splitSize) == 0) {
				if ($rowCount > 0) {
					fclose($out);
					$outputFiles[] = $outputFile . ($fileCount - 1) . '.csv';
				}
				$out = fopen($outputFile . $fileCount++ . '.csv', 'w');
			}
			$data = fgetcsv($in);
			if ($data) {
				fputcsv($out, $data);
			}
			$rowCount++;
		}

		fclose($out);
		if (($rowCount % $splitSize) != 0) {
			$outputFiles[] = $outputFile . ($fileCount - 1) . '.csv';
		}

		return $outputFiles;
	}

	private function getLineCount($file)
	{
		$linecount = 0;
		$handle = fopen($file, "r");
		while (!feof($handle)) {
			$line = fgets($handle, 4096);
			$linecount = $linecount + substr_count($line, PHP_EOL);
		}

		fclose($handle);

		return $linecount;
	}

	private function cleanTempDir()
	{
		$tempDir = 'assets/data/tmp';
		$files = glob($tempDir . '/*');

		if (!file_exists($tempDir)) {
			mkdir($tempDir, 0777, true);
		}

		foreach ($files as $file) {
			if (is_file($file))
				unlink($file);
		}
	}
}
