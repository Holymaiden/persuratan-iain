@foreach ($data as $key => $v)
    <tr class="text-start text-gray-600 fs-7">

        <td>
            <span class="fw-semibold">
                {{ ++$key }} {{-- Kode Klasifikasi --}}
            </span>
        </td>
        <td>
            <span class="fw-semibold text-nowrap">
                {{ $v->name }} {{-- Name --}}
            </span>
        </td>
        <td>
            <span class="fw-semibold">
                {{ $v->surat_masuk_created }}
            </span>
        </td>
        <td>
            <span class="fw-semibold">
                {{ $v->surat_masuk_updated }}
            </span>
        </td>
        <td>
            <span class="fw-semibold">
                {{ $v->surat_keluar_created }}
            </span>
        </td>
        <td>
            <span class="fw-semibold">
                {{ $v->surat_keluar_updated }}
            </span>
        </td>
        <td>
            <span class="fw-semibold">
                {{ $v->arsip_surat_created }}
            </span>
        </td>
        <td>
            <span class="fw-semibold">
                {{ $v->arsip_surat_updated }}
            </span>
        </td>
    </tr>
@endforeach
