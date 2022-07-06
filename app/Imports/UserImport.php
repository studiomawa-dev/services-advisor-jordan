<?php

namespace App\Imports;

use App\Models\Settings\Language;
use App\Models\Settings\Partner;
use App\Models\Settings\PartnerLang;
use App\Models\Settings\User;
use App\Models\Settings\UserPartner;
use App\Models\Settings\UserRole;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Illuminate\Support\Facades\Hash;


class UserImport implements ToCollection, WithHeadingRow, WithCustomCsvSettings
{

	public function collection(Collection $rows)
	{
		foreach ($rows as $row) {

			$name = $row['name'];
			$partner = $row['partner'];
			$email = $row['email'];
			$status = $row['status'];

			if (!($name && $email)) continue;

			$user = User::where('email', $email)->first();

			if ($user === null) {

				$password = md5(date('YmdHis') . rand(1000, 9999999) . rand(100, 999));

				$user = new User;
				$user->name 	 = $name;
				$user->username = $email;
				$user->email 	 = $email;
				$user->password = Hash::make($password);
				$user->active 	 = 0;
				$user->save();
			}

			if ($partner) {
				$partnerModel = PartnerLang::where('name', $partner)->first();
				if ($partnerModel) {

					if (!UserPartner::where('user_id', $user->id)->where('partner_id', $partnerModel->id)->exists()) {
						/* $userPartner = new UserPartner;
						$userPartner->partner_id = $partnerModel->id;
						$userPartner->user_id = $user->id;
						$userPartner->save(); */

						UserPartner::insert(['user_id' => $user->id, 'partner_id' => $partnerModel->id]);
					}
				}
			}

			$roleID = 4; // User Role
			if (!UserRole::where('user_id', $user->id)->where('role_id', $roleID)->exists()) {
				UserRole::insert(['user_id' => $user->id, 'role_id' => $roleID]);
			}

			echo "$name - $partner - $email - $status ===== " . $user->id . "<br/>";
		}
	}

	public function getCsvSettings(): array
	{
		return [
			'input_encoding' => 'UTF-8'
		];
	}

	private static function slugify($langId, $partnerId, $name)
	{
		$text = preg_replace('~[^\pL\d]+~u', '-', $name);
		$text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

		$text = preg_replace('~[^-\w]+~', '', $text);

		$text = trim($text, '-');

		$text = preg_replace('~-+~', '-', $text);

		$text = strtolower($text);

		if (empty($text)) {
			return time() . uniqid();
		}

		$exists = PartnerLang::where('slug', $text)->where('lang_id', $langId)->where('partner_id', '<>', $partnerId)->get();

		if (count($exists) > 0) {
			return self::slugify($langId, $partnerId, $text . '-1');
		}

		return $text;
	}
}
