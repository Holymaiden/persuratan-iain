@foreach ($data as $key => $v)
    <tr class="text-start text-gray-600 fs-7">
        <td>
            <!-- Checkbox untuk selection -->
            <div class="form-check form-check-sm form-check-custom form-check-solid">
                <input class="form-check-input arsip-checkbox" type="checkbox" value="{{ $v->id }}"
                    data-table="{{ isset($v->table_type) ? $v->table_type : 'Arsip' }}"
                    data-status="{{ isset($v->status) ? $v->status : $v->riwayat }}">
            </div>
        </td>
        <td>
            <span class="fw-semibold">
                {{ ++$key }}
            </span>
        </td>
        <td>
            <span class="fw-semibold text-nowrap">
                {{ $v->nomor }}
            </span>
        </td>
        <td>
            <span class="fw-semibold text-nowrap">
                {{ Helper::getDateIndo($v->tgl ?? $v->tgl_surat) }}
            </span>
        </td>
        <td>
            <span class="fw-semibold text-nowrap">
                @if (isset($v->kd_klasifikasi_id) && $v->kd_klasifikasi_id != 0)
                    {{ $v->klasifikasi->jenis_klasifikasi->kode . '.' . $v->klasifikasi->nomor }} -
                    {{ $v->klasifikasi->nama ?? '-' }}
                @else
                    Tidak ada klasifikasi
                @endif
            </span>
        </td>
        <td>
            <span class="fw-semibold">
                {!! $v->uraian ?? '-' !!}
            </span>
        </td>
        <td>
            <span class="fw-semibold">
                {{ $v->ket_keaslian ?? ($v->asal ?? '-') }}
            </span>
        </td>
        <td>
            <span class="fw-semibold">
                {{ $v->perihal ?? '-' }}
            </span>
        </td>
        @php
            $fileField = $v->file ?? ($v->upload_file ?? null);
        @endphp
        @if (empty($fileField) || $fileField == '-')
            <td>
                <span class="fw-semibold">
                    Tidak ada file
                </span>
            </td>
        @else
            <td>
                <span class="fw-semibold">
                    @if (isset($v->table_type) && $v->table_type == 'Surat Keluar')
                        <a href="{{ asset('uploads/surat-keluar/' . $fileField) }}" target="_blank"
                            class="btn btn-icon btn-bg-secondary btn-active-color-primary btn-sm">
                        @elseif(isset($v->table_type) && $v->table_type == 'Surat Masuk')
                            <a href="{{ asset('uploads/ttd/surat-masuk/' . $fileField) }}" target="_blank"
                                class="btn btn-icon btn-bg-secondary btn-active-color-primary btn-sm">
                            @else
                                <a href="{{ asset('uploads/arsip/' . $fileField) }}" target="_blank"
                                    class="btn btn-icon btn-bg-secondary btn-active-color-primary btn-sm">
                    @endif
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
                {{ $v->jumlah ?? ($v->ttd ?? '-') }}
            </span>
        </td>
        <td>
            <span class="fw-semibold">
                {{ $v->no_box ?? '-' }}
            </span>
        </td>
        <td>
            <span class="fw-semibold text-nowrap">
                @if ($v->retensi)
                    {{ Helper::getRentangTanggal($v->tgl ?? $v->tgl_surat, $v->retensi) }} ( Aktif Hingga
                    {{ Helper::getDateIndo($v->retensi) }} ) <br>
                @endif
                @if ($v->retensi2)
                    {{ Helper::getRentangTanggal($v->retensi, $v->retensi2) }} ( Inaktif Hingga
                    {{ Helper::getDateIndo($v->retensi2) }} ) <br>
                @endif
                @if ($v->retensi3)
                    {{ $v->retensi3 }} ( Nasib )<br>
                @endif
            </span>
        </td>
        <td>
            <!-- Status Badge -->
            <span class="badge badge-primary">
                {{ ucfirst(isset($v->status) ? $v->status : $v->riwayat) }}
            </span>

            <!-- Available Actions Info -->
            <div class="mt-1">
                <small class="text-muted d-block">✓ Dapat diubah ke musnah/permanent</small>
                <small class="text-muted d-block">✓ Dapat dikembalikan ke Arsip</small>
                @if (isset($v->table_type))
                    <small class="text-info d-block">📋 Dari: {{ $v->table_type }}</small>
                @endif
            </div>
        </td>
        <td>
            <span class="fw-semibold text-nowrap">
                {{ Helper::getDateIndo($v->tgl_pindah) }}
            </span>
        </td>
        <td>
            <div class="d-flex justify-content-center">
                <!-- Action Detail -->
                <a href="#" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1"
                    onclick="showDetail({{ $v->id }}, '{{ isset($v->table_type) ? $v->table_type : 'Arsip' }}')"
                    title="Lihat Detail">
                    <i class="ki-duotone ki-eye fs-2">
                        <span class="path1"></span>
                        <span class="path2"></span>
                        <span class="path3"></span>
                    </i>
                </a>
            </div>
        </td>
    </tr>
@endforeach
