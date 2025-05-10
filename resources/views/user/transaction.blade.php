@extends('layouts.master')

@section('title')
    Manage Transaction
@endsection

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.dataTables.min.css">
    <style>
        #table-success.table {
            font-size: 0.875em;
        }

        #table-success.table thead th {
            font-size: 0.875em;
        }

        #table-success.table tbody td {
            font-size: 0.875em;
        }

        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter,
        .dataTables_wrapper .dataTables_info,
        .dataTables_wrapper .dataTables_processing,
        .dataTables_wrapper .dataTables_paginate {
            font-size: 0.875em;
        }
    </style>
@endpush

@section('content')
    <div class="page-inner">
        <div class="d-flex align-items-start flex-column flex-md-row pt-2 pb-4">
            <div>
                <h3 class="fw-bold mb-3">Manage Transaction</h3>
                <h6 class="text-muted mb-2">Manage all your transaction here</h6>
            </div>
        </div>

        {{-- Card Transaksi Pending --}}
        <div class="card mb-4">
            <div class="card-header">
                <h4>Pending Transactions</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped" id="table-pending">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Customer</th>
                                <th>Total</th>
                                <th>Transaction Time</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($transactions_pending as $item)
                                <tr>
                                    <td>{{ $item->id }}</td>
                                    <td>{{ $item->user_id ? $item->user->name : $item->customer_name }}</td>
                                    <td>Rp. {{ number_format($item->gross_amount) }}</td>
                                    <td>{{ $item->created_at->format('d/m/Y - H:i') }}</td>
                                    <td>
                                        @if ($item->status == 'Failed')
                                            <span class="badge bg-danger">Failed</span>
                                        @else
                                            <span class="badge bg-warning">Pending</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-info btn-expand-pending" data-id="{{ $item->id }}"
                                            title="View Detail">
                                            <i class="fas fa-chevron-down"></i>
                                        </button>

                                        <button class="btn btn-sm btn-success btn-pay" data-id="{{ $item->id }}"
                                            data-snapToken="{{ $item->snap_token }}" title="Payment">
                                            <i class="fas fa-money-bill-wave"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Card Transaksi Selesai --}}
        <div class="card">
            <div class="card-header">
                <h4>Completed Transactions</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <div class="row mb-3">
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <input type="month" id="filter-month" class="form-control">
                            </div>
                        </div>
                    </div>
                    <table class="table table-striped" id="table-success">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Customer</th>
                                <th>Total</th>
                                <th>Transaction Time</th>
                                <th>Payment Time</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($transactions_success as $item)
                                <tr>
                                    <td>{{ $item->id }}</td>
                                    <td>{{ $item->user_id ? $item->user->name : $item->customer_name }}</td>
                                    <td>Rp. {{ number_format($item->gross_amount) }}</td>
                                    <td>{{ $item->created_at->format('d/m/Y - H:i') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($item->transaction_time)->format('d/m/Y - H:i') }}</td>
                                    <td>
                                        <span class="badge bg-success">Success</span>
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-info btn-expand-success"
                                            data-id="{{ $item->id }}" title="View Detail">
                                            <i class="fas fa-chevron-down"></i>
                                        </button>
                                    </td>

                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Card Transaksi Tidak Selesa --}}
        <div class="card">
            <div class="card-header">
                <h4>Unsuccessful Transactions</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped" id="table-unsuccess">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Customer</th>
                                <th>Total</th>
                                <th>Transaction Time</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($unsuccessfulTransactions as $item)
                                <tr>
                                    <td>{{ $item->id }}</td>
                                    <td>{{ $item->user_id ? $item->user->name : $item->customer_name }}</td>
                                    <td>Rp. {{ number_format($item->gross_amount) }}</td>
                                    <td>{{ $item->created_at->format('d/m/Y - H:i') }}</td>
                                    <td>
                                        <span class="badge bg-danger">Cancel</span>
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-info btn-expand-unsuccess"
                                            data-id="{{ $item->id }}" title="View Detail">
                                            <i class="fas fa-chevron-down"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>
    <script>
        $(document).ready(function() {
            var table_pending = $('#table-pending').DataTable();

            // Delegasi tombol expand
            $('#table-pending tbody').on('click', '.btn-expand-pending', function() {
                var tr = $(this).closest('tr');
                var row = table_pending.row(tr);

                if (row.child.isShown()) {
                    // Close jika sudah terbuka
                    row.child.hide();
                    tr.removeClass('shown');
                } else {
                    // Loading text
                    row.child('<div class="p-3 text-center">Loading...</div>').show();
                    tr.addClass('shown');

                    // Ambil id
                    var id = $(this).data('id');

                    // AJAX untuk ambil detail
                    $.ajax({
                        url: '/get-detail-transaction/' + id,
                        method: 'GET',
                        success: function(response) {
                            row.child('<div class="p-3">' + response + '</div>').show();
                        },
                        error: function() {
                            row.child(
                                '<div class="p-3 text-danger">Failed to load details.</div>'
                            ).show();
                        }
                    });
                }
            });

            // Delegasi tombol Payment
            // $('#table-pending tbody').on('click', '.btn-pay', function() {
            //     var id = $(this).data('id');
            //     var snap_token = $(this).data('snapToken');
            //     $('#paymentModal').find('input[name="transaction_id"]').val(id);
            //     $('#paymentModal').find('input[name="snap_token"]').val(snap_token);
            //     $('#paymentModal').modal('show');
            // });


            $(document).ready(function() {
                var table_success = $('#table-success').DataTable();

                // Filter by month
                $('#filter-month').on('change', function() {
                    var selected = $(this).val(); // format "2025-04"

                    if (selected) {
                        var parts = selected.split('-'); // [2025, 04]
                        var year = parts[0];
                        var month = parts[1];

                        // Karena tanggal di tabel formatnya 'd/m/Y - H:i'
                        // kita buat regex cari '/mm/yyyy' di string
                        var search = month + '/' + year;

                        table_success.column(3).search(search).draw();
                    } else {
                        table_success.column(3).search('').draw();
                    }
                });


                // Delegasi tombol expand
                $('#table-success tbody').on('click', '.btn-expand-success', function() {
                    var tr = $(this).closest('tr');
                    var row = table_success.row(tr);

                    if (row.child.isShown()) {
                        row.child.hide();
                        tr.removeClass('shown');
                    } else {
                        row.child('<div class="p-3 text-center">Loading...</div>').show();
                        tr.addClass('shown');

                        var id = $(this).data('id');

                        $.ajax({
                            url: '/get-detail-transaction/' + id,
                            method: 'GET',
                            success: function(response) {
                                row.child('<div class="p-3">' + response + '</div>')
                                    .show();
                            },
                            error: function() {
                                row.child(
                                    '<div class="p-3 text-danger">Failed to load details.</div>'
                                ).show();
                            }
                        });
                    }
                });
            });

            var table_unsuccess = $('#table-unsuccess').DataTable();

            // Delegasi tombol expand
            $('#table-unsuccess tbody').on('click', '.btn-expand-unsuccess', function() {
                var tr = $(this).closest('tr');
                var row = table_unsuccess.row(tr);

                if (row.child.isShown()) {
                    // Close jika sudah terbuka
                    row.child.hide();
                    tr.removeClass('shown');
                } else {
                    // Loading text
                    row.child('<div class="p-3 text-center">Loading...</div>').show();
                    tr.addClass('shown');

                    // Ambil id
                    var id = $(this).data('id');

                    // AJAX untuk ambil detail
                    $.ajax({
                        url: '/get-detail-transaction/' + id,
                        method: 'GET',
                        success: function(response) {
                            row.child('<div class="p-3">' + response + '</div>').show();
                        },
                        error: function() {
                            row.child(
                                '<div class="p-3 text-danger">Failed to load details.</div>'
                            ).show();
                        }
                    });
                }
            });

        });
    </script>
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}">
    </script>
    <script>
        $(document).ready(function() {
            $('#table-pending tbody').on('click', '.btn-pay', function() {
                let snapToken = $(this).data('snaptoken');
                let transactionId = $(this).data('id');

                // Langsung jalankan Snap tanpa modal
                snap.pay(snapToken, {
                    onSuccess: function(result) {
                        console.log('Success:', result);
                        sendPaymentResult(result, transactionId);
                    },
                    onPending: function(result) {
                        console.log('Pending:', result);
                        sendPaymentResult(result, transactionId);
                    },
                    onError: function(result) {
                        console.log('Error:', result);
                        alert('Pembayaran gagal. Silakan coba lagi.');
                    }
                });
            });

            function sendPaymentResult(result, transactionId) {
                $.ajax({
                    url: '/pay-transaction',
                    method: 'PUT',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    data: {
                        transaction_id: transactionId,
                        snap_result: JSON.stringify(result),
                        payment_method: 'gateway'
                    },
                    success: function(response) {
                        $.notify({
                            message: 'Pembayaran berhasil diproses.',
                            icon: 'fa fa-check'
                        }, {
                            type: 'success',
                            placement: {
                                from: 'bottom',
                                align: 'right'
                            },
                            delay: 5000,
                            z_index: 1050,
                            animate: {
                                enter: 'animated fadeInUp',
                                exit: 'animated fadeOutDown'
                            }
                        });

                        setTimeout(function() {
                            location.reload();
                        }, 2000);
                    },
                    error: function(xhr) {
                        $.notify({
                            message: 'Terjadi kesalahan saat mengirim data pembayaran.',
                            icon: 'fa fa-times'
                        }, {
                            type: 'danger',
                            placement: {
                                from: 'bottom',
                                align: 'right'
                            },
                            delay: 5000,
                            z_index: 1050,
                            animate: {
                                enter: 'animated fadeInUp',
                                exit: 'animated fadeOutDown'
                            }
                        });
                    }
                });
            }
        });
    </script>
@endpush
