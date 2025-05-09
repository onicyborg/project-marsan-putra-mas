@extends('layouts.master')

@section('title')
    Manage Transaction
@endsection

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.dataTables.min.css">
@endpush

@section('content')
    <div class="page-inner">
        <div class="d-flex align-items-start flex-column flex-md-row pt-2 pb-4">
            <div>
                <h3 class="fw-bold mb-3">Manage Transaction</h3>
                <h6 class="text-muted mb-2">Manage all your transaction here</h6>
            </div>
        </div>

        {{-- Button Tambah Transaksi --}}
        <div class="mb-3">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTransactionModal">
                + Add Transaction
            </button>
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

                                        <button class="btn btn-sm btn-danger btn-cancel" data-id="{{ $item->id }}"
                                            title="Cancel Transaction">
                                            <i class="fas fa-times"></i>
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

    {{-- Modal Tambah Transaksi --}}
    <div class="modal fade" id="addTransactionModal" tabindex="-1" aria-labelledby="addTransactionModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="formAddTransaction" method="POST" action="{{ route('transaction.store') }}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="addTransactionModalLabel">Add New Transaction</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        {{-- Pilih Member atau Bukan --}}
                        <div class="mb-3">
                            <label for="clientType" class="form-label">Client Type</label>
                            <select id="clientType" class="form-select" name="client_type" required>
                                <option value="">Select</option>
                                <option value="member">Member</option>
                                <option value="non-member">Non-Member</option>
                            </select>
                        </div>

                        {{-- Jika Member, input nomor HP --}}
                        <div class="mb-3 d-none" id="memberSection">
                            <label for="memberPhone" class="form-label">Phone Number</label>
                            <div class="input-group">
                                <span class="input-group-text">+62</span>
                                <input type="text" name="memberPhone" id="memberPhone"
                                    class="form-control @error('phone_number') is-invalid @enderror"
                                    placeholder="81234567890" oninput="validatePhone(this)">
                                @error('phone_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Tombol Cari Member --}}
                            <button type="button" class="btn btn-secondary mt-2" id="searchMemberBtn">Search
                                Member</button>

                            {{-- Area detail member (nama, email, dll) --}}
                            <div id="memberDetails" class="mt-3"></div>
                        </div>


                        {{-- Jika Non-Member, input nama --}}
                        <div class="mb-3 d-none" id="nonMemberSection">
                            <label for="customerName" class="form-label">Customer Name</label>
                            <input type="text" id="customerName" name="customer_name" class="form-control"
                                placeholder="Enter customer name">
                        </div>

                        {{-- Table Layanan --}}
                        <div class="mb-3">
                            <label class="form-label">Services</label>
                            <table class="table table-bordered" id="serviceTable">
                                <thead>
                                    <tr>
                                        <th>Service Name</th>
                                        <th>Price</th>
                                        <th>
                                            <button type="button" class="btn btn-sm btn-success"
                                                id="addServiceRow">+</button>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><input type="text" name="services[0][service_name]" class="form-control"
                                                required>
                                        </td>
                                        <td><input type="number" name="services[0][price]" class="form-control"
                                                required>
                                        </td>
                                        <td><button type="button"
                                                class="btn btn-sm btn-danger removeServiceRow">x</button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Save Transaction</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Payment Modal -->
    <div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form id="paymentForm" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="transaction_id" value="">
                <input type="hidden" name="snap_token" value="">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Choose Payment Method</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Payment Method</label>
                            <select name="payment_method" class="form-control" id="payment_method" required>
                                <option value="cash">Cash</option>
                                <option value="gateway">Transfer / QRIS (Midtrans)</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" id="btn-pay-submit">Pay</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Cancel -->
    <div class="modal fade" id="cancelModal" tabindex="-1" aria-labelledby="cancelModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="cancelForm" method="POST" action="">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title" id="cancelModalLabel">Cancel Transaction</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to cancel this transaction?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</button>
                        <button type="submit" class="btn btn-danger">Yes, Cancel</button>
                    </div>
                </form>
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

            // Tambah baris layanan
            let serviceIndex = 1;
            $('#addServiceRow').on('click', function() {
                $('#serviceTable tbody').append(`
                <tr>
                    <td><input type="text" name="services[${serviceIndex}][service_name]" class="form-control" required></td>
                    <td><input type="number" name="services[${serviceIndex}][price]" class="form-control" required></td>
                    <td><button type="button" class="btn btn-sm btn-danger removeServiceRow">x</button></td>
                </tr>
            `);
                serviceIndex++;
            });

            // Hapus baris layanan
            $(document).on('click', '.removeServiceRow', function() {
                $(this).closest('tr').remove();
            });
        });

        function validatePhone(input) {
            // Remove any non-digit characters
            let value = input.value.replace(/\D/g, '');

            // Remove leading zero if present
            if (value.startsWith('0')) {
                value = value.substring(1);
            }

            // Update input value
            input.value = value;
        }
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const clientTypeSelect = document.getElementById('clientType');
            const memberSection = document.getElementById('memberSection');
            const nonMemberSection = document.getElementById('nonMemberSection');
            const searchMemberBtn = document.getElementById('searchMemberBtn');
            const memberDetails = document.getElementById('memberDetails');
            const formAddTransaction = document.getElementById('formAddTransaction');

            let memberIdInput = null; // Untuk menyimpan input hidden member_id

            // Saat memilih Member / Non-Member
            clientTypeSelect.addEventListener('change', function() {
                if (this.value === 'member') {
                    memberSection.classList.remove('d-none');
                    nonMemberSection.classList.add('d-none');

                    // Hapus required dari customer_name kalau ada
                    document.getElementById('customerName').removeAttribute('required');
                } else if (this.value === 'non-member') {
                    nonMemberSection.classList.remove('d-none');
                    memberSection.classList.add('d-none');

                    // Kasih required ke customer_name
                    document.getElementById('customerName').setAttribute('required', 'required');

                    // Kalau sebelumnya ada memberIdInput, hapus
                    if (memberIdInput) {
                        memberIdInput.remove();
                        memberIdInput = null;
                    }
                } else {
                    memberSection.classList.add('d-none');
                    nonMemberSection.classList.add('d-none');

                    // Reset required
                    document.getElementById('customerName').removeAttribute('required');
                }
            });

            // Saat klik button Search Member
            searchMemberBtn.addEventListener('click', function() {
                const phone = document.getElementById('memberPhone').value.trim();
                if (phone === '') {
                    alert('Please input phone number first.');
                    return;
                }

                fetch(`/get-member-by-number/${phone}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Tampilkan detail member
                            memberDetails.innerHTML = `
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title mb-1">${data.member.name}</h5>
                                        <p class="card-text mb-1"><strong>Email:</strong> ${data.member.email}</p>
                                        <p class="card-text"><strong>Phone:</strong> ${data.member.phone}</p>
                                    </div>
                                </div>
                            `;

                            // Tambahkan input hidden untuk member_id
                            if (!memberIdInput) {
                                memberIdInput = document.createElement('input');
                                memberIdInput.type = 'hidden';
                                memberIdInput.name = 'member_id';
                                formAddTransaction.appendChild(memberIdInput);
                            }
                            memberIdInput.value = data.member.id;
                            // console.log('Member ID:', memberIdInput.value);

                        } else {
                            memberDetails.innerHTML = `<div class="text-danger">${data.message}</div>`;

                            // Hapus memberIdInput jika tidak ditemukan
                            if (memberIdInput) {
                                memberIdInput.remove();
                                memberIdInput = null;
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching member:', error);
                        memberDetails.innerHTML =
                            `<div class="text-danger">Failed to fetch member data.</div>`;

                        // Hapus memberIdInput saat error
                        if (memberIdInput) {
                            memberIdInput.remove();
                            memberIdInput = null;
                        }
                    });
            });

            // Saat submit form
            formAddTransaction.addEventListener('submit', function(e) {
                if (clientTypeSelect.value === 'member') {
                    if (!memberIdInput || memberIdInput.value.trim() === '') {
                        e.preventDefault();
                        alert('Member masih kosong! Silakan cari member terlebih dahulu.');
                    }
                }
                // Untuk non-member, required customer_name sudah dihandle via attribute
            });
        });

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

            // Handle Cancel Button
            $('.btn-cancel').on('click', function() {
                var id = $(this).data('id');
                // Set action form ke route cancel
                $('#cancelForm').attr('action', '/cancel-transaction/' + id);
                $('#cancelModal').modal('show');
            });

            $(document).ready(function() {
                var table_success = $('#table-success').DataTable({
                    dom: 'Bfrtip',
                    buttons: [{
                        extend: 'excel',
                        text: 'Export to Excel',
                        className: 'btn btn-info mb-3'
                    }],
                    order: [
                        [3, 'desc']
                    ], // Sort default by Transaction Time
                });

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
                let id = $(this).data('id');
                let snap_token = $(this).data('snaptoken');
                $('#paymentModal input[name="transaction_id"]').val(id);
                $('#paymentModal input[name="snap_token"]').val(snap_token);
                $('#paymentModal').modal('show');
            });

            $('#btn-pay-submit').on('click', function() {
                let method = $('#payment_method').val();
                let snapToken = $('input[name="snap_token"]').val();
                let transactionId = $('input[name="transaction_id"]').val();

                if (method === 'cash') {
                    // Submit form secara langsung
                    $('#paymentForm').attr('action', '/pay-transaction').submit();
                } else if (method === 'gateway') {
                    // Jalankan Midtrans Snap
                    snap.pay(snapToken, {
                        onSuccess: function(result) {
                            // Simpan hasil ke server, atau redirect jika perlu
                            console.log('Success:', result);
                            sendPaymentResult(result, transactionId);
                        },
                        onPending: function(result) {
                            console.log('Pending:', result);
                            sendPaymentResult(result, transactionId);
                        },
                        onError: function(result) {
                            console.log('Error:', result);
                            alert('Payment failed. Please try again.');
                        }
                    });
                }
            });

            function sendPaymentResult(result, transactionId) {
                // Kirim hasil ke backend via AJAX
                $.ajax({
                    url: '/pay-transaction',
                    method: 'PUT',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    data: {
                        // _method: 'PUT',
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
                        }, 2000); // tunggu 2 detik sebelum reload
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
