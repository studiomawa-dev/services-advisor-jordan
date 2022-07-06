<?php

namespace App\Models\Settings;

use Eloquent as Model;

/**
 * Class UserPartner
 * @package App\Models\Settings
 * @version May 29, 2019, 2:19 pm UTC
 *
 * @property integer user_id
 * @property integer partner_id
 */
class UserPartner extends Model
{

	public $table = 'user_partner';

	const CREATED_AT = 'created_at';
	const UPDATED_AT = 'updated_at';

	public $fillable = [
		'user_id',
		'partner_id'
	];

	/**
	 * The attributes that should be casted to native types.
	 *
	 * @var array
	 */
	protected $casts = [
		'id' => 'integer',
		'user_id' => 'integer',
		'partner_id' => 'integer'
	];

	/**
	 * Validation rules
	 *
	 * @var array
	 */
	public static $rules = [
		'user_id' => 'required',
		'partner_id' => 'required'
	];

	public static function getUsersByPartnerId($partnerId)
	{
		$userIds = self::getPartnerUserIds($partnerId);
		$users = [];
		if (count($userIds) > 0) {
			$users = User::whereIn('id', $userIds)
				->get();

			if ($users != null && count($users) > 0) {
				foreach ($users as $user) {
					$feedbackTermsNames = [];
					$feedbackTerms = User::getFeedbackTerms($user->id);

					if ($feedbackTerms != null && count($feedbackTerms) > 0) {
						foreach ($feedbackTerms as $feedbackTerm) {
							$feedbackTermsNames[] = $feedbackTerm->langs[0]->name;
						}
					}

					$user->feedbackTerms = implode(', ', $feedbackTermsNames);
				}
			}
		}

		return $users;
	}

	public static function getPartnerUserIds($partnerId)
	{
		$userIds = [];
		$partnerUsers = self::where('partner_id', $partnerId)->get();
		if ($partnerUsers != null && count($partnerUsers) > 0) {
			foreach ($partnerUsers as $partnerUser) {
				$userIds[] =  $partnerUser->user_id;
			}
		}

		return $userIds;
	}

	public static function getUserPartners($userId)
	{
		$partnerIds = self::getUserPartnerIds($userId);
		$partners = [];
		if (count($partnerIds) > 0) {
			$partners = Partner::whereIn('id', $partnerIds)
				->with('logo')
				->with('langs')
				->get();
		}

		return $partners;
	}

	public static function getUserPartnerIds($userId)
	{
		$partnerIds = [];
		$userPartners = self::where('user_id', $userId)->get();
		if ($userPartners != null && count($userPartners) > 0) {
			foreach ($userPartners as $userPartner) {
				$partnerIds[] =  $userPartner->partner_id;
			}
		}
		return $partnerIds;
	}

	public static function setPartnerIds($userId, $partnerIds)
	{
		$currentPartnerIds = self::getUserPartnerIds($userId);
		$itemsToAdd = array_diff($partnerIds, $currentPartnerIds);
		$itemsToRemove = array_diff($currentPartnerIds, $partnerIds);

		if (count($itemsToRemove) > 0) {
			self::where('user_id', $userId)->whereIn('partner_id', $itemsToRemove)->delete();
		}

		if (count($itemsToAdd) > 0) {
			$items = [];
			foreach ($itemsToAdd as $itemToAdd) {
				$items[] = ['user_id' => $userId, 'partner_id' => $itemToAdd];
			}

			self::insert($items);
		}
	}
}
