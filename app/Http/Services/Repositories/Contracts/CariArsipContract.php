<?php

namespace App\Http\Services\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;

interface CariArsipContract
{
	/**
	 * params string $search
	 * @return Collection
	 */

	public function paginated(array $request);
	public function paginate($request);
	public function filter(array $request);
	public function filterForExport(array $request);
	public function getFile($request);
	public function bulkUpdateStatus(array $ids, $status, $tableType);
	public function bulkRevertStatus(array $ids, $tableType);
	public function filterByStatus(array $criteria, $status);
	public function paginatedByStatus(array $criteria, $status);
	public function getCombinedDataByStatus($status, $criteria = []);
	public function deleteArsip($id, $tableType);
}
