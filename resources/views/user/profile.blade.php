@extends('layouts.master')

@section('title')
    Profile
@endsection

@section('content')
    <div class="page-inner">
        <div class="d-flex align-items-start flex-column flex-md-row pt-2 pb-4">
            <div>
                <h3 class="fw-bold mb-3">Profile</h3>
                <h6 class="text-muted mb-2">Manage Your Profile</h6>
            </div>
        </div>

        <div class="card">
            <div class="card-body row">
                <div class="col-md-3 text-center">
                    <img src="{{ asset('assets_kaiadmin/img/profile.png') }}" class="rounded-circle mb-3" width="150"
                        alt="Profile Picture">
                    <h5>{{ Auth::user()->name }}</h5>
                    <p class="text-muted">{{ Auth::user()->email }}</p>
                </div>
                <div class="col-md-9">
                    <form id="profileForm" action="/update-profile" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="inputEmail4" class="form-label">Email</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                id="inputEmail4" value="{{ old('email', Auth::user()->email) }}" placeholder="mail@mail.com"
                                disabled>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" name="name" id="name"
                                    class="form-control @error('name') is-invalid @enderror"
                                    value="{{ old('name', Auth::user()->name) }}" placeholder="John Doe" disabled>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="phone_number" class="form-label">Phone Number</label>
                                <div class="input-group">
                                    <span class="input-group-text">+62</span>
                                    <input type="text" name="phone_number" id="phone_number"
                                        class="form-control @error('phone_number') is-invalid @enderror"
                                        placeholder="81234567890"
                                        value="{{ old('phone_number', ltrim(Auth::user()->phone_number, '+62')) }}"
                                        oninput="validatePhone(this)" disabled>
                                    @error('phone_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 mb-6">
                                <label for="address" class="form-label">Address</label>
                                <textarea name="address" id="address" class="form-control @error('address') is-invalid @enderror" rows="4"
                                    disabled>{{ old('address', Auth::user()->address) }}</textarea>
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="btn-group mt-3">
                            <button type="button" class="btn btn-primary" id="editBtn">Update Profile</button>
                            <button type="button" class="btn btn-secondary" id="changePasswordBtn" data-bs-toggle="modal"
                                data-bs-target="#changePasswordModal">Change Password</button>
                            <button type="submit" class="btn btn-success d-none" id="saveBtn">Save</button>
                            <button type="button" class="btn btn-danger d-none" id="cancelBtn">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal Update Password -->
        <div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="/change-password" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="modal-header">
                            <h5 class="modal-title" id="changePasswordModalLabel">Change Password</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>

                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="old_password" class="form-label">Old Password</label>
                                <input type="password" class="form-control @error('old_password') is-invalid @enderror"
                                    id="old_password" name="old_password" required>
                                @error('old_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="new_password" class="form-label">New Password</label>
                                <input type="password" class="form-control @error('new_password') is-invalid @enderror"
                                    id="new_password" name="new_password" required>
                                @error('new_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="new_password_confirmation" class="form-label">Confirm New Password</label>
                                <input type="password" class="form-control" id="new_password_confirmation"
                                    name="new_password_confirmation" required>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Change Password</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('profileForm');
            const editBtn = document.getElementById('editBtn');
            const saveBtn = document.getElementById('saveBtn');
            const cancelBtn = document.getElementById('cancelBtn');
            const changePasswordBtn = document.getElementById('changePasswordBtn');
            const formInputs = form.querySelectorAll('input, textarea');

            function toggleEditMode(enabled) {
                formInputs.forEach(input => input.disabled = !enabled);
                editBtn.classList.toggle('d-none', enabled);
                changePasswordBtn.classList.toggle('d-none', enabled);
                saveBtn.classList.toggle('d-none', !enabled);
                cancelBtn.classList.toggle('d-none', !enabled);
            }

            editBtn.addEventListener('click', () => toggleEditMode(true));
            cancelBtn.addEventListener('click', () => {
                form.reset();
                toggleEditMode(false);
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
