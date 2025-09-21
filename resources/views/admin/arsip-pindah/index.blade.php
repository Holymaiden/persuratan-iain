@extends('admin._layouts.index')

@push($title)
    active
@endpush

@section('content')
    <!--begin::Toolbar-->
    @component('admin._card.breadcrumb')
        @slot('header')
            {{ $title }}
        @endslot
        @slot('page')
            Data
        @endslot
    @endcomponent
    <!--end::Toolbar-->

    <!--begin::Content-->
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <!--begin::Content container-->
        <div id="kt_app_content_container" class="app-container container-xxl">
            <!--begin::Products-->
            <div class="card card-flush">

                <!--begin::Card header-->
                {{-- @include('admin._card.action') --}}
                <div class="card-header align-items-center py-5 gap-2 gap-md-5">
                    <!--begin::Card title-->
                    <div class="card-title">
                        <div class="mw-100px me-3">
                            <select class="form-select form-select-solid me-3" data-control="select2" data-hide-search="true"
                                data-placeholder="Per Page" id="perPage">
                                <option>5</option>
                                <option>10</option>
                                <option>25</option>
                                <option>50</option>
                                <option>100</option>
                            </select>
                        </div>
                        <div class="d-flex">
                            <input id="input_search" type="text" class="form-control form-control-solid w-300px me-3"
                                placeholder="cari nomor / uraian">

                            <button id="button_search" class="btn btn-secondary me-3">
                                <span class="btn-label">
                                    <i class="fa fa-search"></i>
                                </span>
                            </button>

                            <button id="button_refresh" class="btn btn-secondary me-3">
                                <span class="btn-label">
                                    <i class="fa fa-sync"></i>
                                </span>
                            </button>
                        </div>
                    </div>
                    <!--end::Card title-->

                </div>

                <!--end::Card header-->

                <!--begin::Bulk Actions-->
                <div class="card-body pt-0 pb-3" id="bulk-actions" style="display: none;">
                    <div class="d-flex align-items-center gap-3">
                        <span class="fw-semibold text-gray-600">Terpilih: <span id="selected-count">0</span> arsip</span>

                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-success btn-sm" id="bulk-permanent-btn">
                                <i class="ki-duotone ki-save-2 fs-3">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                Ubah ke Permanen
                            </button>
                            <button type="button" class="btn btn-danger btn-sm" id="bulk-musnah-btn">
                                <i class="ki-duotone ki-trash fs-3">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                    <span class="path4"></span>
                                    <span class="path5"></span>
                                </i>
                                Ubah ke Musnah
                            </button>
                            <button type="button" class="btn btn-primary btn-sm" id="bulk-revert-btn">
                                <i class="ki-duotone ki-undo fs-3">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                Kembalikan ke Arsip
                            </button>
                        </div>

                        <button type="button" class="btn btn-light btn-sm" id="clear-selection-btn">
                            <i class="ki-duotone ki-cross fs-3">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            Batal Pilih
                        </button>
                    </div>
                </div>
                <!--end::Bulk Actions-->

                <!--begin::Card body-->
                <div class="card-body pt-0">
                    <div class="table-responsive">
                        <!--begin::Table-->
                        <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_ecommerce_products_table">
                            <thead>
                                <tr class="text-start text-gray-600 fw-bold fs-7 text-uppercase gs-0">
                                    <th class="w-10px pe-2">
                                        <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                            <input class="form-check-input" type="checkbox" id="select-all-checkbox">
                                        </div>
                                    </th>
                                    <th class="min-w-20px pe-2">No</th>
                                    <th class="min-w-120px text-nowrap">Nomor Arsip</th>
                                    <th class="min-w-140px">Tanggal Arsip</th>
                                    <th class="min-w-120px text-nowrap">Kode Klasifikasi</th>
                                    <th class="min-w-300px">Uraian Arsip</th>
                                    <th class="min-w-120px">Keterangan</th>
                                    <th class="min-w-300px">Perihal</th>
                                    <th class="min-w-120px">File</th>
                                    <th class="min-w-120px">Jumlah </th>
                                    <th class="min-w-120px text-nowrap">Nomor Box</th>
                                    <th class="min-w-120px">Retensi</th>
                                    <th class="min-w-150px">Status</th>
                                </tr>
                            </thead>

                            <tbody class="fw-semibold text-gray-600 datatables">

                            </tbody>

                        </table>
                        <!--end::Table-->
                    </div>

                    <!--begin::Pagination-->
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <div class="d-flex flex-wrap py-2 mr-3">
                            <div class="text-center pagination">
                                <div id="contentPage"></div>
                            </div>
                        </div>
                        <div class="d-flex align-items-center py-3">
                            <ul class="pagination twbs-pagination">
                            </ul>
                        </div>
                    </div>
                    <!--end::Pagination-->

                </div>



                <!--end::Card body-->
            </div>
            <!--end::Products-->
        </div>
        <!--end::Content container-->
    </div>
    <!--end::Content-->
@endsection

@push('jsScript')
    <script type="text/javascript">
        $(document).ready(function() {
            loadpage(5, '');
            var $pagination = $('.twbs-pagination');
            var defaultOpts = {
                totalPages: 1,
                prev: '&#8672;',
                next: '&#8674;',
                first: '&#8676;',
                last: '&#8677;',
            };
            $pagination.twbsPagination(defaultOpts);

            function loaddata(page, per_page, search) {
                $.ajax({
                    url: '{{ route($title . '.data') }}',
                    data: {
                        "page": page,
                        "per_page": per_page,
                        "search": search,
                    },
                    type: "GET",
                    datatype: "json",
                    success: function(data) {
                        $(".datatables").html(data.html);
                    }
                });
            }

            function loadpage(per_page, search) {
                $.ajax({
                    url: '{{ route($title . '.data') }}',
                    data: {
                        "per_page": per_page,
                        "search": search,
                    },
                    type: "GET",
                    datatype: "json",
                    success: function(response) {
                        if ($pagination.data("twbs-pagination")) {
                            $pagination.twbsPagination('destroy');
                            $(".datatables").html('<tr><td colspan="4">Data not found</td></tr>');
                        }
                        $pagination.twbsPagination($.extend({}, defaultOpts, {
                            startPage: 1,
                            totalPages: response.total_page,
                            visiblePages: 8,
                            prev: '&#8672;',
                            next: '&#8674;',
                            first: '&#8676;',
                            last: '&#8677;',
                            onPageClick: function(event, page) {
                                if (page == 1) {
                                    var to = 1;
                                } else {
                                    var to = page * per_page - (per_page - 1);
                                }
                                if (page == response.total_page) {
                                    var end = response.total_data;
                                } else {
                                    var end = page * per_page;
                                }
                                $('#contentPage').text('Showing ' + to + ' to ' + end +
                                    ' of ' +
                                    response.total_data + ' entries');
                                loaddata(page, per_page, search);
                            }
                        }));
                    }
                });
            }

            const filterForm = $('#filterArsip');
            const hideBtnFilter = $('#button_hideFilter')
            const showBtnFilter = $('#button_filter')
            const resetFilter = $('.reset-filter')


            filterForm.hide()
            hideBtnFilter.hide()

            showBtnFilter.on('click', function(e) {
                filterForm.show()
                hideBtnFilter.show()
                showBtnFilter.hide()
            });

            hideBtnFilter.on('click', function(e) {
                filterForm.hide()
                hideBtnFilter.hide()
                showBtnFilter.show()
                resetFilter.val('')
            });


            $("#button_search, #perPage").on('click change', function(event) {
                let search = $('#input_search').val();
                let per_page = $('#perPage').val() ?? 5;
                loadpage(per_page, search);
            });

            $("#button_refresh").on('click', function(event) {
                $('#input_search').val('');
                loadpage(5, '');
            });

            // more filter
            $('#search_filter').on('click', function() {
                let nomor = $('#nomor').val()
                let uraian = $('#uraian').val()
                let retensi = $('#retensi').val()
                let pencipta = $('#pencipta').val()
                let unit_pengolah = $('#unit_pengolah').val()
                let lokal = $('#lokal').val()
                let media = $('#media').val()
                let tgl = $('#tgl').val()
                let ket = $('#ket').val()
                let kd_klasifikasi_id = $('#kd_klasifikasi_id').val()

                const formData = {
                    'nomor': nomor,
                    'uraian': uraian,
                    'retensi': retensi,
                    'pencipta': pencipta,
                    'unit_pengolah': unit_pengolah,
                    'lokal': lokal,
                    'media': media,
                    'tgl': tgl,
                    'ket': ket,
                    'kd_klasifikasi_id': kd_klasifikasi_id
                }
                loadpage(5, formData)

            });

            // Handle select all checkbox
            $('#select-all-checkbox').change(function() {
                $('.arsip-checkbox').prop('checked', $(this).is(':checked'));
                updateBulkActions();
            });

            // Handle individual checkbox
            $('body').on('change', '.arsip-checkbox', function() {
                updateBulkActions();

                // Update select all checkbox state
                const totalCheckboxes = $('.arsip-checkbox').length;
                const checkedCheckboxes = $('.arsip-checkbox:checked').length;

                if (checkedCheckboxes === 0) {
                    $('#select-all-checkbox').prop('indeterminate', false).prop('checked', false);
                } else if (checkedCheckboxes === totalCheckboxes) {
                    $('#select-all-checkbox').prop('indeterminate', false).prop('checked', true);
                } else {
                    $('#select-all-checkbox').prop('indeterminate', true);
                }
            });

            // Update bulk actions visibility and button states
            function updateBulkActions() {
                const selectedCheckboxes = $('.arsip-checkbox:checked');
                const count = selectedCheckboxes.length;

                if (count > 0) {
                    $('#bulk-actions').show();
                    $('#selected-count').text(count);

                    // For arsip-pindah: semua always active karena status pindah bisa diubah ke permanent/musnah atau direvert
                    $('#bulk-permanent-btn').prop('disabled', false);
                    $('#bulk-musnah-btn').prop('disabled', false);
                    $('#bulk-revert-btn').prop('disabled', false);
                } else {
                    $('#bulk-actions').hide();
                }
            }

            // Handle bulk status change to permanent/musnah
            $('#bulk-permanent-btn, #bulk-musnah-btn').click(function() {
                const buttonId = $(this).attr('id');
                let status = '';
                let statusText = '';

                if (buttonId === 'bulk-permanent-btn') {
                    status = 'permanent';
                    statusText = 'Permanen';
                } else if (buttonId === 'bulk-musnah-btn') {
                    status = 'musnah';
                    statusText = 'Musnah';
                }

                const selectedIds = $('.arsip-checkbox:checked').map(function() {
                    return $(this).val();
                }).get();

                if (selectedIds.length === 0) {
                    Swal.fire({
                        title: 'Peringatan!',
                        text: 'Silakan pilih arsip terlebih dahulu',
                        icon: 'warning'
                    });
                    return;
                }

                Swal.fire({
                    title: 'Konfirmasi Perubahan Status Bulk',
                    text: `Apakah Anda yakin ingin mengubah ${selectedIds.length} arsip pindah menjadi status ${statusText}?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: `Ya, Ubah ke ${statusText}!`,
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        bulkUpdateStatus(selectedIds, status);
                    }
                });
            });

            // Handle bulk revert
            $('#bulk-revert-btn').click(function() {
                const selectedIds = $('.arsip-checkbox:checked').map(function() {
                    return $(this).val();
                }).get();

                if (selectedIds.length === 0) {
                    Swal.fire({
                        title: 'Peringatan!',
                        text: 'Silakan pilih arsip terlebih dahulu',
                        icon: 'warning'
                    });
                    return;
                }

                Swal.fire({
                    title: 'Konfirmasi Kembalikan Status Bulk',
                    text: `Apakah Anda yakin ingin mengembalikan ${selectedIds.length} arsip pindah ke status Arsip?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, Kembalikan!',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        bulkRevertStatus(selectedIds);
                    }
                });
            });

            // Clear selection
            $('#clear-selection-btn').click(function() {
                $('.arsip-checkbox, #select-all-checkbox').prop('checked', false);
                $('#select-all-checkbox').prop('indeterminate', false);
                updateBulkActions();
            });

            // Bulk update status function
            function bulkUpdateStatus(ids, status) {
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    url: '{{ route('arsip-pindah.bulk-update-status') }}',
                    data: {
                        ids: ids,
                        status: status
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                title: 'Berhasil!',
                                text: response.message,
                                icon: 'success',
                                timer: 2000,
                                showConfirmButton: false
                            });

                            // Clear selection and reload data
                            clearSelectionAndReload();
                        } else {
                            Swal.fire({
                                title: 'Error!',
                                text: response.message,
                                icon: 'error'
                            });
                        }
                    },
                    error: function(xhr) {
                        var errorMsg = 'Terjadi kesalahan saat mengubah status arsip pindah.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }

                        Swal.fire({
                            title: 'Error!',
                            text: errorMsg,
                            icon: 'error'
                        });
                    }
                });
            }

            // Bulk revert status function
            function bulkRevertStatus(ids) {
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    url: '{{ route('arsip-pindah.bulk-revert-status') }}',
                    data: {
                        ids: ids
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                title: 'Berhasil!',
                                text: response.message,
                                icon: 'success',
                                timer: 2000,
                                showConfirmButton: false
                            });

                            // Clear selection and reload data
                            clearSelectionAndReload();
                        } else {
                            Swal.fire({
                                title: 'Error!',
                                text: response.message,
                                icon: 'error'
                            });
                        }
                    },
                    error: function(xhr) {
                        var errorMsg = 'Terjadi kesalahan saat mengembalikan status arsip pindah.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }

                        Swal.fire({
                            title: 'Error!',
                            text: errorMsg,
                            icon: 'error'
                        });
                    }
                });
            }

            // Clear selection and reload data
            function clearSelectionAndReload() {
                $('.arsip-checkbox, #select-all-checkbox').prop('checked', false);
                $('#select-all-checkbox').prop('indeterminate', false);
                updateBulkActions();

                var search = $('#input_search').val();
                var per_page = $('#perPage').val() ?? 5;
                loadpage(per_page, search);
            }
        });
    </script>
@endpush
