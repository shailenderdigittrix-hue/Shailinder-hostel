@extends('backend.layouts.master')

@section('content')
<div class="container mt-4">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="mb-0">SMTP Settings</h4>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.updateSMTP', $smtp->id) }}" method="POST">
                @csrf

                <!-- <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">MAIL_MAILER</label>
                        <input type="text" name="mailer" class="form-control" value="{{ old('mailer', $smtp->mailer ?? 'log') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">MAIL_SCHEME</label>
                        <input type="text" name="scheme" class="form-control" value="{{ old('scheme', $smtp->scheme ?? '') }}">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">MAIL_HOST</label>
                        <input type="text" name="host" class="form-control" value="{{ old('host', $smtp->host ?? '127.0.0.1') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">MAIL_PORT</label>
                        <input type="number" name="port" class="form-control" value="{{ old('port', $smtp->port ?? 2525) }}">
                    </div>
                </div> -->

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">MAIL_USERNAME</label>
                        <input 
                            type="text" 
                            name="username" 
                            class="form-control" 
                            value="{{ old('username', $smtp->username ?? '') }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">MAIL_PASSWORD</label>
                        <div class="input-group">
                            <input 
                                type="password" 
                                name="password" 
                                id="mailPassword" 
                                class="form-control" 
                                value="{{ old('password', $smtp->password ?? '') }}">
                            <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                <i data-feather="eye"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">MAIL_FROM_ADDRESS</label>
                        <input type="email" name="from_address" class="form-control" value="{{ old('from_address', $smtp->from_address ?? '') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">MAIL_FROM_NAME</label>
                        <input type="text" name="from_name" class="form-control" value="{{ old('from_name', $smtp->from_name ?? '') }}">
                    </div>
                </div>

                <!-- <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Encryption</label>
                        <input type="text" name="encryption" class="form-control" value="{{ old('encryption', $smtp->encryption ?? '') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="active" {{ old('status', $smtp->status ?? '') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status', $smtp->status ?? '') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                </div> -->

                <div class="text-end">
                    <button type="submit" class="btn btn-success px-4">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const passwordInput = document.getElementById('mailPassword');
        const toggleButton = document.getElementById('togglePassword');
        const icon = toggleButton.querySelector('i');

        toggleButton.addEventListener('click', function () {
            const isHidden = passwordInput.getAttribute('type') === 'password';
            passwordInput.setAttribute('type', isHidden ? 'text' : 'password');
            icon.setAttribute('data-feather', isHidden ? 'eye-off' : 'eye');
            feather.replace(); // refresh Feather icons
        });
    });
</script>
@endsection
