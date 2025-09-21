<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Services\Repositories\Contracts\ArsipSuratContract;
use Illuminate\Http\Request;

class ArsipSuratMusnahController extends Controller
{
    protected $title, $repo, $response;

    public function __construct(ArsipSuratContract $repo)
    {
        $this->title = 'arsip-musnah';
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
            $data = is_array($request->search) ? $this->repo->filter($request->all(), 'musnah') : $this->repo->paginated($request->all(), 'musnah');
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
            $data = $this->repo->filter($request->all(), 'musnah');
            return response()->json($data);
        } catch (\Exception $e) {
            return view('errors.message', ['message' => $e->getMessage()]);
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

            $result = $this->repo->bulkRevertStatus($ids);

            if ($result) {
                $count = count($ids);
                return response()->json([
                    'success' => true,
                    'message' => "Berhasil mengembalikan {$count} arsip musnah ke status Arsip"
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengembalikan status arsip musnah'
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
