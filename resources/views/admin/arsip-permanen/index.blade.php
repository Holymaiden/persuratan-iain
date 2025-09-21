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

            // Bulk Actions JavaScript
            let selectedArsip = [];
            const bulkActionsDiv = $('#bulk-actions');
            const selectedCountSpan = $('#selected-count');
            const selectAllCheckbox = $('#select-all-checkbox');

            // Update bulk actions visibility
            function updateBulkActions() {
                if (selectedArsip.length > 0) {
                    bulkActionsDiv.show();
                    selectedCountSpan.text(selectedArsip.length);
                } else {
                    bulkActionsDiv.hide();
                    selectedCountSpan.text('0');
                }
            }

            // Handle individual checkbox change
            $(document).on('change', '.arsip-checkbox', function() {
                const arsipId = $(this).val();
                if ($(this).is(':checked')) {
                    if (!selectedArsip.includes(arsipId)) {
                        selectedArsip.push(arsipId);
                    }
                } else {
                    selectedArsip = selectedArsip.filter(id => id !== arsipId);
                }
                updateBulkActions();
            });

            // Handle select all checkbox
            selectAllCheckbox.on('change', function() {
                const isChecked = $(this).is(':checked');
                $('.arsip-checkbox').prop('checked', isChecked);

                if (isChecked) {
                    selectedArsip = [];
                    $('.arsip-checkbox').each(function() {
                        selectedArsip.push($(this).val());
                    });
                } else {
                    selectedArsip = [];
                }
                updateBulkActions();
            });

            // Clear selection
            $('#clear-selection-btn').on('click', function() {
                selectedArsip = [];
                $('.arsip-checkbox, #select-all-checkbox').prop('checked', false);
                updateBulkActions();
            });

            // Handle bulk revert (kembalikan ke arsip)
            $('#bulk-revert-btn').on('click', function() {
                if (selectedArsip.length === 0) {
                    Swal.fire('Peringatan', 'Pilih arsip yang ingin dikembalikan terlebih dahulu!',
                        'warning');
                    return;
                }

                Swal.fire({
                    title: 'Konfirmasi',
                    text: `Apakah Anda yakin ingin mengembalikan ${selectedArsip.length} arsip ke status Arsip?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, Kembalikan!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '{{ route('arsip-permanen.bulk-revert-status') }}',
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                ids: selectedArsip
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire('Berhasil!', response.message, 'success');
                                    selectedArsip = [];
                                    $('.arsip-checkbox, #select-all-checkbox').prop(
                                        'checked', false);
                                    updateBulkActions();
                                    loaddata(1, $('#perPage').val() || 5, $(
                                        '#input_search').val());
                                } else {
                                    Swal.fire('Error!', response.message, 'error');
                                }
                            },
                            error: function() {
                                Swal.fire('Error!',
                                    'Terjadi kesalahan saat memproses permintaan.',
                                    'error');
                            }
                        });
                    }
                });
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
        });
    </script>
@endpush
