@extends('layouts.master')

@section('title')
    Manage Member
@endsection

@section('content')
    <div class="page-inner">
        <div class="d-flex align-items-start flex-column flex-md-row pt-2 pb-4">
            <div>
                <h3 class="fw-bold mb-3">Manage Member</h3>
                <h6 class="text-muted mb-2">Manage all your members here</h6>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <!-- Button Add Member -->
                <div class="mb-3">
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addMemberModal">
                        <i class="fa fa-plus"></i> Add Member
                    </button>
                </div>

                <!-- Table Member -->
                <div class="table-responsive">
                    <table id="memberTable" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Phone Number</th>
                                <th>Email</th>
                                <th>Address</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Hardcode sample data -->
                            @foreach ($members as $item)
                                <tr>
                                    <td>{{ $item->name }}</td>
                                    <td>{{ $item->phone_number }}</td>
                                    <td>{{ $item->email }}</td>
                                    <td>{{ $item->address }}</td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-warning edit-btn" data-bs-toggle="modal"
                                            data-bs-target="#updateMemberModal" data-id="{{ $item->id }}"
                                            data-name="{{ $item->name }}" data-phone="{{ $item->phone_number }}"
                                            data-email="{{ $item->email }}" data-address="{{ $item->address }}">
                                            <i class="fa fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger delete-btn" data-bs-toggle="modal"
                                            data-bs-target="#deleteConfirmModal" data-id="{{ $item->id }}"
                                            data-name="{{ $item->name }}">
                                            <i class="fa fa-trash"></i>
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

    <!-- Modal Add Member -->
    <div class="modal fade" id="addMemberModal" tabindex="-1" aria-labelledby="addMemberModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="addMemberModalLabel">Add Member</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addMemberForm" method="POST" action="{{ route('members.store') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="memberName" class="form-label">Name</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                id="memberName" placeholder="Enter name" value="{{ old('name') }}">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="phone_number" class="form-label">Phone Number</label>
                            <div class="input-group">
                                <span class="input-group-text">+62</span>
                                <input type="text" name="phone_number" id="phone_number"
                                    class="form-control @error('phone_number') is-invalid @enderror"
                                    placeholder="81234567890" value="{{ old('phone_number') }}"
                                    oninput="validatePhone(this)">
                                @error('phone_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="memberEmail" class="form-label">Email</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                id="memberEmail" placeholder="Enter email" value="{{ old('email') }}">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="memberAddress" class="form-label">Address</label>
                            <textarea name="address" class="form-control @error('address') is-invalid @enderror" id="memberAddress" rows="2"
                                placeholder="Enter address">{{ old('address') }}</textarea>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <small class="text-muted">* Password default akan diatur menjadi <strong>Qwerty123*</strong></small>

                        <div class="d-grid gap-2 mt-3">
                            <button type="submit" class="btn btn-primary">Save Member</button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Update Member -->
    <div class="modal fade" id="updateMemberModal" tabindex="-1" aria-labelledby="updateMemberModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="updateMemberModalLabel">Update Member</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="updateMemberForm" method="POST" action="{{ route('members.update') }}">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="id" id="updateMemberId">

                        <div class="mb-3">
                            <label for="updateMemberName" class="form-label">Name</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                id="updateMemberName">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="updateMemberPhone" class="form-label">Phone Number</label>
                            <div class="input-group">
                                <span class="input-group-text">+62</span>
                                <input type="text" name="phone_number" id="updateMemberPhone"
                                    class="form-control @error('phone_number') is-invalid @enderror"
                                    placeholder="81234567890" oninput="validatePhone(this)">
                                @error('phone_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="updateMemberEmail" class="form-label">Email</label>
                            <input type="email" name="email"
                                class="form-control @error('email') is-invalid @enderror" id="updateMemberEmail">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="updateMemberAddress" class="form-label">Address</label>
                            <textarea name="address" class="form-control @error('address') is-invalid @enderror" id="updateMemberAddress"
                                rows="2"></textarea>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-warning">Update Member</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

    <!-- Modal Confirm Delete -->
    <div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-labelledby="deleteConfirmModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="deleteMemberForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="modal-body text-center">
                        <h5>Are you sure you want to delete <span id="deleteMemberName" class="fw-bold"></span>?</h5>
                        <div class="d-flex justify-content-center mt-4">
                            <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-danger">Yes, Delete</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#memberTable').DataTable();

            // Event klik edit button
            $('.edit-btn').on('click', function() {
                var id = $(this).data('id');
                var name = $(this).data('name');
                var phone = $(this).data('phone');
                var email = $(this).data('email');
                var address = $(this).data('address');

                $('#updateMemberId').val(id);
                $('#updateMemberName').val(name);
                $('#updateMemberPhone').val(phone);
                $('#updateMemberEmail').val(email);
                $('#updateMemberAddress').val(address);
            });

            // Event klik delete button
            $('.delete-btn').on('click', function() {
                var id = $(this).data('id');
                var name = $(this).data('name');

                $('#deleteMemberName').text(name);

                // Ganti action form delete ke id yang dipilih
                $('#deleteMemberForm').attr('action', '/members/' + id);
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
@endpush
