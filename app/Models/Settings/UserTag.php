<?php

namespace App\Models\Settings;

use Eloquent as Model;

/**
 * Class UserTag
 * @package App\Models\Settings
 * @version May 13, 2022, 9:19 pm UTC
 *
 * @property integer user_id
 * @property integer tag_id
 */
class UserTag extends Model
{

	public $table = 'user_tag';

	const CREATED_AT = 'created_at';
	const UPDATED_AT = 'updated_at';

	public $fillable = [
		'user_id',
		'tag_id'
	];

	/**
	 * The attributes that should be casted to native types.
	 *
	 * @var array
	 */
	protected $casts = [
		'id' => 'integer',
		'user_id' => 'integer',
		'tag_id' => 'integer'
	];

	/**
	 * Validation rules
	 *
	 * @var array
	 */
	public static $rules = [
		'user_id' => 'required',
		'tag_id' => 'required'
	];

	public static function getUsersByTagId($tagId)
	{
		$userIds = self::getTagUserIds($tagId);
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

	public static function getTagUserIds($tagId)
	{
		$userIds = [];
		$tagUsers = self::where('tag_id', $tagId)->get();
		if ($tagUsers != null && count($tagUsers) > 0) {
			foreach ($tagUsers as $tagUser) {
				$userIds[] =  $tagUser->user_id;
			}
		}

		return $userIds;
	}

	public static function getUserTags($userId)
	{
		$tagIds = self::getUserTagIds($userId);
		$tags = [];
		if (count($tagIds) > 0) {
			$tags = Tag::whereIn('id', $tagIds)
				->get();
		}

		return $tags;
	}

	public static function getUserTagIds($userId)
	{
		$tagIds = [];
		$userTags = self::where('user_id', $userId)->get();
		if ($userTags != null && count($userTags) > 0) {
			foreach ($userTags as $userTag) {
				$tagIds[] =  $userTag->tag_id;
			}
		}
		return $tagIds;
	}

	public static function setTagIds($userId, $tagIds)
	{
		$currentTagIds = self::getUserTagIds($userId);
		$itemsToAdd = array_diff($tagIds, $currentTagIds);
		$itemsToRemove = array_diff($currentTagIds, $tagIds);

		if (count($itemsToRemove) > 0) {
			self::where('user_id', $userId)->whereIn('tag_id', $itemsToRemove)->delete();
		}

		if (count($itemsToAdd) > 0) {
			$items = [];
			foreach ($itemsToAdd as $itemToAdd) {
				$items[] = ['user_id' => $userId, 'tag_id' => $itemToAdd];
			}

			self::insert($items);
		}
	}
}
