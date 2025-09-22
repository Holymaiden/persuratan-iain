<?php

namespace App\Http\Services\Repositories;

use App\Http\Services\Repositories\BaseRepository;
use App\Http\Services\Repositories\Contracts\CariArsipContract;
use App\Models\ArsipSurat;
use App\Models\surat_keluar;
use App\Models\surat_masuk;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

class CariArsipRepository extends BaseRepository implements CariArsipContract
{
	/**
	 * @var
	 */
	protected $model, $suratMasuk, $suratKeluar;

	public function __construct(ArsipSurat $model, surat_masuk $suratMasuk, surat_keluar $suratKeluar)
	{
		$this->model = $model;
		$this->suratKeluar = $suratKeluar;
		$this->suratMasuk = $suratMasuk;
	}

	public function paginated(array $criteria)
	{
		$perPage = $criteria['per_page'] ?? 5;
		$field = $criteria['sort_field'] ?? 'id';
		$sortOrder = $criteria['sort_order'] ?? 'desc';
		$search = $criteria['search'] ?? '';

		// $type = $criteria['type_surat'] ?? 'Arsip';

		// if ($type === 'Surat Masuk') {
		// 	$filter = $this->suratMasuk;
		// } elseif ($type === 'Surat Keluar') {
		// 	$filter = $this->suratKeluar;
		// } else {
		// 	$filter = $this->model;
		// }


		return $this->model->when($search, function ($query) use ($search): void {
			$query->where(function ($q) use ($search): void {
				$q->where('nomor', 'like', "%" . $search . "%")
					->orWhere('jumlah', 'like', "%" . $search . "%")
					->orWhere('uraian', 'like', "%" . $search . "%")
					->orWhere('lokal', 'like', "%" . $search . "%")
					->orWhere('pencipta', 'like', "%" . $search . "%")
					->orWhere('retensi', 'like', "%" . $search . "%")
					->orWhere('retensi2', 'like', "%" . $search . "%")
					->orWhere('retensi3', 'like', "%" . $search . "%")
					->orWhere('unit_pengolah', 'like', "%" . $search . "%")
					->orWhere('media', 'like', "%" . $search . "%")
					->orWhere('tgl', 'like', "%" . $search . "%")
					->orWhere('ket', 'like', "%" . $search . "%")
					->orWhere('perihal', 'like', "%" . $search . "%")
					->orWhere('no_rak', 'like', "%" . $search . "%")
					->orWhere('no_box', 'like', "%" . $search . "%")
					->orWhere('upload', 'like', "%" . $search . "%");
			});
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

	public function filter(array $criteria)
	{
		$perPage = $criteria['per_page'] ?? 5;
		$field = $criteria['sort_field'] ?? 'id';
		$sortOrder = $criteria['sort_order'] ?? 'desc';

		$type = $criteria['search']['type_surat'] ?? 'Arsip';

		if ($type === 'Surat Masuk') {
			$filter = $this->suratMasuk->select('*')
				->selectRaw('CASE WHEN retensi < CURDATE() THEN 1 ELSE 0 END as pindah_aktif')
				->selectRaw('CASE WHEN retensi2 < CURDATE() THEN 1 ELSE 0 END as permanent_aktif')
				->selectRaw('CASE WHEN retensi2 < CURDATE() THEN 1 ELSE 0 END as musnah_aktif')
				->selectRaw('CASE WHEN riwayat IN ("pindah", "musnah", "permanent") THEN 1 ELSE 0 END as revert_aktif')
				->where('riwayat', 'arsip');
		} elseif ($type === 'Surat Keluar') {
			$filter = $this->suratKeluar->select('*')
				->selectRaw('CASE WHEN retensi < CURDATE() THEN 1 ELSE 0 END as pindah_aktif')
				->selectRaw('CASE WHEN retensi2 < CURDATE() THEN 1 ELSE 0 END as permanent_aktif')
				->selectRaw('CASE WHEN retensi2 < CURDATE() THEN 1 ELSE 0 END as musnah_aktif')
				->selectRaw('CASE WHEN riwayat IN ("pindah", "musnah", "permanent") THEN 1 ELSE 0 END as revert_aktif')
				->where('riwayat', 'arsip');
		} else if ($type == 'Arsip') {
			$filter = $this->model->select('*')
				->selectRaw('CASE WHEN retensi < CURDATE() THEN 1 ELSE 0 END as pindah_aktif')
				->selectRaw('CASE WHEN retensi2 < CURDATE() THEN 1 ELSE 0 END as permanent_aktif')
				->selectRaw('CASE WHEN retensi2 < CURDATE() THEN 1 ELSE 0 END as musnah_aktif')
				->selectRaw('CASE WHEN status IN ("pindah", "musnah", "permanent") THEN 1 ELSE 0 END as revert_aktif')
				->where('status', 'arsip');
		} else {
			$filter = $this->model->select('*')
				->selectRaw('CASE WHEN retensi < CURDATE() THEN 1 ELSE 0 END as pindah_aktif')
				->selectRaw('CASE WHEN retensi2 < CURDATE() THEN 1 ELSE 0 END as permanent_aktif')
				->selectRaw('CASE WHEN retensi2 < CURDATE() THEN 1 ELSE 0 END as musnah_aktif')
				->selectRaw('CASE WHEN status IN ("pindah", "musnah", "permanent") THEN 1 ELSE 0 END as revert_aktif')
				->where('status', 'arsip');
		}

		// Proses filtering
		$kd_klasifikasi_id = $criteria['search']['kd_klasifikasi_id'] ?? '';
		$nomor = $criteria['search']['nomor'] ?? '';
		$jumlah = $criteria['search']['jumlah'] ?? '';
		$uraian = $criteria['search']['uraian'] ?? '';
		$lokal = $criteria['search']['lokal'] ?? '';
		$pencipta = $criteria['search']['pencipta'] ?? '';
		$retensi = $criteria['search']['retensi'] ?? '';
		$retensi2 = $criteria['search']['retensi2'] ?? '';
		$retensi3 = $criteria['search']['retensi3'] ?? '';
		$unit_pengolah = $criteria['search']['unit_pengolah'] ?? '';
		$media = $criteria['search']['media'] ?? '';
		$tgl = $criteria['search']['tgl'] ?? '';
		$ket = $criteria['search']['ket'] ?? '';
		$perihal = $criteria['search']['perihal'] ?? '';
		$no_rak = $criteria['search']['no_rak'] ?? '';
		$no_box = $criteria['search']['no_box'] ?? '';
		$upload = $criteria['search']['upload'] ?? '';
		$dari_tanggal = $criteria['search']['dari_tanggal'] ?? '';
		$sampai_tanggal = $criteria['search']['sampai_tanggal'] ?? '';

		// Terapkan filter
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
			$filter = Schema::hasColumn($filter->getTable(), 'pencipta') ? $filter->where('pencipta', 'like', '%' . $pencipta . '%')
				: $filter->where('asal', 'like', '%' . $pencipta . '%');
		}

		if (!empty($retensi)) {
			$filter = $filter->where('retensi', 'like', '%' . $retensi . '%');
		}

		if (!empty($retensi2)) {
			$filter = $filter->where('retensi2', 'like', '%' . $retensi2 . '%');
		}

		if (!empty($retensi3)) {
			$filter = $filter->where('retensi3', 'like', '%' . $retensi3 . '%');
		}

		if (!empty($unit_pengolah)) {
			$filter = $filter->where('unit_pengolah', 'like', '%' . $unit_pengolah . '%');
		}

		if (!empty($media)) {
			$filter = $filter->where('jenis_media', 'like', '%' . $media . '%');
		}

		if (!empty($tgl)) {
			if ($type === 'Arsip') {
				$filter = $filter->where('tgl', 'like', '%' . $tgl . '%');
			} else {
				$filter = $filter->where('tgl_surat', 'like', '%' . $tgl . '%');
			}
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

		if (!empty($jumlah)) {
			$filter = $filter->where('jumlah', 'like', '%' . $jumlah . '%');
		}

		if (!empty($upload)) {
			$filter = $filter->where('upload', 'like', '%' . $upload . '%');
		}

		if (!empty($dari_tanggal) || !empty($sampai_tanggal)) {
			if ($type === 'Arsip') {
				$filter = $filter->whereBetween('tgl', [$dari_tanggal, $sampai_tanggal]);
			} else {
				// For Surat Masuk and Surat Keluar
				$filter = $filter->whereBetween('tgl_surat', [$dari_tanggal, $sampai_tanggal]);
			}
		}

		// Pengurutan dan paginasi
		$data = array(
			'filter' 	=> $filter->orderBy($field, $sortOrder)->paginate($perPage),
			// 'filter' 	=> $filter->orderBy($field, $sortOrder),
			'type'		=> $type
		);
		return $data;
	}

	public function filterForExport(array $criteria)
	{
		$field = $criteria['sort_field'] ?? 'id';
		$sortOrder = $criteria['sort_order'] ?? 'desc';

		$type = $criteria['search']['type_surat'] ?? 'Arsip';

		if ($type === 'Surat Masuk') {
			$filter = $this->suratMasuk->select('*')
				->selectRaw('CASE WHEN retensi < CURDATE() THEN 1 ELSE 0 END as pindah_aktif')
				->selectRaw('CASE WHEN retensi2 < CURDATE() THEN 1 ELSE 0 END as permanent_aktif')
				->selectRaw('CASE WHEN retensi2 < CURDATE() THEN 1 ELSE 0 END as musnah_aktif')
				->selectRaw('CASE WHEN riwayat IN ("pindah", "musnah", "permanent") THEN 1 ELSE 0 END as revert_aktif')
				->where('riwayat', 'arsip');
		} elseif ($type === 'Surat Keluar') {
			$filter = $this->suratKeluar->select('*')
				->selectRaw('CASE WHEN retensi < CURDATE() THEN 1 ELSE 0 END as pindah_aktif')
				->selectRaw('CASE WHEN retensi2 < CURDATE() THEN 1 ELSE 0 END as permanent_aktif')
				->selectRaw('CASE WHEN retensi2 < CURDATE() THEN 1 ELSE 0 END as musnah_aktif')
				->selectRaw('CASE WHEN riwayat IN ("pindah", "musnah", "permanent") THEN 1 ELSE 0 END as revert_aktif')
				->where('riwayat', 'arsip');
		} else if ($type == 'Arsip') {
			$filter = $this->model->select('*')
				->selectRaw('CASE WHEN retensi < CURDATE() THEN 1 ELSE 0 END as pindah_aktif')
				->selectRaw('CASE WHEN retensi2 < CURDATE() THEN 1 ELSE 0 END as permanent_aktif')
				->selectRaw('CASE WHEN retensi2 < CURDATE() THEN 1 ELSE 0 END as musnah_aktif')
				->selectRaw('CASE WHEN status IN ("pindah", "musnah", "permanent") THEN 1 ELSE 0 END as revert_aktif')
				->where('status', 'arsip');
		} else {
			$filter = $this->model->select('*')
				->selectRaw('CASE WHEN retensi < CURDATE() THEN 1 ELSE 0 END as pindah_aktif')
				->selectRaw('CASE WHEN retensi2 < CURDATE() THEN 1 ELSE 0 END as permanent_aktif')
				->selectRaw('CASE WHEN retensi2 < CURDATE() THEN 1 ELSE 0 END as musnah_aktif')
				->selectRaw('CASE WHEN status IN ("pindah", "musnah", "permanent") THEN 1 ELSE 0 END as revert_aktif')
				->where('status', 'arsip');
		}

		// Proses filtering (sama seperti method filter yang sudah ada)
		$kd_klasifikasi_id = $criteria['search']['kd_klasifikasi_id'] ?? '';
		$nomor = $criteria['search']['nomor'] ?? '';
		$jumlah = $criteria['search']['jumlah'] ?? '';
		$uraian = $criteria['search']['uraian'] ?? '';
		$lokal = $criteria['search']['lokal'] ?? '';
		$pencipta = $criteria['search']['pencipta'] ?? '';
		$retensi = $criteria['search']['retensi'] ?? '';
		$retensi2 = $criteria['search']['retensi2'] ?? '';
		$retensi3 = $criteria['search']['retensi3'] ?? '';
		$unit_pengolah = $criteria['search']['unit_pengolah'] ?? '';
		$media = $criteria['search']['media'] ?? '';
		$tgl = $criteria['search']['tgl'] ?? '';
		$ket = $criteria['search']['ket'] ?? '';
		$perihal = $criteria['search']['perihal'] ?? '';
		$no_rak = $criteria['search']['no_rak'] ?? '';
		$no_box = $criteria['search']['no_box'] ?? '';
		$upload = $criteria['search']['upload'] ?? '';
		$dari_tanggal = $criteria['search']['dari_tanggal'] ?? '';
		$sampai_tanggal = $criteria['search']['sampai_tanggal'] ?? '';

		// Terapkan filter
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
			$filter = Schema::hasColumn($filter->getTable(), 'pencipta') ? $filter->where('pencipta', 'like', '%' . $pencipta . '%')
				: $filter->where('asal', 'like', '%' . $pencipta . '%');
		}

		if (!empty($retensi)) {
			$filter = $filter->where('retensi', 'like', '%' . $retensi . '%');
		}

		if (!empty($retensi2)) {
			$filter = $filter->where('retensi2', 'like', '%' . $retensi2 . '%');
		}

		if (!empty($retensi3)) {
			$filter = $filter->where('retensi3', 'like', '%' . $retensi3 . '%');
		}

		if (!empty($unit_pengolah)) {
			$filter = $filter->where('unit_pengolah', 'like', '%' . $unit_pengolah . '%');
		}

		if (!empty($media)) {
			$filter = $filter->where('jenis_media', 'like', '%' . $media . '%');
		}

		if (!empty($tgl)) {
			if ($type === 'Arsip') {
				$filter = $filter->where('tgl', 'like', '%' . $tgl . '%');
			} else {
				$filter = $filter->where('tgl_surat', 'like', '%' . $tgl . '%');
			}
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

		if (!empty($jumlah)) {
			$filter = $filter->where('jumlah', 'like', '%' . $jumlah . '%');
		}

		if (!empty($upload)) {
			$filter = $filter->where('upload', 'like', '%' . $upload . '%');
		}

		if (!empty($dari_tanggal) || !empty($sampai_tanggal)) {
			if ($type === 'Arsip') {
				$filter = $filter->whereBetween('tgl', [$dari_tanggal, $sampai_tanggal]);
			} else {
				// For Surat Masuk and Surat Keluar
				$filter = $filter->whereBetween('tgl_surat', [$dari_tanggal, $sampai_tanggal]);
			}
		}

		// Return semua data tanpa pagination untuk export
		return $filter->orderBy($field, $sortOrder)->get();
	}


	public function getFile($request) {}

	public function bulkUpdateStatus(array $ids, $status, $tableType)
	{
		if (empty($ids) || !in_array($status, ['pindah', 'musnah', 'permanent'])) {
			return false;
		}

		$updateData = [];
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
			$affected = 0;

			// Jika Mixed, coba update di semua tabel
			if ($tableType === 'Mixed') {
				// Try update in arsip_surat table
				$updateDataArsip = array_merge($updateData, ['status' => $status]);
				$affectedArsip = $this->model->whereIn('id', $ids)->update($updateDataArsip);

				// Try update in surat_masuk table
				$updateDataSurat = array_merge($updateData, ['riwayat' => $status]);
				$affectedSuratMasuk = $this->suratMasuk->whereIn('id', $ids)->update($updateDataSurat);

				// Try update in surat_keluar table
				$affectedSuratKeluar = $this->suratKeluar->whereIn('id', $ids)->update($updateDataSurat);

				$affected = $affectedArsip + $affectedSuratMasuk + $affectedSuratKeluar;
			} else {
				// Update berdasarkan tipe tabel
				if ($tableType === 'Surat Masuk') {
					$updateData['riwayat'] = $status;
					$affected = $this->suratMasuk->whereIn('id', $ids)->update($updateData);
				} elseif ($tableType === 'Surat Keluar') {
					$updateData['riwayat'] = $status;
					$affected = $this->suratKeluar->whereIn('id', $ids)->update($updateData);
				} else {
					// Untuk Arsip, gunakan field 'status' bukan 'riwayat'
					$updateData['status'] = $status;
					$affected = $this->model->whereIn('id', $ids)->update($updateData);
				}
			}

			return $affected > 0;
		} catch (\Exception $e) {
			Log::error('Bulk update status error: ' . $e->getMessage());
			return false;
		}
	}

	public function bulkRevertStatus(array $ids, $tableType)
	{
		if (empty($ids)) {
			return false;
		}

		$updateData = [
			'tgl_pindah' => null,
			'tgl_musnah' => null,
			'tgl_permanent' => null,
		];

		try {
			$affected = 0;

			// Jika Mixed, coba update di semua tabel
			if ($tableType === 'Mixed') {
				// Try update in arsip_surat table
				$updateDataArsip = array_merge($updateData, ['status' => 'arsip']);
				$affectedArsip = $this->model->whereIn('id', $ids)->update($updateDataArsip);

				// Try update in surat_masuk table
				$updateDataSurat = array_merge($updateData, ['riwayat' => 'arsip']);
				$affectedSuratMasuk = $this->suratMasuk->whereIn('id', $ids)->update($updateDataSurat);

				// Try update in surat_keluar table
				$affectedSuratKeluar = $this->suratKeluar->whereIn('id', $ids)->update($updateDataSurat);

				$affected = $affectedArsip + $affectedSuratMasuk + $affectedSuratKeluar;
			} else {
				// Update berdasarkan tipe tabel
				if ($tableType === 'Surat Masuk') {
					$updateData['riwayat'] = 'arsip';
					$affected = $this->suratMasuk->whereIn('id', $ids)->update($updateData);
				} elseif ($tableType === 'Surat Keluar') {
					$updateData['riwayat'] = 'arsip';
					$affected = $this->suratKeluar->whereIn('id', $ids)->update($updateData);
				} else {
					// Untuk Arsip, gunakan field 'status' bukan 'riwayat'
					$updateData['status'] = 'arsip';
					$affected = $this->model->whereIn('id', $ids)->update($updateData);
				}
			}

			return $affected > 0;
		} catch (\Exception $e) {
			Log::error('Bulk revert status error: ' . $e->getMessage());
			return false;
		}
	}

	/**
	 * Get combined data filtered by specific status
	 * Untuk arsip-musnah, arsip-permanen, dan arsip-pindah controllers
	 * Mengambil data gabungan dari arsip_surat, surat_masuk, dan surat_keluar
	 */
	public function filterByStatus(array $criteria, $status)
	{
		$perPage = $criteria['per_page'] ?? 5;
		$field = $criteria['sort_field'] ?? 'id';
		$sortOrder = $criteria['sort_order'] ?? 'desc';

		// Get combined data from all three tables
		$combinedData = $this->getCombinedDataByStatus($status, $criteria);

		// Apply additional filtering if search criteria exist
		if (isset($criteria['search'])) {
			$kd_klasifikasi_id = $criteria['search']['kd_klasifikasi_id'] ?? '';
			$nomor = $criteria['search']['nomor'] ?? '';
			$uraian = $criteria['search']['uraian'] ?? '';

			// Filter the collection
			$combinedData = $combinedData->filter(function ($item) use ($kd_klasifikasi_id, $nomor, $uraian) {
				$match = true;

				if (!empty($kd_klasifikasi_id) && isset($item->kd_klasifikasi_id)) {
					$match = $match && ($item->kd_klasifikasi_id == $kd_klasifikasi_id);
				}

				if (!empty($nomor) && isset($item->nomor)) {
					$match = $match && (stripos($item->nomor, $nomor) !== false);
				}

				if (!empty($uraian) && isset($item->uraian)) {
					$match = $match && (stripos($item->uraian, $uraian) !== false);
				}

				return $match;
			});
		}

		// Sort the collection
		$combinedData = $sortOrder === 'asc'
			? $combinedData->sortBy($field)
			: $combinedData->sortByDesc($field);

		// Manual pagination for collection
		$currentPage = request()->get('page', 1);
		$total = $combinedData->count();
		$items = $combinedData->slice(($currentPage - 1) * $perPage, $perPage)->values();

		return new \Illuminate\Pagination\LengthAwarePaginator(
			$items,
			$total,
			$perPage,
			$currentPage,
			[
				'path' => request()->url(),
				'pageName' => 'page',
			]
		);
	}

	/**
	 * Simple filter for status-specific controllers
	 * Returns all data with specified status from all tables
	 * Mengambil data gabungan dari arsip_surat, surat_masuk, dan surat_keluar
	 */
	public function paginatedByStatus(array $criteria, $status)
	{
		$perPage = $criteria['per_page'] ?? 5;
		$field = $criteria['sort_field'] ?? 'id';
		$sortOrder = $criteria['sort_order'] ?? 'desc';

		// Get combined data from all three tables
		$combinedData = $this->getCombinedDataByStatus($status);

		// Sort the collection
		$combinedData = $sortOrder === 'asc'
			? $combinedData->sortBy($field)
			: $combinedData->sortByDesc($field);

		// Manual pagination for collection
		$currentPage = request()->get('page', 1);
		$total = $combinedData->count();
		$items = $combinedData->slice(($currentPage - 1) * $perPage, $perPage)->values();

		return new \Illuminate\Pagination\LengthAwarePaginator(
			$items,
			$total,
			$perPage,
			$currentPage,
			[
				'path' => request()->url(),
				'pageName' => 'page',
			]
		);
	}

	/**
	 * Get combined data from all tables with specific status
	 * Mengambil dari semua tabel (arsip_surat, surat_masuk, surat_keluar)
	 */
	public function getCombinedDataByStatus($status, $criteria = [])
	{
		$collections = collect();

		// Get from arsip_surat
		$arsipData = $this->model->select('*')
			->selectRaw('CASE WHEN retensi < CURDATE() THEN 1 ELSE 0 END as pindah_aktif')
			->selectRaw('CASE WHEN retensi2 < CURDATE() THEN 1 ELSE 0 END as permanent_aktif')
			->selectRaw('CASE WHEN retensi2 < CURDATE() THEN 1 ELSE 0 END as musnah_aktif')
			->selectRaw('CASE WHEN status IN ("pindah", "musnah", "permanent") THEN 1 ELSE 0 END as revert_aktif')
			->selectRaw('"Arsip" as table_type')
			->where('status', $status)
			->get();

		// Get from surat_masuk
		$suratMasukData = $this->suratMasuk->select('*')
			->selectRaw('CASE WHEN retensi < CURDATE() THEN 1 ELSE 0 END as pindah_aktif')
			->selectRaw('CASE WHEN retensi2 < CURDATE() THEN 1 ELSE 0 END as permanent_aktif')
			->selectRaw('CASE WHEN retensi2 < CURDATE() THEN 1 ELSE 0 END as musnah_aktif')
			->selectRaw('CASE WHEN riwayat IN ("pindah", "musnah", "permanent") THEN 1 ELSE 0 END as revert_aktif')
			->selectRaw('"Surat Masuk" as table_type')
			->where('riwayat', $status)
			->get();

		// Get from surat_keluar
		$suratKeluarData = $this->suratKeluar->select('*')
			->selectRaw('CASE WHEN retensi < CURDATE() THEN 1 ELSE 0 END as pindah_aktif')
			->selectRaw('CASE WHEN retensi2 < CURDATE() THEN 1 ELSE 0 END as permanent_aktif')
			->selectRaw('CASE WHEN retensi2 < CURDATE() THEN 1 ELSE 0 END as musnah_aktif')
			->selectRaw('CASE WHEN riwayat IN ("pindah", "musnah", "permanent") THEN 1 ELSE 0 END as revert_aktif')
			->selectRaw('"Surat Keluar" as table_type')
			->where('riwayat', $status)
			->get();

		// Combine all collections
		$collections = $collections->merge($arsipData)
			->merge($suratMasukData)
			->merge($suratKeluarData);

		return $collections;
	}

	/**
	 * Delete arsip permanently
	 */
	public function deleteArsip($id, $tableType)
	{
		try {
			$deleted = false;

			if ($tableType === 'Surat Masuk') {
				$deleted = $this->suratMasuk->where('id', $id)->delete();
			} elseif ($tableType === 'Surat Keluar') {
				$deleted = $this->suratKeluar->where('id', $id)->delete();
			} else {
				// Default ke arsip_surat
				$deleted = $this->model->where('id', $id)->delete();
			}

			return $deleted > 0;
		} catch (\Exception $e) {
			Log::error('Delete arsip error: ' . $e->getMessage());
			return false;
		}
	}
}
