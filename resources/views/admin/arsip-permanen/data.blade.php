@foreach ($data as $key => $v)
    <tr class="text-start text-gray-600 fs-7">
        <td>
            <!-- Checkbox untuk selection -->
            <div class="form-check form-check-sm form-check-custom form-check-solid">
                <input class="form-check-input arsip-checkbox" type="checkbox" value="{{ $v->id }}"
                    data-status="{{ $v->status }}">
            </div>
        </td>
        <td>
            <span class="fw-semibold">
                {{ ++$key }} {{-- Kode Klasifikasi --}}
            </span>
        </td>
        <td>
            <span class="fw-semibold text-nowrap">
                {{ $v->nomor }} {{-- Nomor Surat --}}
            </span>
        </td>
        <td>
            <span class="fw-semibold text-nowrap">
                {{ Helper::getDateIndo($v->tgl) }} {{-- Tanggal Surat --}}
            </span>
        </td>
        <td>
            <span class="fw-semibold text-nowrap">
                @if ($v->kd_klasifikasi_id != 0)
                    {{ $v->klasifikasi->jenis_klasifikasi->kode . '.' . $v->klasifikasi->nomor }} -
                    {{ $v->klasifikasi->nama ?? '-' }} {{-- Perihal Surat --}}
                @else
                    Tidak ada klasifikasi
                @endif
            </span>
        </td>
        <td>
            <span class="fw-semibold">
                {!! $v->uraian !!}
            </span>
        </td>
        <td>
            <span class="fw-semibold">
                {{ $v->ket_keaslian }} {{-- Tanggal Input --}}
            </span>
        </td>
        <td>
            <span class="fw-semibold">
                {{ $v->perihal }}
            </span>
        </td>
        @if (empty($v->file) || $v->file == '-')
            <td>
                <span class="fw-semibold">
                    Tidak ada file
                </span>
            </td>
        @else
            <td>
                <span class="fw-semibold">
                    {{-- {{ $v->file }}  --}}
                    <a href="{{ asset('uploads/arsip/' . $v->file) }}" target="_blank"
                        class="btn btn-icon btn-bg-secondary btn-active-color-primary btn-sm">
                        <i class="ki-duotone ki-folder-down fs-2">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                    </a>
                </span>
            </td>
        @endif
        <td>
            <span class="fw-semibold">
                {{ $v->jumlah }} {{-- TTD --}}
            </span>
        </td>
        <td>
            <span class="fw-semibold">
                {{ $v->no_box }}
            </span>
        </td>
        <td>
            <span class="fw-semibold text-nowrap">
                {{ Helper::getRentangTanggal($v->tgl, $v->retensi) }} ( Aktif Hingga
                {{ Helper::getDateIndo($v->retensi) }} ) <br>
                {{ Helper::getRentangTanggal($v->retensi, $v->retensi2) }} ( Inaktif Hingga
                {{ Helper::getDateIndo($v->retensi2) }} ) <br>
                {{ $v->retensi3 }} ( Nasib )<br>
            </span>
        </td>
        <td>
            <!-- Status Badge -->
            <span class="badge badge-success">
                {{ ucfirst($v->status) }}
            </span>

            <!-- Available Actions Info -->
            <div class="mt-1">
                <small class="text-muted d-block">✓ Dapat dikembalikan ke Arsip</small>
            </div>
        </td>
        <td>
            <span class="fw-semibold text-nowrap">
                {{ Helper::getDateIndo($v->tgl_pindah) }} {{-- Tanggal Pindah --}}
            </span>
        </td>
        <td>
            <span class="fw-semibold text-nowrap">
                {{ Helper::getDateIndo($v->tgl_permanen) }} {{-- Tanggal Permanen --}}
            </span>
        </td>
    </tr>
@endforeach
