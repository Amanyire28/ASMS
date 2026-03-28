{{-- resources/views/modules/students/create.blade.php --}}
{{-- This view works for BOTH HTMX and regular requests --}}

@if(!request()->header('HX-Request'))
    @extends('layouts.app')
    @section('title', 'Add Student')
    @section('content')
@endif

<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Add New Student</h1>
    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Fill in the details to register a new student</p>
</div>

<!-- Student Form -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow">
    <form action="{{ route('students.store') }}" method="POST" enctype="multipart/form-data" class="p-6">
        @csrf

        <!-- Personal Information -->
        <div class="mb-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                <i class="fas fa-user mr-2 text-maroon"></i>
                Personal Information
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Student ID (auto-generated) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Student ID
                    </label>
                    <div class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-500 dark:text-gray-400 text-sm">
                        <i class="fas fa-magic mr-1"></i> Auto-generated on save (e.g. {{ date('Y') }}0001)
                    </div>
                </div>

                <!-- Photo -->
                <div>
                    <label for="photo" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Student Photo
                    </label>
                    <input type="file" name="photo" id="photo" accept="image/*"
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-maroon focus:border-transparent dark:bg-gray-700 dark:text-white @error('photo') border-red-500 @enderror">
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Max size: 2MB (JPEG, PNG, JPG)</p>
                    @error('photo')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- First Name -->
                <div>
                    <label for="first_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        First Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="first_name" id="first_name" value="{{ old('first_name') }}"
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-maroon focus:border-transparent dark:bg-gray-700 dark:text-white @error('first_name') border-red-500 @enderror"
                           required>
                    @error('first_name')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Last Name -->
                <div>
                    <label for="last_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Last Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="last_name" id="last_name" value="{{ old('last_name') }}"
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-maroon focus:border-transparent dark:bg-gray-700 dark:text-white @error('last_name') border-red-500 @enderror"
                           required>
                    @error('last_name')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Date of Birth -->
                <div>
                    <label for="date_of_birth" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Date of Birth <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="date_of_birth" id="date_of_birth" value="{{ old('date_of_birth') }}"
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-maroon focus:border-transparent dark:bg-gray-700 dark:text-white @error('date_of_birth') border-red-500 @enderror"
                           required>
                    @error('date_of_birth')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Gender -->
                <div>
                    <label for="gender" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Gender <span class="text-red-500">*</span>
                    </label>
                    <select name="gender" id="gender"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-maroon focus:border-transparent dark:bg-gray-700 dark:text-white @error('gender') border-red-500 @enderror"
                            required>
                        <option value="">Select Gender</option>
                        <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                        <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                        <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                    @error('gender')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Contact Information -->
        <div class="mb-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                <i class="fas fa-address-book mr-2 text-maroon"></i>
                Contact Information
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Email
                    </label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}"
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-maroon focus:border-transparent dark:bg-gray-700 dark:text-white @error('email') border-red-500 @enderror"
                           placeholder="student@example.com">
                    @error('email')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Phone -->
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Phone Number
                    </label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone') }}"
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-maroon focus:border-transparent dark:bg-gray-700 dark:text-white @error('phone') border-red-500 @enderror"
                           placeholder="+1 (555) 123-4567">
                    @error('phone')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Address -->
                <div class="md:col-span-2">
                    <label for="address" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Address
                    </label>
                    <textarea name="address" id="address" rows="3"
                              class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-maroon focus:border-transparent dark:bg-gray-700 dark:text-white @error('address') border-red-500 @enderror"
                              placeholder="Enter full address">{{ old('address') }}</textarea>
                    @error('address')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Parent/Guardian Information -->
        <div class="mb-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                <i class="fas fa-user-friends mr-2 text-maroon"></i>
                Parent/Guardian Information
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Parent Name -->
                <div>
                    <label for="parent_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Parent/Guardian Name
                    </label>
                    <input type="text" name="parent_name" id="parent_name" value="{{ old('parent_name') }}"
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-maroon focus:border-transparent dark:bg-gray-700 dark:text-white @error('parent_name') border-red-500 @enderror"
                           placeholder="Full name">
                    @error('parent_name')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Parent Phone -->
                <div>
                    <label for="parent_phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Parent/Guardian Phone
                    </label>
                    <input type="text" name="parent_phone" id="parent_phone" value="{{ old('parent_phone') }}"
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-maroon focus:border-transparent dark:bg-gray-700 dark:text-white @error('parent_phone') border-red-500 @enderror"
                           placeholder="+1 (555) 123-4567">
                    @error('parent_phone')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Parent Email -->
                <div class="md:col-span-2">
                    <label for="parent_email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Parent/Guardian Email
                    </label>
                    <input type="email" name="parent_email" id="parent_email" value="{{ old('parent_email') }}"
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-maroon focus:border-transparent dark:bg-gray-700 dark:text-white @error('parent_email') border-red-500 @enderror"
                           placeholder="parent@example.com">
                    @error('parent_email')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Academic Information -->
        <div class="mb-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                <i class="fas fa-graduation-cap mr-2 text-maroon"></i>
                Academic Information
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Class -->
                <div>
                    <label for="class_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Assign to Class
                    </label>
                    <select name="class_id" id="class_id"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-maroon focus:border-transparent dark:bg-gray-700 dark:text-white @error('class_id') border-red-500 @enderror">
                        <option value="">Select Class (Optional)</option>
                        @foreach($classes as $class)
                        <option value="{{ $class->id }}" {{ old('class_id') == $class->id ? 'selected' : '' }}>
                            {{ $class->name }}
                        </option>
                        @endforeach
                    </select>
                    @error('class_id')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Enrollment Date -->
                <div>
                    <label for="enrollment_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Enrollment Date <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="enrollment_date" id="enrollment_date" value="{{ old('enrollment_date', date('Y-m-d')) }}"
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-maroon focus:border-transparent dark:bg-gray-700 dark:text-white @error('enrollment_date') border-red-500 @enderror"
                           required>
                    @error('enrollment_date')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200 dark:border-gray-700">
            <a href="{{ route('students.index') }}"
               hx-get="{{ route('students.index') }}"
               hx-target="#page-content"
               hx-push-url="true"
               hx-indicator="#loading-indicator"
               class="px-6 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                <i class="fas fa-times mr-2"></i>
                Cancel
            </a>
            <button type="submit"
                    class="px-6 py-2 bg-maroon hover:bg-maroon-dark text-white rounded-lg transition-colors flex items-center">
                <i class="fas fa-save mr-2"></i>
                Save Student
            </button>
        </div>
    </form>
</div>

@if(!request()->header('HX-Request'))
    @endsection
@endif
