<?php

namespace App\Http\Controllers\Admin\Settings;

use Flash;
use Response;
use Request;
use Input;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

use App\Http\Requests\Settings\CreateUserRequest;
use App\Http\Requests\Settings\UpdateUserRequest;
use App\Http\Controllers\AppBaseController;
use App\Mail\WelcomeMail;
use App\Repositories\Settings\UserRepository;
use App\Repositories\Settings\RoleRepository;
use App\Repositories\Settings\TagRepository;
use App\Repositories\Settings\PartnerRepository;
use App\Repositories\Definitions\TermRepository;


use App\Models\Settings\User;
use App\Models\Settings\UserTag;
use App\Models\Settings\UserPartner;
use App\Models\Settings\UserRole;
use App\Models\Settings\Language;

use App\DataTables\Settings\UserDataTable;
use Exception;

class UserController extends AppBaseController
{
	/** @var  UserRepository */
	private $userRepository;

	/** @var  RoleRepository */
	private $roleRepository;

	/** @var  TagRepository */
	private $tagRepository;

	/** @var  PartnerRepository */
	private $partnerRepository;

	/** @var  TermRepository */
	private $termRepository;

	public function __construct(
		UserRepository $userRepo,
		RoleRepository $roleRepo,
		TagRepository $tagRepo,
		PartnerRepository $partnerRepo,
		TermRepository $termRepo
	) {
		$this->middleware('auth');
		$this->userRepository    = $userRepo;
		$this->roleRepository    = $roleRepo;
		$this->tagRepository     = $tagRepo;
		$this->partnerRepository = $partnerRepo;
		$this->termRepository    = $termRepo;
	}

	/**
	 * Display a listing of the User.
	 *
	 * @param UserDataTable $userDataTable
	 * @return Response
	 */
	public function index(UserDataTable $userDataTable)
	{
		$src = Request::get('src');
		$role = Request::get('role');
		$tag = Request::get('tag');
		$partner = Request::get('partner');
		$category = Request::get('category');

		$roles = $this->roleRepository->getRolesForSelect(true);
		$partners = $this->partnerRepository->getPartnersForSelect('en', true);
		$tags = $this->tagRepository->getForSelect(true);
		$categoryTerms = $this->termRepository->getRootCategoryTermsForSelect(true);

		$pageData = array(
			'src' => $src,
			'role_id' => $role,
			'partner_id' => $partner,
			'tag_id' => $tag,
			'category_id' => $category,
			'partners' => $partners,
			'tags' => $tags,
			'roles' => $roles,
			'categoryTerms' => $categoryTerms,
		);

		return $userDataTable->render('settings.users.index', $pageData);
	}

	/**
	 * Show the form for creating a new User.
	 *
	 * @return Response
	 */
	public function create()
	{
		$defaultLangCode = Language::defaultLang()->code;

		$roles = $this->roleRepository->getRolesForSelect();
		$partners = $this->partnerRepository->getPartnersForSelect('en', false);
		$tags = $this->tagRepository->getForSelect();
		$categoryTerms = $this->termRepository->getRootCategoryTermsForSelect();

		if (!Auth::user()->isAdmin()) {
			$partnerIds = Auth::user()->partnerIds();
			foreach ($partners as $partnerId => $partnerName) {
				if ($partnerId != '' && !in_array($partnerId, $partnerIds)) {
					unset($partners[$partnerId]);
				}
			}

			$assignableRoleIds = $this->getAssignableRoles();

			foreach ($roles as $roleId => $roleName) {
				if (!in_array($roleId, $assignableRoleIds)) {
					unset($roles[$roleId]);
				}
			}
		}

		$pageData = array(
			'user' => new User(),
			'roles' => $roles,
			'tags' => $tags,
			'partners' => $partners,
			'categoryTerms' => $categoryTerms,
		);

		return view('settings.users.create', $pageData);
	}

	/**
	 * Store a newly created User in storage.
	 *
	 * @param CreateUserRequest $request
	 *
	 * @return Response
	 */
	public function store(CreateUserRequest $request)
	{
		$input = $request->all();
		$password = "";

		$tagIds = [];
		if (isset($input['tag_id'])) {
			$tagIds = $input['tag_id'];
		}

		$partnerIds = [];
		if (isset($input['partner_id'])) {
			$partnerIds = $input['partner_id'];
		}

		$roleIds = [];
		if (isset($input['role_id'])) {
			$roleIds = $input['role_id'];
		}

		$userByEmail = $this->userRepository->getByEmail($input['email']);
		$userByUsername = $this->userRepository->getByUsername($input['username']);

		if ($userByEmail != null) {
			Flash::error('Email already exists.');
			return redirect()->back()->withInput(Input::all());
		} else if ($userByUsername != null) {
			Flash::error('Username already exists.');
			return redirect()->back()->withInput(Input::all());
		} else {
			if (isset($input['password']) && strlen($input['password']) > 0) {
				if (strlen($input['password']) < 8) {
					Flash::error('Passwords must be at least 8 characters in length');
					return redirect()->back()->withInput(Input::all());
				} else {
					$password = $input['password'];
					$input['password'] = Hash::make($input['password']);
				}
			} else {
				Flash::error('Passwords must be at least 8 characters in length');
				return redirect()->back()->withInput(Input::all());
			}

			foreach ($roleIds as $roleId) {
				if (isset($roleId) && $roleId > 0) {
					if (!$this->canAssingRole($roleId)) {
						Flash::error('You don\'t have permission to assign this role.');
						return redirect()->back()->withInput(Input::all());
					}
				} else {
					Flash::error('You have to assign a role to the user.');
					return redirect()->back()->withInput(Input::all());
				}
			}

			$user = $this->userRepository->create($input);

			if ($user) {
				if (isset($input['feedback_term_id'])) {
					$user->feedback_ids = implode(',', $input['feedback_term_id']);
				} else {
					$user->feedback_ids = '';
				}

				$result = $user->save();

				if ($result) {
					UserRole::setRoleIds($user->id, $roleIds);
					UserPartner::setPartnerIds($user->id, $partnerIds);
					UserTag::setTagIds($user->id, $tagIds);

					$data = ([
						'name' => $user->name,
						'email' => $user->email,
						'username' => $user->username,
						'password' => $password
					]);

					try {
						Mail::to($user->email)->send(new WelcomeMail($data));
					} catch (Exception $e) {
					}

					Flash::success('User saved successfully.');
					return redirect(route('settings.users.index'));
				} else {
					Flash::error('An error occured.');
					return redirect()->back()->withInput(Input::all());
				}
			}
		}



		return redirect(route('settings.users.index'));
	}

	/**
	 * Display the specified User.
	 *
	 * @param  int $id
	 *
	 * @return Response
	 */
	public function show($id)
	{
		$user = $this->userRepository->find($id);

		if (empty($user)) {
			Flash::error('User not found');

			return redirect(route('settings.users.index'));
		}

		return view('settings.users.show')->with('user', $user);
	}

	/**
	 * Show the form for editing the specified User.
	 *
	 * @param  int $id
	 *
	 * @return Response
	 */
	public function edit($id)
	{
		$defaultLangCode = Language::defaultLang()->code;

		$user = $this->userRepository->find($id);
		$roles = $this->roleRepository->getRolesForSelect();
		$partners = $this->partnerRepository->getPartnersForSelect($defaultLangCode);
		$tags = $this->tagRepository->getForSelect();
		$user->role_id = UserRole::getUserRoleIds($id);
		$user->tag_id = UserTag::getUserTagIds($id);
		$user->partner_id = UserPartner::getUserPartnerIds($id);
		if ($user->feedback_ids != null) {
			$user->feedback_term_id =  explode(',', $user->feedback_ids);
		}
		$categoryTerms = $this->termRepository->getRootCategoryTermsForSelect();

		if (!Auth::user()->isAdmin()) {
			$partnerIds = Auth::user()->partnerIds();
			foreach ($partners as $partnerId => $partnerName) {
				if ($partnerId != '' && !in_array($partnerId, $partnerIds)) {
					unset($partners[$partnerId]);
				}
			}

			$assignableRoleIds = $this->getAssignableRoles();

			foreach ($roles as $roleId => $roleName) {
				if (!in_array($roleId, $assignableRoleIds)) {
					unset($roles[$roleId]);
				}
			}
		}

		if (empty($user)) {
			Flash::error('User not found');

			return redirect(route('settings.users.index'));
		}

		return view('settings.users.edit')
			->with('user', $user)
			->with('roles', $roles)
			->with('partners', $partners)
			->with('tags', $tags)
			->with('categoryTerms', $categoryTerms);
	}

	/**
	 * Update the specified User in storage.
	 *
	 * @param  int              $id
	 * @param UpdateUserRequest $request
	 *
	 * @return Response
	 */
	public function update($id, UpdateUserRequest $request)
	{
		$input = $request->all();

		$tagIds = [];
		if (isset($input['tag_id'])) {
			$tagIds = $input['tag_id'];
		}
		if ($tagIds == null) $tagIds = [];


		$partnerIds = [];
		if (isset($input['partner_id'])) {
			$partnerIds = $input['partner_id'];
		}
		if ($partnerIds == null) $partnerIds = [];

		$roleIds = [];
		if (isset($input['role_id'])) {
			$roleIds = $input['role_id'];
		}
		if ($roleIds == null) $roleIds = [];

		$user = $this->userRepository->find($id);
		$userByEmail = $this->userRepository->getByEmail($input['email']);
		$userByUsername = $this->userRepository->getByUsername($input['username']);

		if (empty($user)) {
			Flash::error('User not found');
			return redirect(route('settings.users.index'));
		}

		if ($userByEmail != null && $userByEmail->id != $user->id) {
			Flash::error('Email already exists.');
			return redirect()->back()->withInput(Input::all());
		} else if ($userByUsername != null && $userByUsername->id != $user->id) {
			Flash::error('Username already exists.');
			return redirect()->back()->withInput(Input::all());
		}

		foreach ($roleIds as $roleId) {
			if (isset($roleId) && $roleId > 0) {
				if (!$this->canAssingRole($roleId)) {
					Flash::error('You don\'t have permission to assign this role.');
					return redirect()->back()->withInput(Input::all());
				}
			} else {
				Flash::error('You have to assign a role to the user.');
				return redirect()->back()->withInput(Input::all());
			}
		}

		$user->name = $input['name'];
		$user->email = $input['email'];
		$user->username = $input['username'];
		$user->phone = $input['phone'];
		$user->photo_id = $input['photo_id'];
		$user->active = (isset($input['active']) && $input['active'] == 'on' ? 1 : 0);

		if (isset($input['feedback_term_id'])) {
			$user->feedback_ids = implode(',', $input['feedback_term_id']);
		} else {
			$user->feedback_ids = '';
		}

		if (isset($input['password']) && strlen($input['password']) > 0) {
			if (strlen($input['password']) < 8) {
				Flash::error('Passwords must be at least 8 characters in length');

				return redirect(route('settings.users.edit', ['id' => $id]));
			} else {
				$user->password = Hash::make($input['password']);
			}
		}

		/*
		$currentUserRoleId = Auth::user()->role_id;
		$roleId = $input['role_id'];

		if($currentUserRoleId > $roleId) {
			Flash::error('You don\'t have permission to assign this role.');
			return redirect()->back()->withInput(Input::all());
		}
*/
		$result = $user->save();

		if ($result) {
			UserRole::setRoleIds($id, $roleIds);
			UserPartner::setPartnerIds($id, $partnerIds);
			UserTag::setTagIds($id, $tagIds);
			Flash::success('User updated successfully.');
		} else {
			Flash::error('An error occured.');
		}

		return redirect(route('settings.users.index'));
	}

	/**
	 * Remove the specified User from storage.
	 *
	 * @param  int $id
	 *
	 * @return Response
	 */
	public function destroy($id)
	{
		$user = $this->userRepository->find($id);

		if (empty($user)) {
			Flash::error('User not found');

			return redirect(route('settings.users.index'));
		}

		$this->userRepository->delete($id);

		Flash::success('User deleted successfully.');

		return redirect(route('settings.users.index'));
	}

	public function profile($username)
	{
		$user = $this->userRepository->getByUsername($username);
		$categoryTerms = $this->termRepository->getRootCategoryTermsForSelect();

		if (empty($user)) {
			Flash::error('User not found');

			return redirect(route('settings.users.index'));
		}

		$authUser = Auth::user();
		if ($user->id == $authUser->id) {
			return redirect('/admin/me');
		}

		return view('settings.users.profile')
			->with('isMe', false)
			->with('user', $user);
	}

	public function me()
	{
		$authUser = Auth::user();
		$user = $this->userRepository->getByUsername($authUser->username);
		$categoryTerms = $this->termRepository->getRootCategoryTermsForSelect();

		if (empty($user)) {
			Flash::error('User not found');

			return redirect(route('settings.users.index'));
		}

		$userFeedbacks = [];
		if ($user->feedback_ids != null) {
			$termIds = explode(',', $user->feedback_ids);
			foreach ($termIds as $termId) {
				foreach ($categoryTerms as $id => $categoryTerm) {
					if ($id == $termId) {
						array_push($userFeedbacks, $categoryTerm);
					}
				}
			}
		}
		$user->feedbacks = $userFeedbacks;

		return view('settings.users.profile')
			->with('isMe', true)
			->with('user', $user);
	}

	public function photo($username)
	{
		$user = $this->userRepository->getByUsername($username);

		$filePath = 'media/profileimg.png';

		if ($user != null && $user->photo != null) {
			$filePath = 'media/' . $user->photo->filename;
		}

		return response()->file($filePath);
	}

	private function getAssignableRoles()
	{
		$assignableRoleIds = [];

		$userRoleIds = Auth::user()->roleIds();

		if ($userRoleIds != null && count($userRoleIds) > 0) {
			foreach ($userRoleIds as $userRoleId) {
				switch ($userRoleId) {
					case 1:
						$assignableRoleIds = array_merge($assignableRoleIds, [1, 2, 3, 4, 5, 6]);
						break;

					case 2:
						$assignableRoleIds = array_merge($assignableRoleIds, [2, 3, 4, 5, 6]);
						break;

					case 3:
						$assignableRoleIds = array_merge($assignableRoleIds, [3]);
						break;

					case 5:
						$assignableRoleIds = array_merge($assignableRoleIds, [5]);
						break;

					case 4:
						$assignableRoleIds = array_merge($assignableRoleIds, [3, 4, 5, 6]);
						break;

					case 6:
						$assignableRoleIds = array_merge($assignableRoleIds, [3, 5, 6]);
						break;
				}
			}
		}
		return array_unique($assignableRoleIds);
	}

	private function canAssingRole($roleId)
	{
		$assignableRoleIds = $this->getAssignableRoles();
		return in_array(intval($roleId), $assignableRoleIds);
	}
}
