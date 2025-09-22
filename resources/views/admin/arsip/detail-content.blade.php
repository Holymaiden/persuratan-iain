<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">
                    Informasi Arsip
                    @if (isset($tableType))
                        <span class="badge badge-info">{{ $tableType }}</span>
                    @endif
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td class="fw-bold" style="width: 150px;">Nomor Surat:</td>
                                <td>{{ $data->nomor ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Tanggal:</td>
                                <td>{{ Helper::getDateIndo($data->tgl ?? $data->tgl_surat) }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Perihal:</td>
                                <td>{{ $data->perihal ?? '-' }}</td>
                            </tr>
                            @if (isset($data->uraian))
                                <tr>
                                    <td class="fw-bold">Uraian:</td>
                                    <td>{!! $data->uraian ?? '-' !!}</td>
                                </tr>
                            @endif
                            <tr>
                                <td class="fw-bold">
                                    @if ($tableType === 'Arsip')
                                        Keterangan Keaslian:
                                    @else
                                        Asal Surat:
                                    @endif
                                </td>
                                <td>{{ $data->ket_keaslian ?? ($data->asal ?? '-') }}</td>
                            </tr>
                            @if (isset($data->pencipta))
                                <tr>
                                    <td class="fw-bold">Pencipta:</td>
                                    <td>{{ $data->pencipta ?? '-' }}</td>
                                </tr>
                            @endif
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            @if (isset($data->kd_klasifikasi_id) && $data->kd_klasifikasi_id != 0)
                                <tr>
                                    <td class="fw-bold" style="width: 150px;">Klasifikasi:</td>
                                    <td>
                                        {{ $data->klasifikasi->jenis_klasifikasi->kode ?? '' }}.{{ $data->klasifikasi->nomor ?? '' }}
                                        -
                                        {{ $data->klasifikasi->nama ?? '-' }}
                                    </td>
                                </tr>
                            @endif
                            <tr>
                                <td class="fw-bold">
                                    @if ($tableType === 'Arsip')
                                        Jumlah:
                                    @else
                                        TTD:
                                    @endif
                                </td>
                                <td>{{ $data->jumlah ?? ($data->ttd ?? '-') }}</td>
                            </tr>
                            @if (isset($data->no_box))
                                <tr>
                                    <td class="fw-bold">No Box:</td>
                                    <td>{{ $data->no_box ?? '-' }}</td>
                                </tr>
                            @endif
                            @if (isset($data->no_rak))
                                <tr>
                                    <td class="fw-bold">No Rak:</td>
                                    <td>{{ $data->no_rak ?? '-' }}</td>
                                </tr>
                            @endif
                            <tr>
                                <td class="fw-bold">Status:</td>
                                <td>
                                    @if (isset($data->status))
                                        <span class="badge badge-primary">{{ ucfirst($data->status) }}</span>
                                    @elseif(isset($data->riwayat))
                                        <span class="badge badge-primary">{{ ucfirst($data->riwayat) }}</span>
                                    @else
                                        <span class="badge badge-secondary">-</span>
                                    @endif
                                </td>
                            </tr>
                            @if (isset($data->jenis_media))
                                <tr>
                                    <td class="fw-bold">Jenis Media:</td>
                                    <td>{{ $data->jenis_media ?? '-' }}</td>
                                </tr>
                            @endif
                        </table>
                    </div>
                </div>

                @if (isset($data->retensi) || isset($data->retensi2) || isset($data->retensi3))
                    <div class="row mt-3">
                        <div class="col-12">
                            <h6 class="fw-bold">Informasi Retensi:</h6>
                            <div class="alert">
                                @if ($data->retensi)
                                    <strong>Aktif:</strong>
                                    {{ Helper::getRentangTanggal($data->tgl ?? $data->tgl_surat, $data->retensi) }}
                                    (Hingga {{ Helper::getDateIndo($data->retensi) }})<br>
                                @endif
                                @if ($data->retensi2)
                                    <strong>Inaktif:</strong>
                                    {{ Helper::getRentangTanggal($data->retensi, $data->retensi2) }}
                                    (Hingga {{ Helper::getDateIndo($data->retensi2) }})<br>
                                @endif
                                @if ($data->retensi3)
                                    <strong>Nasib:</strong> {{ $data->retensi3 }}
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

                @php
                    $fileField = $data->file ?? ($data->upload_file ?? null);
                @endphp
                @if (!empty($fileField) && $fileField !== '-')
                    <div class="row mt-3">
                        <div class="col-12">
                            <h6 class="fw-bold">File Dokumen:</h6>
                            <div class="d-flex align-items-center">
                                <i class="ki-duotone ki-file-added fs-2 me-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                @if ($tableType === 'Surat Keluar')
                                    <a href="{{ asset('uploads/surat-keluar/' . $fileField) }}" target="_blank"
                                        class="btn btn-primary btn-sm">
                                    @elseif($tableType === 'Surat Masuk')
                                        <a href="{{ asset('uploads/ttd/surat-masuk/' . $fileField) }}" target="_blank"
                                            class="btn btn-primary btn-sm">
                                        @else
                                            <a href="{{ asset('uploads/arsip/' . $fileField) }}" target="_blank"
                                                class="btn btn-primary btn-sm">
                                @endif
                                <i class="ki-duotone ki-eye fs-4 me-1">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                </i>
                                Lihat File
                                </a>
                            </div>
                        </div>
                    </div>
                @endif

                @if (isset($data->tgl_pindah) || isset($data->tgl_musnah) || isset($data->tgl_permanent))
                    <div class="row mt-3">
                        <div class="col-12">
                            <h6 class="fw-bold">Informasi Tanggal:</h6>
                            <div class="row">
                                @if (isset($data->tgl_pindah))
                                    <div class="col-md-4">
                                        <div class="alert alert-info">
                                            <strong>Tanggal Pindah:</strong><br>
                                            {{ Helper::getDateIndo($data->tgl_pindah) }}
                                        </div>
                                    </div>
                                @endif
                                @if (isset($data->tgl_musnah))
                                    <div class="col-md-4">
                                        <div class="alert alert-danger">
                                            <strong>Tanggal Musnah:</strong><br>
                                            {{ Helper::getDateIndo($data->tgl_musnah) }}
                                        </div>
                                    </div>
                                @endif
                                @if (isset($data->tgl_permanent))
                                    <div class="col-md-4">
                                        <div class="alert alert-success">
                                            <strong>Tanggal Permanen:</strong><br>
                                            {{ Helper::getDateIndo($data->tgl_permanent) }}
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
