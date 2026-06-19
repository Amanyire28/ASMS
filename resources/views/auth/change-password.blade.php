<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Change Password - {{ config('app.name', 'ASMS') }}</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        maroon: {
                            DEFAULT: '#800000',
                            dark: '#5f0000',
                            light: '#b34d4d'
                        },
                    },
                },
            },
        };
    </script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

    <style>
        body {
            background: linear-gradient(135deg, #1e1b4b 0%, #312e81 50%, #3b0764 100%);
            height: 100vh;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            position: relative;
            /* overflow: hidden; */
        }

        /* Simplified background shapes */
        body::before {
            content: '';
            position: absolute;
            width: 300px;
            height: 300px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.05);
            top: -100px;
            left: -100px;
            animation: float 15s infinite ease-in-out;
        }

        @keyframes float {
            0%, 100% { transform: translate(0, 0) scale(1); }
            50% { transform: translate(30px, 30px) scale(1.1); }
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }

        .password-strength-bar {
            height: 3px;
            background: #e5e7eb;
            border-radius: 2px;
            overflow: hidden;
            margin-top: 0.25rem;
        }

        .password-strength-fill {
            height: 100%;
            transition: all 0.4s ease;
            border-radius: 2px;
        }

        .animate-shake {
            animation: shake 0.5s;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            10%, 30%, 50%, 70%, 90% { transform: translateX(-4px); }
            20%, 40%, 60%, 80% { transform: translateX(4px); }
        }

        .input-field {
            transition: all 0.3s ease;
        }

        .input-field:focus {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(128, 0, 0, 0.12);
        }

        .btn-submit {
            transition: all 0.3s ease;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(128, 0, 0, 0.4);
        }

        /* Compact password requirements */
        .requirements-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 0.5rem;
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
        }
    </style>
</head>
<body>
    <div class="w-full max-w-md relative z-10" style="max-height: calc(100vh - 2rem);">
        <!-- Card -->
        <div class="glass-card rounded-2xl overflow-hidden mb-2">
            <!-- Compact Header -->
            <div class="bg-gradient-to-br from-maroon via-maroon-dark to-maroon p-4 text-white text-center relative">
                <div class="relative z-10">

                    <h1 class="text-2xl font-bold mb-1">Change Password</h1>

                </div>
            </div>

            <!-- Form Section -->
            <div class="p-6">
                <!-- Alert Messages - Compact -->
                @if(auth()->user()->must_change_password)
                    <div class="mb-3">
                        <div class="bg-gradient-to-r from-amber-50 to-orange-50 border-l-3 border-amber-400 p-3 rounded-lg">
                            <div class="flex items-start">
                                <i class="fas fa-exclamation-triangle text-amber-500 text-sm mt-0.5"></i>
                                <div class="ml-2">
                                    <p class="text-xs font-bold text-amber-900">Password Change Required</p>
                                    <p class="text-xs text-amber-700 mt-0.5">Update your default password to continue.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                @if(session('warning'))
                    <div class="mb-3">
                        <div class="bg-gradient-to-r from-amber-50 to-orange-50 border-l-3 border-amber-400 p-3 rounded-lg">
                            <div class="flex items-center">
                                <i class="fas fa-exclamation-triangle text-amber-500 text-sm"></i>
                                <p class="ml-2 text-xs text-amber-800 font-medium">{{ session('warning') }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-3 animate-shake">
                        <div class="bg-gradient-to-r from-red-50 to-rose-50 border-l-3 border-red-400 p-3 rounded-lg">
                            <div class="flex items-center">
                                <i class="fas fa-times-circle text-red-500 text-sm"></i>
                                <p class="ml-2 text-xs text-red-800 font-medium">{{ session('error') }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Form -->
                <form method="POST" action="{{ route('password.change.update') }}" id="passwordForm" class="space-y-2">
                    @csrf

                    <!-- Current Password -->
                    <div>
                        <label for="current_password" class="block text-xs font-bold text-gray-700 mb-1">
                            Current Password
                        </label>
                        <div class="relative">
                            <div class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                                <i class="fas fa-key text-sm"></i>
                            </div>
                            <input type="password"
                                   name="current_password"
                                   id="current_password"
                                   required
                                   class="input-field w-full pl-10 pr-10 py-2.5 text-sm border-2 border-gray-200 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon transition-all outline-none"
                                   placeholder="Current password">
                            <button type="button"
                                    onclick="togglePassword('current_password')"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-maroon transition">
                                <i class="fas fa-eye text-sm" id="current_password_icon"></i>
                            </button>
                        </div>
                        @error('current_password')
                            <p class="mt-1 text-xs text-red-600 flex items-center">
                                <i class="fas fa-exclamation-circle mr-1 text-xs"></i>{{ $message }}
                            </p>
                        @enderror
                        <div class="mt-1 bg-blue-50 border border-blue-200 rounded p-2 flex items-start">
                            <i class="fas fa-info-circle text-blue-500 text-xs mt-0.5 mr-1.5"></i>
                            <div class="text-xs text-blue-700">
                                <span class="font-semibold">Default:</span>
                                <span class="ml-1 font-mono bg-blue-100 px-1.5 py-0.5 rounded">ASMS@<?php echo date('Y'); ?></span>
                            </div>
                        </div>
                    </div>

                    <!-- New Password -->
                    <div>
                        <label for="password" class="block text-xs font-bold text-gray-700 mb-1">
                            New Password
                        </label>
                        <div class="relative">
                            <div class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                                <i class="fas fa-lock text-sm"></i>
                            </div>
                            <input type="password"
                                   name="password"
                                   id="password"
                                   required
                                   class="input-field w-full pl-10 pr-10 py-2.5 text-sm border-2 border-gray-200 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon transition-all outline-none"
                                   placeholder="Create strong password"
                                   oninput="checkPasswordStrength()">
                            <button type="button"
                                    onclick="togglePassword('password')"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-maroon transition">
                                <i class="fas fa-eye text-sm" id="password_icon"></i>
                            </button>
                        </div>

                        <!-- Password Strength Indicator -->
                        <div class="password-strength-bar">
                            <div id="strength-fill" class="password-strength-fill" style="width: 0%; background: #e5e7eb;"></div>
                        </div>
                        <p id="strength-text" class="mt-0.5 text-xs font-bold"></p>

                        @error('password')
                            <p class="mt-1 text-xs text-red-600 flex items-center">
                                <i class="fas fa-exclamation-circle mr-1 text-xs"></i>{{ $message }}
                            </p>
                        @enderror

                        <!-- Compact Password Requirements -->
                        <div class="mt-2 bg-gray-50 rounded p-2 border border-gray-200">
                            <p class="text-xs font-bold text-gray-700 mb-1.5">Must contain:</p>
                            <div class="requirements-grid">
                                <div class="flex items-center text-xs" id="req-length">
                                    <i class="fas fa-circle text-gray-300 mr-1.5 text-[5px]"></i>
                                    <span class="text-gray-600">8+ chars</span>
                                </div>
                                <div class="flex items-center text-xs" id="req-uppercase">
                                    <i class="fas fa-circle text-gray-300 mr-1.5 text-[5px]"></i>
                                    <span class="text-gray-600">Uppercase</span>
                                </div>
                                <div class="flex items-center text-xs" id="req-lowercase">
                                    <i class="fas fa-circle text-gray-300 mr-1.5 text-[5px]"></i>
                                    <span class="text-gray-600">Lowercase</span>
                                </div>
                                <div class="flex items-center text-xs" id="req-number">
                                    <i class="fas fa-circle text-gray-300 mr-1.5 text-[5px]"></i>
                                    <span class="text-gray-600">Number</span>
                                </div>
                                <div class="flex items-center text-xs" id="req-special">
                                    <i class="fas fa-circle text-gray-300 mr-1.5 text-[5px]"></i>
                                    <span class="text-gray-600">Special char</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label for="password_confirmation" class="block text-xs font-bold text-gray-700 mb-1">
                            Confirm Password
                        </label>
                        <div class="relative">
                            <div class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                                <i class="fas fa-shield-alt text-sm"></i>
                            </div>
                            <input type="password"
                                   name="password_confirmation"
                                   id="password_confirmation"
                                   required
                                   class="input-field w-full pl-10 pr-10 py-2.5 text-sm border-2 border-gray-200 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon transition-all outline-none"
                                   placeholder="Confirm password">
                            <button type="button"
                                    onclick="togglePassword('password_confirmation')"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-maroon transition">
                                <i class="fas fa-eye text-sm" id="password_confirmation_icon"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit"
                            class="btn-submit w-full bg-gradient-to-r from-maroon via-maroon-dark to-maroon text-white font-bold py-2.5 rounded-lg shadow flex items-center justify-center space-x-2 mt-4">
                        <i class="fas fa-check-circle text-sm"></i>
                        <span class="text-sm">Update Password</span>
                    </button>
                </form>

                <!-- Compact Footer -->
                <div class="mt-4 text-center ">
                    <p class="text-xs text-gray-500 flex items-center justify-center">
                        <i class="fas fa-shield-alt mr-1.5 text-maroon text-xs"></i>
                        End-to-end encrypted & securely stored
                    </p>
                </div>
            </div>
        </div>


    </div>

    <!-- JavaScript -->
    <script>
        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const icon = document.getElementById(fieldId + '_icon');

            if (field.type === 'password') {
                field.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                field.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        }

        function checkPasswordStrength() {
            const password = document.getElementById('password').value;
            const strengthFill = document.getElementById('strength-fill');
            const strengthText = document.getElementById('strength-text');

            let strength = 0;
            const hasLength = password.length >= 8;
            const hasUppercase = /[A-Z]/.test(password);
            const hasLowercase = /[a-z]/.test(password);
            const hasNumber = /[0-9]/.test(password);
            const hasSpecial = /[^A-Za-z0-9]/.test(password);

            updateRequirement('req-length', hasLength);
            updateRequirement('req-uppercase', hasUppercase);
            updateRequirement('req-lowercase', hasLowercase);
            updateRequirement('req-number', hasNumber);
            updateRequirement('req-special', hasSpecial);

            if (hasLength) strength += 20;
            if (hasUppercase) strength += 20;
            if (hasLowercase) strength += 20;
            if (hasNumber) strength += 20;
            if (hasSpecial) strength += 20;

            strengthFill.style.width = strength + '%';

            if (strength === 0) {
                strengthFill.style.background = '#e5e7eb';
                strengthText.textContent = '';
            } else if (strength <= 40) {
                strengthFill.style.background = '#ef4444';
                strengthText.textContent = 'Weak password';
                strengthText.className = 'mt-0.5 text-xs font-bold text-red-600';
            } else if (strength <= 60) {
                strengthFill.style.background = '#f59e0b';
                strengthText.textContent = 'Fair password';
                strengthText.className = 'mt-0.5 text-xs font-bold text-orange-600';
            } else if (strength <= 80) {
                strengthFill.style.background = '#3b82f6';
                strengthText.textContent = 'Good password';
                strengthText.className = 'mt-0.5 text-xs font-bold text-blue-600';
            } else {
                strengthFill.style.background = '#10b981';
                strengthText.textContent = 'Strong password';
                strengthText.className = 'mt-0.5 text-xs font-bold text-green-600';
            }
        }

        function updateRequirement(id, met) {
            const element = document.getElementById(id);
            const icon = element.querySelector('i');
            if (met) {
                icon.className = 'fas fa-check-circle text-green-500 mr-1.5 text-xs';
                element.querySelector('span').className = 'text-green-600 font-semibold';
            } else {
                icon.className = 'fas fa-circle text-gray-300 mr-1.5 text-[5px]';
                element.querySelector('span').className = 'text-gray-600';
            }
        }

        document.getElementById('passwordForm').addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const confirmation = document.getElementById('password_confirmation').value;

            if (password !== confirmation) {
                e.preventDefault();
                alert('⚠️ Passwords do not match!');
                document.getElementById('password_confirmation').focus();
                document.getElementById('password_confirmation').classList.add('border-red-500');
                setTimeout(() => {
                    document.getElementById('password_confirmation').classList.remove('border-red-500');
                }, 3000);
            }
        });
    </script>
</body>
</html>
