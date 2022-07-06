<?php

namespace App\Repositories\Services;

use App\Models\Services\Service;
use App\Models\Services\ServiceContact;
use App\Models\Settings\User;
use App\Repositories\BaseRepository;
use stdClass;

/**
 * Class ServiceContactRepository
 * @package App\Repositories\Services
 * @version May 27, 2019, 1:59 pm UTC
 */

class ServiceContactRepository extends BaseRepository
{
	/**
	 * @var array
	 */
	protected $fieldSearchable = [
		'service_id',
		'contact_id',
		'category_id'
	];

	/**
	 * Return searchable fields
	 *
	 * @return array
	 */
	public function getFieldsSearchable()
	{
		return $this->fieldSearchable;
	}

	/**
	 * Configure the Model
	 **/
	public function model()
	{
		return ServiceContact::class;
	}

	public function getContactsWithPartner($partnerIds = null)
	{
		$all_contacts = $all_contacts = User::where('active', 1)->whereNull('deleted_at')->get(); //->where('role_id', 5) only contacts

		$contacts = [];
		foreach ($all_contacts as $contact) {
			$c = new stdClass();
			$c->id = $contact->id;
			$c->name = $contact->name;
			$c->email = $contact->email;
			$c->phone = $contact->phone;
			$c->feedback_ids = $contact->feedback_ids != null && strlen($contact->feedback_ids) > 0 ? explode(',', $contact->feedback_ids) : [];
			$c->partners = [];

			$isInPartner = false;
			foreach ($contact->partners as $partner) {
				array_push($c->partners, $partner->id);

				if ($partnerIds != null && in_array($partner->id, $partnerIds)) {
					$isInPartner = true;
				}
			}

			if ($partnerIds == null || $isInPartner) {
				array_push($contacts, $c);
			}
		}

		return $contacts;
	}

	public function getContactsForSelect()
	{
		$all_contacts = User::where('active', 1)->where('role_id', 5)->whereNull('deleted_at')->get();

		$contacts = [];
		foreach ($all_contacts as $contact) {
			$email = ($contact->email != null && strlen($contact->email) > 0) ? ' <' . $contact->email . '>' : '';
			$contacts[$contact->id . ''] = $contact->name . $email;
		}

		return $contacts;
	}

	public function getServiceContacts($serviceId)
	{
		$serviceContactIds = [];
		$service = Service::where('id', $serviceId)->with('contacts')->firstOrFail();
		if ($service != null && $service->contacts != null && count($service->contacts) > 0) {
			foreach ($service->contacts as $serviceContact) {
				$serviceContactIds[$serviceContact->category_id] = $serviceContact->contact_id;
			}
		}
		return $serviceContactIds;
	}

	public function getServiceContactUsers($serviceId, $categoryIds)
	{
		$serviceContacts = [];
		$service = Service::where('id', $serviceId)->with('contacts.contact')->firstOrFail();
		if ($service != null && $service->contacts != null && count($service->contacts) > 0) {
			foreach ($service->contacts as $serviceContact) {
				if(in_array($serviceContact->category_id, $categoryIds)) {
					$contact = $serviceContact->contact;

					$serviceContacts[$serviceContact->category_id] = $serviceContact->contact->toArray();
				}

			}
		}

		return $serviceContacts;
	}

	public function setServiceContacts($serviceId, $input)
	{
		$categoryContacts = [];
		foreach ($input as $key => $value) {
			if (strpos($key, 'service_contact_') > -1) {
				$id = intval(str_replace('service_contact_', '', $key));
				if ($value != null) {
					$categoryContacts[$id] = $value;
				}
			}
		}

		$currentCategoryContacts = $this->getServiceContacts($serviceId);

		$itemsToAdd = array_diff_assoc($categoryContacts, $currentCategoryContacts);
		$itemsToRemove = array_diff_assoc($currentCategoryContacts, $categoryContacts);

		if (count($itemsToRemove) > 0) {
			$categoryIds = array_keys($itemsToRemove);
			ServiceContact::where('service_id', $serviceId)->whereIn('category_id', $categoryIds)->delete();
		}

		if (count($itemsToAdd) > 0) {
			$items = [];
			foreach ($itemsToAdd as $itemCategoryId => $itemContactId) {
				$items[] = ['service_id' => $serviceId, 'contact_id' => intval($itemContactId), 'category_id' => $itemCategoryId];
			}

			ServiceContact::insert($items);
		}
	}
}
