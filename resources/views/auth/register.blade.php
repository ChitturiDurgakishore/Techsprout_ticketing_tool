<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - TicketFlow</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <script src="{{ asset('js/app.js') }}" defer></script>
</head>

<body>
    <div class="auth-wrapper">
        <div class="auth-card">
            <div class="auth-header">
                <span class="auth-logo">🎫</span>
                <h1 class="auth-title">Create Account</h1>
                <p class="auth-subtitle">Join the TicketFlow team</p>
            </div>

            @if ($errors->any())
                <div class="alert alert-error">
                    Please correct the errors in the form.
                </div>
            @endif

            <form method="POST" action="/register">
                @csrf

                <div class="form-group">
                    <label for="name">Full Name</label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        placeholder="e.g. John Doe"
                        value="{{ old('name') }}"
                        required
                        autofocus
                    >
                    @error('name')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        placeholder="you@company.com"
                        value="{{ old('email') }}"
                        required
                    >
                    @error('email')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="••••••••"
                        required
                    >
                    @error('password')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="role">Team Role</label>
                    <select name="role" id="role" required>
                        <option value="">Select Role</option>
                        <option value="developer" {{ old('role') === 'developer' ? 'selected' : '' }}>Developer</option>
                        <option value="tester" {{ old('role') === 'tester' ? 'selected' : '' }}>QA / Tester</option>
                        <option value="seo" {{ old('role') === 'seo' ? 'selected' : '' }}>SEO Specialist</option>
                        <option value="user" {{ old('role') === 'user' ? 'selected' : '' }}>Standard User</option>
                    </select>
                    @error('role')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 12px; margin-top: 8px;">
                    Create Account
                </button>
            </form>

            <div class="auth-footer">
                Already have an account?
                <a href="/">Sign In</a>
            </div>
        </div>
    </div>
</body>

</html>

