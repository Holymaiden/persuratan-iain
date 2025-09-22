<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Services\Repositories\Contracts\CariArsipContract;
use Illuminate\Http\Request;

class ArsipSuratPindahController extends Controller
{
    protected $title, $repo, $response;

    public function __construct(CariArsipContract $repo)
    {
        $this->title = 'arsip-pindah';
        $this->repo = $repo;
    }

    public function index()
    {
        try {
            $title = $this->title;
            return view('admin.' . $title . '.index', compact('title'));
        } catch (\Exception $e) {
            return view('errors.message', ['message' => $e->getMessage()]);
        }
    }

    public function data(Request $request)
    {
        try {
            $title = $this->title;
            $data = is_array($request->search) ? $this->repo->filterByStatus($request->all(), 'pindah') : $this->repo->paginatedByStatus($request->all(), 'pindah');
            $perPage = $request->per_page == '' ? 5 : $request->per_page;
            $view = view('admin.' . $title . '.data', compact('data', 'title'))->with('i', ($request->input('page', 1) -
                1) * $perPage)->render();
            return response()->json([
                "total_page" => $data->lastpage(),
                "total_data" => $data->total(),
                "html"       => $view,
            ]);
        } catch (\Exception $e) {
            return view('errors.message', ['message' => $e->getMessage()]);
        }
    }

    public function filter(Request $request)
    {
        try {
            $data = $this->repo->filterByStatus($request->all(), 'pindah');
            return response()->json($data);
        } catch (\Exception $e) {
            return view('errors.message', ['message' => $e->getMessage()]);
        }
    }

    public function bulkUpdateStatus(Request $request)
    {
        try {
            $ids = $request->input('ids', []);
            $status = $request->input('status');

            // Validasi input
            if (empty($ids) || !is_array($ids)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Silakan pilih minimal satu arsip'
                ], 400);
            }

            // Untuk arsip pindah, hanya bisa diubah ke musnah atau permanent
            if (!in_array($status, ['musnah', 'permanent'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Status tidak valid untuk arsip pindah'
                ], 400);
            }

            $result = $this->repo->bulkUpdateStatus($ids, $status, 'Mixed');

            if ($result) {
                $count = count($ids);
                return response()->json([
                    'success' => true,
                    'message' => "Berhasil mengubah status {$count} arsip pindah menjadi " . ucfirst($status)
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengubah status arsip pindah'
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function bulkRevertStatus(Request $request)
    {
        try {
            $ids = $request->input('ids', []);

            // Validasi input
            if (empty($ids) || !is_array($ids)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Silakan pilih minimal satu arsip'
                ], 400);
            }

            $result = $this->repo->bulkRevertStatus($ids, 'Mixed');

            if ($result) {
                $count = count($ids);
                return response()->json([
                    'success' => true,
                    'message' => "Berhasil mengembalikan {$count} arsip pindah ke status Arsip"
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengembalikan status arsip pindah'
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}
