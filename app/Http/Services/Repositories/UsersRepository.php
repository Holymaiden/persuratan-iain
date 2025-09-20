<?php

namespace App\Http\Services\Repositories;

use App\Http\Services\Repositories\BaseRepository;
use App\Http\Services\Repositories\Contracts\UsersContract;
use App\Models\User;
use App\Models\log_surat;

class UsersRepository extends BaseRepository implements UsersContract
{
	/**
	 * @var
	 */
	protected $model;

	public function __construct(User $model)
	{
		$this->model = $model;
	}

	public function paginated(array $criteria)
	{
		$perPage = $criteria['per_page'] ?? 5;
		$field = $criteria['sort_field'] ?? 'id';
		$sortOrder = $criteria['sort_order'] ?? 'desc';
		$search = $criteria['search'] ?? '';
		return $this->model
			->where(function ($query) {
				$query->where('email', '!=', 'fihaaadmin@gmail.com')
					->orWhereNull('email');
			})
			->when($search, function ($query) use ($search) {
				$query->where(function ($subQuery) use ($search) {
					$subQuery->where('name', 'like', "%{$search}%")
						->orWhere('username', 'like', "%{$search}%");
				});
			})
			->orderBy($field, $sortOrder)
			->paginate($perPage);
	}

	public function userActivity(array $criteria)
	{
		$perPage = $criteria['per_page'] ?? 5;
		$field = $criteria['sort_field'] ?? 'users.id';
		$sortOrder = $criteria['sort_order'] ?? 'desc';
		$search = $criteria['search'] ?? '';

		$query = $this->model
			->select([
				'users.id',
				'users.name',
				'users.username',
				'users.email'
			])
			->selectRaw('
				COALESCE(COUNT(CASE WHEN log_surats.activity = ? AND log_surats.jenis_log = ? THEN 1 END), 0) as surat_keluar_created,
				COALESCE(COUNT(CASE WHEN log_surats.activity = ? AND log_surats.jenis_log = ? THEN 1 END), 0) as surat_keluar_updated,
				COALESCE(COUNT(CASE WHEN log_surats.activity = ? AND log_surats.jenis_log = ? THEN 1 END), 0) as arsip_surat_created,
				COALESCE(COUNT(CASE WHEN log_surats.activity = ? AND log_surats.jenis_log = ? THEN 1 END), 0) as arsip_surat_updated,
				COALESCE(COUNT(CASE WHEN log_surats.activity = ? AND log_surats.jenis_log = ? THEN 1 END), 0) as surat_masuk_created,
				COALESCE(COUNT(CASE WHEN log_surats.activity = ? AND log_surats.jenis_log = ? THEN 1 END), 0) as surat_masuk_updated,
				COALESCE(COUNT(CASE WHEN log_surats.activity IN (?, ?) THEN 1 END), 0) as total_activity
			', [
				'Create',
				'Surat keluar',
				'Update',
				'Surat keluar',
				'Create',
				'Arsip surat',
				'Update',
				'Arsip surat',
				'Create',
				'Surat masuk',
				'Update',
				'Surat masuk',
				'Create',
				'Update'
			])
			->leftJoin('log_surats', 'users.id', '=', 'log_surats.user_id')
			->where(function ($query) {
				$query->where('users.email', '!=', 'fihaaadmin@gmail.com')
					->orWhereNull('users.email');
			})
			->when($search, function ($query) use ($search) {
				$query->where(function ($subQuery) use ($search) {
					$subQuery->where('users.name', 'like', "%{$search}%")
						->orWhere('users.username', 'like', "%{$search}%")
						->orWhere('users.email', 'like', "%{$search}%");
				});
			})
			->groupBy('users.id', 'users.name', 'users.username', 'users.email')
			->orderBy($field, $sortOrder);

		return $query->paginate($perPage);
	}
}
