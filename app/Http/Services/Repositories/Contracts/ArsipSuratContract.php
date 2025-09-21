<?php

namespace App\Http\Services\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;

interface ArsipSuratContract
{
	/**
	 * params string $search
	 * @return Collection
	 */
	public function all();
	public function find($id);
	public function findByCriteria(array $criteria);
	public function getByCriteria(array $criteria);
	public function store(array $attributes);
	public function update(array $attributes, $id);
	public function delete($id);
	public function paginated(array $request);
	public function paginate($request);
	public function filter(array $request);
	public function getFile($request);
	public function updateStatus($id, $status);
	public function revertStatus($id);
	public function bulkUpdateStatus(array $ids, $status);
	public function bulkRevertStatus(array $ids);
}
