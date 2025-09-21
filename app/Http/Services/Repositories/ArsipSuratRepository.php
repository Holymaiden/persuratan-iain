<?php

namespace App\Http\Services\Repositories;

use App\Http\Services\Repositories\BaseRepository;
use App\Http\Services\Repositories\Contracts\ArsipSuratContract;
use App\Models\ArsipSurat;

class ArsipSuratRepository extends BaseRepository implements ArsipSuratContract
{
	/**
	 * @var
	 */
	protected $model;

	public function __construct(ArsipSurat $model)
	{
		$this->model = $model;
	}

	public function paginated(array $criteria, $status = 'arsip')
	{
		$perPage = $criteria['per_page'] ?? 5;
		$field = $criteria['sort_field'] ?? 'id';
		$sortOrder = $criteria['sort_order'] ?? 'desc';
		$search = $criteria['search'] ?? '';

		return $this->model->select('*')
			->selectRaw('CASE WHEN retensi < CURDATE() THEN true ELSE false END as pindah_aktif')
			->selectRaw('CASE WHEN retensi2 < CURDATE() THEN true ELSE false END as permanent_aktif')
			->selectRaw('CASE WHEN retensi2 < CURDATE() THEN true ELSE false END as musnah_aktif')
			->selectRaw('CASE WHEN status IN ("pindah", "musnah", "permanent") THEN true ELSE false END as revert_aktif')
			->when($search, function ($query) use ($search): void {
				$query->where('nomor', 'like', "%" . $search . "%");
				$query->orWhere('uraian', 'like', "%" . $search . "%");
			})->when($status, function ($query) use ($status): void {
				$query->where('status', '=', $status);
			})
			->orderBy($field, $sortOrder)
			->paginate($perPage);
	}

	public function paginate($criteria)
	{
		$perPage = $criteria['per_page'] ?? 5;
		$field = $criteria['sort_field'] ?? 'id';
		$sortOrder = $criteria['sort_order'] ?? 'desc';
		return $this->model->orderBy($field, $sortOrder)->paginate($perPage);
	}

	public function filter(array $criteria, $status = 'arsip')
	{
		$perPage = $criteria['per_page'] ?? 5;
		$field = $criteria['sort_field'] ?? 'id';
		$sortOrder = $criteria['sort_order'] ?? 'desc';
		// criteria
		$kd_klasifikasi_id = $criteria['search']['kd_klasifikasi_id'] ?? '';
		$nomor = $criteria['search']['nomor'] ?? '';
		$uraian = $criteria['search']['uraian'] ?? '';
		$lokal = $criteria['search']['lokal'] ?? '';
		$pencipta = $criteria['search']['pencipta'] ?? '';
		$retensi = $criteria['search']['retensi'] ?? '';
		$unit_pengolah = $criteria['search']['unit_pengolah'] ?? '';
		$media = $criteria['search']['media'] ?? '';
		$tgl = $criteria['search']['tgl'] ?? '';
		$ket = $criteria['search']['ket'] ?? '';
		$perihal = $criteria['search']['perihal'] ?? '';
		$no_rak = $criteria['search']['no_rak'] ?? '';
		$no_box = $criteria['search']['no_box'] ?? '';

		$filter = $this->model->select('*')
			->selectRaw('CASE WHEN retensi < CURDATE() THEN true ELSE false END as pindah_aktif')
			->selectRaw('CASE WHEN retensi2 < CURDATE() THEN true ELSE false END as permanent_aktif')
			->selectRaw('CASE WHEN retensi2 < CURDATE() THEN true ELSE false END as musnah_aktif')
			->selectRaw('CASE WHEN status IN ("pindah", "musnah", "permanent") THEN true ELSE false END as revert_aktif');

		if (!empty($kd_klasifikasi_id)) {
			$filter = $filter->where('kd_klasifikasi_id', '=', $kd_klasifikasi_id);
		}

		if (!empty($ket)) {
			$filter = $filter->where('ket_keaslian', '=', $ket);
		}

		if (!empty($nomor)) {
			$filter = $filter->where('nomor', 'like', "%" . $nomor . "%");
		}

		if (!empty($uraian)) {
			$filter = $filter->where('uraian', 'like', "%" . $uraian . "%");
		}

		if (!empty($lokal)) {
			$filter = $filter->where('lokal', 'like', '%' . $lokal . '%');
		}

		if (!empty($pencipta)) {
			$filter = $filter->where('pencipta', 'like', '%' . $pencipta . '%');
		}

		if (!empty($retensi)) {
			$filter = $filter->where('retensi', 'like', '%' . $retensi . '%');
		}

		if (!empty($unit_pengolah)) {
			$filter = $filter->where('unit_pengolah', 'like', '%' . $unit_pengolah . '%');
		}

		if (!empty($media)) {
			$filter = $filter->where('jenis_media', 'like', '%' . $media . '%');
		}

		if (!empty($tgl)) {
			$filter = $filter->where('tgl', 'like', '%' . $tgl . '%');
		}

		if (!empty($perihal)) {
			$filter = $filter->where('perihal', 'like', '%' . $perihal . '%');
		}

		if (!empty($no_rak)) {
			$filter = $filter->where('no_rak', 'like', '%' . $no_rak . '%');
		}

		if (!empty($no_box)) {
			$filter = $filter->where('no_box', 'like', '%' . $no_box . '%');
		}

		if (!empty($status)) {
			$filter = $filter->where('status', '=', $status);
		}

		$filter = $filter->orderBy($field, $sortOrder)->paginate($perPage);
		return $filter;
	}

	public function getFile($request) {}

	public function updateStatus($id, $status)
	{
		$arsip = $this->model->find($id);
		if (!$arsip) {
			return false;
		}

		$updateData = ['status' => $status];
		$currentDate = now()->format('Y-m-d');

		// Set tanggal berdasarkan status
		switch ($status) {
			case 'pindah':
				$updateData['tgl_pindah'] = $currentDate;
				break;
			case 'musnah':
				$updateData['tgl_musnah'] = $currentDate;
				break;
			case 'permanent':
				$updateData['tgl_permanent'] = $currentDate;
				break;
		}

		return $arsip->update($updateData);
	}

	public function revertStatus($id)
	{
		$arsip = $this->model->find($id);
		if (!$arsip) {
			return false;
		}

		$updateData = [
			'status' => 'arsip',
			'tgl_pindah' => null,
			'tgl_musnah' => null,
			'tgl_permanent' => null
		];

		return $arsip->update($updateData);
	}

	public function bulkUpdateStatus(array $ids, $status)
	{
		if (empty($ids) || !in_array($status, ['pindah', 'musnah', 'permanent'])) {
			return false;
		}

		$updateData = ['status' => $status];
		$currentDate = now()->format('Y-m-d');

		// Set tanggal berdasarkan status
		switch ($status) {
			case 'pindah':
				$updateData['tgl_pindah'] = $currentDate;
				break;
			case 'musnah':
				$updateData['tgl_musnah'] = $currentDate;
				break;
			case 'permanent':
				$updateData['tgl_permanent'] = $currentDate;
				break;
		}

		try {
			$affected = $this->model->whereIn('id', $ids)->update($updateData);
			return $affected > 0;
		} catch (\Exception $e) {
			return false;
		}
	}

	public function bulkRevertStatus(array $ids)
	{
		if (empty($ids)) {
			return false;
		}

		$updateData = [
			'status' => 'arsip',
			'tgl_pindah' => null,
			'tgl_musnah' => null,
			'tgl_permanent' => null
		];

		try {
			$affected = $this->model->whereIn('id', $ids)->update($updateData);
			return $affected > 0;
		} catch (\Exception $e) {
			return false;
		}
	}
}
