@section('title', 'School Profile Settings')
@section('page-title', 'School Profile Settings')
@section('page-description', 'Configure your ASMS ')

@extends('layouts.app')

@section('content')
<div x-data="schoolProfileSettings()" class="max-w-5xl mx-auto px-2 sm:px-4 lg:px-4 ">



    @if(session('error'))
    <div class="mb-6 bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 p-4 rounded-lg">
        <div class="flex items-center">
            <i class="fas fa-exclamation-circle text-red-500 mr-3"></i>
            <p class="text-red-700 dark:text-red-300">{{ session('error') }}</p>
        </div>
    </div>
    @endif

    <!-- Settings Card -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden">

        <!-- Tab Navigation -->
        <div class="border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
            <nav class="flex overflow-x-auto" aria-label="Tabs">
                <button @click="activeTab = 'basic'"
                        :class="activeTab === 'basic' ? 'border-maroon text-maroon dark:text-maroon-light' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300'"
                        class="whitespace-nowrap py-3 px-4 sm:py-4 sm:px-6 border-b-2 font-medium text-sm transition-all duration-200">
                    <i class="fas fa-school mr-2"></i>
                    Basic
                </button>

                <button @click="activeTab = 'contact'"
                        :class="activeTab === 'contact' ? 'border-maroon text-maroon dark:text-maroon-light' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300'"
                        class="whitespace-nowrap py-3 px-4 sm:py-4 sm:px-6 border-b-2 font-medium text-sm transition-all duration-200">
                    <i class="fas fa-address-book mr-2"></i>
                    Contact
                </button>

                <button @click="activeTab = 'academic'"
                        :class="activeTab === 'academic' ? 'border-maroon text-maroon dark:text-maroon-light' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300'"
                        class="whitespace-nowrap py-3 px-4 sm:py-4 sm:px-6 border-b-2 font-medium text-sm transition-all duration-200">
                    <i class="fas fa-calendar-alt mr-2"></i>
                    Academic
                </button>

                <button @click="activeTab = 'email'"
                        :class="activeTab === 'email' ? 'border-maroon text-maroon dark:text-maroon-light' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300'"
                        class="whitespace-nowrap py-3 px-4 sm:py-4 sm:px-6 border-b-2 font-medium text-sm transition-all duration-200">
                    <i class="fas fa-envelope mr-2"></i>
                    Email
                </button>

                <button @click="activeTab = 'report'"
                        :class="activeTab === 'report' ? 'border-maroon text-maroon dark:text-maroon-light' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300'"
                        class="whitespace-nowrap py-3 px-4 sm:py-4 sm:px-6 border-b-2 font-medium text-sm transition-all duration-200">
                    <i class="fas fa-file-alt mr-2"></i>
                    Report
                </button>

                <button @click="activeTab = 'admission'"
                        :class="activeTab === 'admission' ? 'border-maroon text-maroon dark:text-maroon-light' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300'"
                        class="whitespace-nowrap py-3 px-4 sm:py-4 sm:px-6 border-b-2 font-medium text-sm transition-all duration-200">
                    <i class="fas fa-file-contract mr-2"></i>
                    Admission Letters
                </button>
            </nav>
        </div>

        <!-- Tab Content -->
        <div class="p-2 sm:p-6 lg:p-6 max-w-full overflow-hidden">

            <!-- BASIC INFORMATION TAB -->
            <div x-show="activeTab === 'basic'" x-transition>
                    <div class="space-y-4 sm:space-y-6">
                        <h3 class="text-base sm:text-lg font-semibold text-gray-900 dark:text-white mb-3 sm:mb-4">
                            <i class="fas fa-info-circle text-maroon mr-2"></i>
                            Basic Information
                        </h3>

                        <!-- School Logo -->

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                School Logo
                            </label>

                             <form action="{{ route('settings.delete-logo') }}" method="POST"  class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class=" my-3 px-2 py-2.5 sm:px-6 sm:py-3 bg-gradient-to-r from-maroon to-maroon-dark text-white font-semibold text-sm sm:text-base rounded-lg hover:shadow-lg transition-all duration-200 flex items-cente">
                                           Delete Logo
                                        </button>
                                </form>


                                <form action="{{ route('settings.update-basic-info') }}" method="POST" enctype="multipart/form-data" >
                                @csrf

                            <div class="flex flex-col sm:flex-row sm:items-center sm:space-x-4 space-y-3 sm:space-y-0">
                                @if($basicSettings['school_logo'] ?? null)
                                    @php
                                        $logoPath = $basicSettings['school_logo'];
                                        // Clean up path just in case
                                        $logoPath = str_replace('storage/app/public/', '', $logoPath);
                                        $logoPath = ltrim($logoPath, '/\\');
                                    @endphp

                                    <img src="{{ asset('storage/' . $logoPath) }}"
                                        alt="School Logo"
                                        class="w-20 h-20 sm:w-24 sm:h-24 object-contain rounded-lg border-2 border-gray-200 dark:border-gray-700 p-2">


                                @else
                                    <div class="w-20 h-20 sm:w-24 sm:h-24 bg-gray-100 dark:bg-gray-700 rounded-lg border-2 border-dashed border-gray-300 dark:border-gray-600 flex items-center justify-center">
                                        <i class="fas fa-image text-gray-400 text-xl sm:text-2xl"></i>
                                    </div>
                                @endif
                            </div>

                            <input type="file"
                                name="school_logo"
                                accept="image/png,image/jpeg,image/jpg"
                                class="mt-2 block w-full text-sm text-gray-500 file:mr-2 file:py-1.5 file:px-3 sm:file:mr-4 sm:file:py-2 sm:file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-maroon file:text-white hover:file:bg-maroon-dark">
                            <p class="mt-1 text-xs sm:text-sm text-gray-500">PNG, JPG up to 2MB. Recommended: 200x200px</p>
                            @error('school_logo')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>


                        <!-- School Name -->
                        <div>
                            <label for="school_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                School Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text"
                                   name="school_name"
                                   id="school_name"
                                   value="{{ old('school_name', $basicSettings['school_name'] ?? '') }}"
                                   required
                                   class="w-full px-3 sm:px-4 py-2 text-sm sm:text-base border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-maroon focus:border-transparent dark:bg-gray-700 dark:text-white">
                            @error('school_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- School Motto -->
                        <div>
                            <label for="school_motto" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                School Motto/Tagline
                            </label>
                            <input type="text"
                                   name="school_motto"
                                   id="school_motto"
                                   value="{{ old('school_motto', $basicSettings['school_motto'] ?? '') }}"
                                   class="w-full px-3 sm:px-4 py-2 text-sm sm:text-base border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-maroon focus:border-transparent dark:bg-gray-700 dark:text-white">
                            @error('school_motto')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Registration Number -->
                        <div>
                            <label for="registration_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Registration/License Number
                            </label>
                            <input type="text"
                                   name="registration_number"
                                   id="registration_number"
                                   value="{{ old('registration_number', $basicSettings['registration_number'] ?? '') }}"
                                   class="w-full px-3 sm:px-4 py-2 text-sm sm:text-base border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-maroon focus:border-transparent dark:bg-gray-700 dark:text-white">
                            @error('registration_number')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Established Year -->
                        <div>
                            <label for="established_year" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Year Established
                            </label>
                            <input type="number"
                                   name="established_year"
                                   id="established_year"
                                   min="1900"
                                   max="{{ date('Y') }}"
                                   value="{{ old('established_year', $basicSettings['established_year'] ?? '') }}"
                                   class="w-full px-3 sm:px-4 py-2 text-sm sm:text-base border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-maroon focus:border-transparent dark:bg-gray-700 dark:text-white">
                            @error('established_year')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Save Button -->
                    <div class="mt-6 sm:mt-8 flex items-center justify-end">
                        <button type="submit"
                                class="px-4 py-2.5 sm:px-6 sm:py-3 bg-gradient-to-r from-maroon to-maroon-dark text-white font-semibold text-sm sm:text-base rounded-lg hover:shadow-lg transition-all duration-200 flex items-center">
                            <i class="fas fa-save mr-2"></i>
                            Save Changes
                        </button>
                    </div>
                </form>









            </div>

            <!-- CONTACT INFORMATION TAB -->
            <div x-show="activeTab === 'contact'" x-transition style="display: none;">
                <form action="{{ route('settings.update-contact-info') }}" method="POST" >
                    @csrf

                    <div class="space-y-4 sm:space-y-6">
                        <h3 class="text-base sm:text-lg font-semibold text-gray-900 dark:text-white mb-3 sm:mb-4">
                            <i class="fas fa-map-marker-alt text-maroon mr-2"></i>
                            Contact Information
                        </h3>

                        <!-- Physical Address -->
                        <div>
                            <label for="physical_address" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Physical Address <span class="text-red-500">*</span>
                            </label>
                            <textarea name="physical_address"
                                      id="physical_address"
                                      rows="3"
                                      required
                                      class="w-full px-3 sm:px-4 py-2 text-sm sm:text-base border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-maroon focus:border-transparent dark:bg-gray-700 dark:text-white">{{ old('physical_address', $contactSettings['physical_address'] ?? '') }}</textarea>
                            @error('physical_address')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
                            <!-- City -->
                            <div>
                                <label for="city" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    City/District <span class="text-red-500">*</span>
                                </label>
                                <input type="text"
                                       name="city"
                                       id="city"
                                       value="{{ old('city', $contactSettings['city'] ?? '') }}"
                                       required
                                       class="w-full px-3 sm:px-4 py-2 text-sm sm:text-base border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-maroon focus:border-transparent dark:bg-gray-700 dark:text-white">
                                @error('city')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Country -->
                            <div>
                                <label for="country" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Country <span class="text-red-500">*</span>
                                </label>
                                <select name="country"
                                        id="country"
                                        required
                                        class="w-full px-3 sm:px-4 py-2 text-sm sm:text-base border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-maroon focus:border-transparent dark:bg-gray-700 dark:text-white">
                                    @foreach($countries as $country)
                                        <option value="{{ $country }}" {{ (old('country', $contactSettings['country'] ?? '') == $country) ? 'selected' : '' }}>
                                            {{ $country }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('country')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
                            <!-- Primary Phone -->
                            <div>
                                <label for="phone_primary" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Primary Phone <span class="text-red-500">*</span>
                                </label>
                                <input type="tel"
                                       name="phone_primary"
                                       id="phone_primary"
                                       value="{{ old('phone_primary', $contactSettings['phone_primary'] ?? '') }}"
                                       required
                                       placeholder="+256700000000"
                                       class="w-full px-3 sm:px-4 py-2 text-sm sm:text-base border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-maroon focus:border-transparent dark:bg-gray-700 dark:text-white">
                                @error('phone_primary')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Secondary Phone -->
                            <div>
                                <label for="phone_secondary" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Secondary Phone
                                </label>
                                <input type="tel"
                                       name="phone_secondary"
                                       id="phone_secondary"
                                       value="{{ old('phone_secondary', $contactSettings['phone_secondary'] ?? '') }}"
                                       placeholder="+256700000000"
                                       class="w-full px-3 sm:px-4 py-2 text-sm sm:text-base border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-maroon focus:border-transparent dark:bg-gray-700 dark:text-white">
                                @error('phone_secondary')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
                            <!-- Official Email -->
                            <div>
                                <label for="official_email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Official Email <span class="text-red-500">*</span>
                                </label>
                                <input type="email"
                                       name="official_email"
                                       id="official_email"
                                       value="{{ old('official_email', $contactSettings['official_email'] ?? '') }}"
                                       required
                                       placeholder="info@school.com"
                                       class="w-full px-3 sm:px-4 py-2 text-sm sm:text-base border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-maroon focus:border-transparent dark:bg-gray-700 dark:text-white">
                                @error('official_email')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Website URL -->
                            <div>
                                <label for="website_url" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Website URL
                                </label>
                                <input type="url"
                                       name="website_url"
                                       id="website_url"
                                       value="{{ old('website_url', $contactSettings['website_url'] ?? '') }}"
                                       placeholder="https://school.com"
                                       class="w-full px-3 sm:px-4 py-2 text-sm sm:text-base border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-maroon focus:border-transparent dark:bg-gray-700 dark:text-white">
                                @error('website_url')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Save Button -->
                    <div class="mt-6 sm:mt-8 flex items-center justify-end">
                        <button type="submit"
                                class="px-4 py-2.5 sm:px-6 sm:py-3 bg-gradient-to-r from-maroon to-maroon-dark text-white font-semibold text-sm sm:text-base rounded-lg hover:shadow-lg transition-all duration-200 flex items-center">
                            <i class="fas fa-save mr-2"></i>
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>

            <!-- ACADEMIC STRUCTURE TAB -->
            <div x-show="activeTab === 'academic'" x-transition style="display: none;">
                <form action="{{ route('settings.update-academic-structure') }}" method="POST" >
                    @csrf

                    <div class="space-y-4 sm:space-y-6">
                        <h3 class="text-base sm:text-lg font-semibold text-gray-900 dark:text-white mb-3 sm:mb-4">
                            <i class="fas fa-graduation-cap text-maroon mr-2"></i>
                            Academic Structure
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
                            <!-- Current Academic Year -->
                            <div class="md:col-span-2 lg:col-span-1">
                                <label for="current_academic_year" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Current Academic Year <span class="text-red-500">*</span>
                                </label>
                                <input type="text"
                                       name="current_academic_year"
                                       id="current_academic_year"
                                       value="{{ old('current_academic_year', $academicSettings['current_academic_year'] ?? '') }}"
                                       required
                                       placeholder="2024/2025"
                                       pattern="\d{4}/\d{4}"
                                       class="w-full px-3 sm:px-4 py-2 text-sm sm:text-base border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-maroon focus:border-transparent dark:bg-gray-700 dark:text-white">
                                <p class="mt-1 text-xs text-gray-500">Format: YYYY/YYYY</p>
                                @error('current_academic_year')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Term System -->
                            <div>
                                <label for="term_system" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Term System <span class="text-red-500">*</span>
                                </label>
                                <select name="term_system"
                                        id="term_system"
                                        required
                                        class="w-full px-3 sm:px-4 py-2 text-sm sm:text-base border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-maroon focus:border-transparent dark:bg-gray-700 dark:text-white">
                                    <option value="3_terms" {{ (old('term_system', $academicSettings['term_system'] ?? '') == '3_terms') ? 'selected' : '' }}>
                                        3 Terms
                                    </option>
                                    <option value="2_semesters" {{ (old('term_system', $academicSettings['term_system'] ?? '') == '2_semesters') ? 'selected' : '' }}>
                                        2 Semesters
                                    </option>
                                </select>
                                @error('term_system')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Current Term -->
                            <div>
                                <label for="current_term" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Current Term <span class="text-red-500">*</span>
                                </label>
                                <select name="current_term"
                                        id="current_term"
                                        required
                                        class="w-full px-3 sm:px-4 py-2 text-sm sm:text-base border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-maroon focus:border-transparent dark:bg-gray-700 dark:text-white">
                                    <option value="1" {{ (old('current_term', $academicSettings['current_term'] ?? '') == '1') ? 'selected' : '' }}>Term 1</option>
                                    <option value="2" {{ (old('current_term', $academicSettings['current_term'] ?? '') == '2') ? 'selected' : '' }}>Term 2</option>
                                    <option value="3" {{ (old('current_term', $academicSettings['current_term'] ?? '') == '3') ? 'selected' : '' }}>Term 3</option>
                                </select>
                                @error('current_term')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Term Dates -->
                        <div class="mt-6">
                            <h4 class="font-semibold text-gray-900 dark:text-white mb-3 text-sm sm:text-base">Term Dates</h4>

                            @foreach([1, 2, 3] as $term)
                            <div class="mb-4 p-3 sm:p-4 bg-gray-50 dark:bg-gray-700/30 rounded-lg">
                                <h5 class="font-medium text-gray-900 dark:text-white mb-2 text-sm">
                                    {{ $termDates[$term]['name'] ?? 'Term ' . $term }}
                                </h5>

                                <input type="hidden" name="term_dates[{{ $term }}][name]" value="{{ $termDates[$term]['name'] ?? 'Term ' . $term }}">

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">
                                            Start Date
                                        </label>
                                        <input type="date"
                                               name="term_dates[{{ $term }}][start_date]"
                                               value="{{ old('term_dates.' . $term . '.start_date', $termDates[$term]['start_date'] ?? '') }}"
                                               required
                                               class="w-full px-3 py-1.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-1 focus:ring-maroon focus:border-transparent dark:bg-gray-700 dark:text-white">
                                    </div>

                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">
                                            End Date
                                        </label>
                                        <input type="date"
                                               name="term_dates[{{ $term }}][end_date]"
                                               value="{{ old('term_dates.' . $term . '.end_date', $termDates[$term]['end_date'] ?? '') }}"
                                               required
                                               class="w-full px-3 py-1.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-1 focus:ring-maroon focus:border-transparent dark:bg-gray-700 dark:text-white">
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Save Button -->
                    <div class="mt-6 sm:mt-8 flex items-center justify-end">
                        <button type="submit"
                                class="px-4 py-2.5 sm:px-6 sm:py-3 bg-gradient-to-r from-maroon to-maroon-dark text-white font-semibold text-sm sm:text-base rounded-lg hover:shadow-lg transition-all duration-200 flex items-center">
                            <i class="fas fa-save mr-2"></i>
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>

            <!-- EMAIL CONFIGURATION TAB -->
            <div x-show="activeTab === 'email'" x-transition style="display: none;">
                <form action="{{ route('settings.update-email-config') }}" method="POST" >
                    @csrf

                    <div class="space-y-4 sm:space-y-6">
                        <h3 class="text-base sm:text-lg font-semibold text-gray-900 dark:text-white mb-3 sm:mb-4">
                            <i class="fas fa-at text-maroon mr-2"></i>
                            Email Configuration
                        </h3>

                        <!-- Email Domain -->
                        <div>
                            <label for="email_domain" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                School Email Domain <span class="text-red-500">*</span>
                            </label>
                            <input type="text"
                                   name="email_domain"
                                   id="email_domain"
                                   value="{{ old('email_domain', $emailSettings['email_domain'] ?? '') }}"
                                   required
                                   placeholder="asms.ac.ug"
                                   class="w-full px-3 sm:px-4 py-2 text-sm sm:text-base border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-maroon focus:border-transparent dark:bg-gray-700 dark:text-white">
                            <p class="mt-1 text-xs sm:text-sm text-gray-500">Users will get emails like: username@{{ $emailSettings['email_domain'] ?? 'school.com' }}</p>
                            @error('email_domain')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Email Format -->
                        <div>
                            <label for="email_format" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Email Format <span class="text-red-500">*</span>
                            </label>
                            <select name="email_format"
                                    id="email_format"
                                    required
                                    class="w-full px-3 sm:px-4 py-2 text-sm sm:text-base border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-maroon focus:border-transparent dark:bg-gray-700 dark:text-white">
                                <option value="staffid" {{ (old('email_format', $emailSettings['email_format'] ?? '') == 'staffid') ? 'selected' : '' }}>
                                    Staff ID (e.g., jdoe@school.com)
                                </option>
                                <option value="firstname.lastname" {{ (old('email_format', $emailSettings['email_format'] ?? '') == 'firstname.lastname') ? 'selected' : '' }}>
                                    First.Last Name (e.g., john.doe@school.com)
                                </option>
                            </select>
                            @error('email_format')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Save Button -->
                    <div class="mt-6 sm:mt-8 flex items-center justify-end">
                        <button type="submit"
                                class="px-4 py-2.5 sm:px-6 sm:py-3 bg-gradient-to-r from-maroon to-maroon-dark text-white font-semibold text-sm sm:text-base rounded-lg hover:shadow-lg transition-all duration-200 flex items-center">
                            <i class="fas fa-save mr-2"></i>
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>

            <!-- REPORT CARD SETTINGS TAB -->
            <div x-show="activeTab === 'report'" x-transition style="display: none;">
    <!-- Main Report Card Settings Form -->
    <form action="{{ route('settings.update-report-card') }}" method="POST" enctype="multipart/form-data"  class="mb-8">
        @csrf

        <div class="space-y-4 sm:space-y-6">
            <div class="flex items-center justify-between mb-3 sm:mb-4">
                <h3 class="text-base sm:text-lg font-semibold text-gray-900 dark:text-white">
                    <i class="fas fa-certificate text-maroon mr-2"></i>
                    Report Card Settings
                </h3>
            </div>

            <!-- Letterhead Text -->
            <div class="bg-white dark:bg-gray-800 p-4 sm:p-6 rounded-lg border border-gray-200 dark:border-gray-700">
                <label for="letterhead_text" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Letterhead Text
                </label>
                <textarea name="letterhead_text"
                        id="letterhead_text"
                        rows="3"
                        placeholder="School name, address, contact info..."
                        class="w-full px-3 sm:px-4 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-maroon focus:border-transparent dark:bg-gray-700 dark:text-white font-mono">{{ old('letterhead_text', $reportSettings['letterhead_text'] ?? '') }}</textarea>
                <p class="mt-2 text-xs sm:text-sm text-gray-500 dark:text-gray-400">This appears at the top of report cards</p>
                @error('letterhead_text')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Footer Text -->
            <div class="bg-white dark:bg-gray-800 p-4 sm:p-6 rounded-lg border border-gray-200 dark:border-gray-700">
                <label for="report_footer_text" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Footer Text
                </label>
                <textarea name="report_footer_text"
                        id="report_footer_text"
                        rows="2"
                        placeholder="Footer disclaimer or contact info..."
                        class="w-full px-3 sm:px-4 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-maroon focus:border-transparent dark:bg-gray-700 dark:text-white">{{ old('report_footer_text', $reportSettings['report_footer_text'] ?? '') }}</textarea>
                <p class="mt-2 text-xs sm:text-sm text-gray-500 dark:text-gray-400">This appears at the bottom of report cards</p>
                @error('report_footer_text')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Principal Name -->
            <div class="bg-white dark:bg-gray-800 p-4 sm:p-6 rounded-lg border border-gray-200 dark:border-gray-700">
                <label for="principal_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Principal/Headteacher Name <span class="text-red-500">*</span>
                </label>
                <input type="text"
                    name="principal_name"
                    id="principal_name"
                    value="{{ old('principal_name', $reportSettings['principal_name'] ?? '') }}"
                    required
                    placeholder="Dr. John Doe"
                    class="w-full px-3 sm:px-4 py-2 text-sm sm:text-base border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-maroon focus:border-transparent dark:bg-gray-700 dark:text-white">
                @error('principal_name')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Signatures Section -->
            <div class="bg-white dark:bg-gray-800 p-4 sm:p-6 rounded-lg border border-gray-200 dark:border-gray-700">
                <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-4">Signatures</h4>

                <div class="space-y-6">
                    <!-- Principal Signature -->
                    <div>


                        <div class="flex flex-col sm:flex-row sm:items-center sm:space-x-4 space-y-3 sm:space-y-0">
                            @if($reportSettings['principal_signature'] ?? null)
                                <div class="flex-shrink-0">
                                    <img src="{{ asset('storage/' . $reportSettings['principal_signature']) }}"
                                        alt="Principal Signature"
                                        class="h-16 w-32 sm:h-20 sm:w-40 object-contain border-2 border-gray-200 dark:border-gray-700 p-2 bg-white dark:bg-gray-900 rounded">
                                </div>
                            @else
                                <div class="h-16 w-32 sm:h-20 sm:w-40 bg-gray-100 dark:bg-gray-700 rounded border-2 border-dashed border-gray-300 dark:border-gray-600 flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-signature text-gray-400 text-2xl"></i>
                                </div>
                            @endif

                            <div class="flex-grow">
                                <input type="file"
                                    name="principal_signature"
                                    accept="image/png,image/jpeg,image/jpg"
                                    class="block w-full text-sm text-gray-500 dark:text-gray-400
                                        file:mr-3 file:py-2 file:px-4 file:rounded-lg
                                        file:border-0 file:text-sm file:font-medium
                                        file:bg-maroon file:text-white hover:file:bg-maroon-dark
                                        dark:file:bg-maroon dark:file:text-white">
                                <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">PNG, JPG up to 1MB. Transparent background recommended.</p>
                                @error('principal_signature')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Headteacher Signature -->
                    <div class="pt-4 border-t border-gray-200 dark:border-gray-700">


                        <div class="flex flex-col sm:flex-row sm:items-center sm:space-x-4 space-y-3 sm:space-y-0">
                            @if($reportSettings['headteacher_signature'] ?? null)
                                <div class="flex-shrink-0">
                                    <img src="{{ asset('storage/' . $reportSettings['headteacher_signature']) }}"
                                        alt="Headteacher Signature"
                                        class="h-16 w-32 sm:h-20 sm:w-40 object-contain border-2 border-gray-200 dark:border-gray-700 p-2 bg-white dark:bg-gray-900 rounded">
                                </div>
                            @else
                                <div class="h-16 w-32 sm:h-20 sm:w-40 bg-gray-100 dark:bg-gray-700 rounded border-2 border-dashed border-gray-300 dark:border-gray-600 flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-signature text-gray-400 text-2xl"></i>
                                </div>
                            @endif

                            <div class="flex-grow">
                                <input type="file"
                                    name="headteacher_signature"
                                    accept="image/png,image/jpeg,image/jpg"
                                    class="block w-full text-sm text-gray-500 dark:text-gray-400
                                        file:mr-3 file:py-2 file:px-4 file:rounded-lg
                                        file:border-0 file:text-sm file:font-medium
                                        file:bg-maroon file:text-white hover:file:bg-maroon-dark
                                        dark:file:bg-maroon dark:file:text-white">
                                <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">PNG, JPG up to 1MB. Transparent background recommended.</p>
                                @error('headteacher_signature')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Save Button -->
        <div class="mt-6 sm:mt-8 flex items-center justify-end">
            <button type="submit"
                    class="px-5 py-2.5 sm:px-6 sm:py-3 bg-gradient-to-r from-maroon to-maroon-dark
                           text-white font-semibold text-sm sm:text-base rounded-lg
                           hover:shadow-lg transition-all duration-200
                           flex items-center transform hover:-translate-y-0.5">
                <i class="fas fa-save mr-2"></i>
                Save Report Card Settings
            </button>
        </div>
    </form>

     <div class="flex items-center justify-between mb-3">


                              @if($reportSettings['headteacher_signature'] ?? null)
                               <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Headteacher Signature
                            </label>
                                <form action="{{ route('settings.delete-signature') }}" method="POST" >
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="type" value="headteacher">
                                    <button type="submit" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 text-sm flex items-center">
                                        <i class="fas fa-trash mr-1 text-xs"></i>
                                        Delete
                                    </button>
                                </form>
                            @endif

                        </div>





                            <div class="flex items-center justify-between mb-3">

                            @if($reportSettings['principal_signature'] ?? null)
                             <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Principal Signature
                            </label>
                                <form action="{{ route('settings.delete-signature') }}" method="POST" >
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="type" value="principal">
                                    <button type="submit" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 text-sm flex items-center">
                                        <i class="fas fa-trash mr-1 text-xs"></i>
                                        Delete
                                    </button>
                                </form>
                            @endif
                        </div>
</div>

            <!-- ADMISSION LETTER SETTINGS TAB -->
            <div x-show="activeTab === 'admission'" x-transition style="display: none;">
                <div class="space-y-4 sm:space-y-6">
                    <div class="flex items-center justify-between mb-3 sm:mb-4">
                        <h3 class="text-base sm:text-lg font-semibold text-gray-900 dark:text-white">
                            <i class="fas fa-file-contract text-maroon mr-2"></i>
                            Admission Letter Template
                        </h3>
                    </div>

                    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded-lg p-4">
                        <p class="text-sm text-blue-800 dark:text-blue-300">
                            Customize the content and template of admission letters. Configure opening paragraphs, requirements, and closing statements.
                        </p>
                    </div>

                    <div class="mt-6">
                        <a href="{{ route('settings.admission-letter') }}" 
                           class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-maroon to-maroon-dark text-white font-semibold text-sm sm:text-base rounded-lg hover:shadow-lg transition-all duration-200 transform hover:-translate-y-0.5">
                            <i class="fas fa-cog mr-2"></i>
                            Configure Admission Letters
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>


@endsection
