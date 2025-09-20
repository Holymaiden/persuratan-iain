<?php

namespace App\Http\Services\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;

interface UsersContract
{
	/**
	 * params string $search
	 * @return Collection
	 */

	public function paginated(array $request);

	/**
	 * params array $criteria
	 * @return Collection
	 */
	public function userActivity(array $criteria);
}
