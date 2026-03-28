@if(!request()->header('HX-Request'))
    @extends('layouts.app')
    @section('title', 'Add New Teacher')
    @section('page-title', 'Add New Teacher')
    @section('page-description', 'Create a new teacher record in the system')
    @section('content')
@endif
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Page Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <div class="flex items-center space-x-4">
                    <div class="p-3 bg-gradient-to-r from-maroon/10 to-maroon/5 rounded-2xl">
                        <i class="fas fa-chalkboard-teacher text-maroon text-2xl"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Add New Teacher</h1>
                        <p class="text-gray-600 dark:text-gray-300">Create a new teacher record in the system</p>
                    </div>
                </div>
            </div>
            <a href="{{ route('teachers.index') }}"
               class="inline-flex items-center px-4 py-3 bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-600 hover:from-gray-100 hover:to-gray-200 dark:hover:from-gray-600 dark:hover:to-gray-500 text-gray-700 dark:text-gray-300 font-semibold rounded-xl transition-all duration-300 shadow-sm hover:shadow-md">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Teachers
            </a>
        </div>
    </div>

    <!-- Progress Steps -->
    <div class="mb-8">
        <div class="flex items-center justify-center">
            <div class="flex items-center space-x-8">
                <!-- Step 1 -->
                <div class="flex flex-col items-center">
                    <div id="step1" class="step-circle active">
                        <span class="step-number">1</span>
                        <div class="step-check hidden">
                            <i class="fas fa-check"></i>
                        </div>
                    </div>
                    <span class="step-label mt-2">Basic Info</span>
                </div>

                <!-- Step Connector -->
                <div class="h-1 w-16 bg-gray-200 dark:bg-gray-700"></div>

                <!-- Step 2 -->
                <div class="flex flex-col items-center">
                    <div id="step2" class="step-circle">
                        <span class="step-number">2</span>
                        <div class="step-check hidden">
                            <i class="fas fa-check"></i>
                        </div>
                    </div>
                    <span class="step-label mt-2">Details</span>
                </div>

                <!-- Step Connector -->
                <div class="h-1 w-16 bg-gray-200 dark:bg-gray-700"></div>

                <!-- Step 3 -->
                <div class="flex flex-col items-center">
                    <div id="step3" class="step-circle">
                        <span class="step-number">3</span>
                        <div class="step-check hidden">
                            <i class="fas fa-check"></i>
                        </div>
                    </div>
                    <span class="step-label mt-2">Classes</span>
                </div>

                <!-- Step Connector -->
                <div class="h-1 w-16 bg-gray-200 dark:bg-gray-700"></div>

                <!-- Step 4 -->
                <div class="flex flex-col items-center">
                    <div id="step4" class="step-circle">
                        <span class="step-number">4</span>
                        <div class="step-check hidden">
                            <i class="fas fa-check"></i>
                        </div>
                    </div>
                    <span class="step-label mt-2">Subjects</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Form Card -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden">
        <!-- Form Header -->
        <div class="px-6 py-8 border-b border-gray-100 dark:border-gray-700 bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-800">
            <h2 id="stepTitle" class="text-xl font-bold text-gray-900 dark:text-white">Basic Information</h2>
            <p id="stepDescription" class="text-gray-600 dark:text-gray-300 mt-1">Enter teacher's personal details</p>
        </div>

        <!-- Step 1: Basic Information -->
        <div id="step1Content" class="p-6">
            <form id="basicInfoForm" class="space-y-6">
                @csrf
                <input type="hidden" id="teacherId">

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- Employee ID -->
                    <div>
                        <label for="employee_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Employee ID <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               name="employee_id"
                               id="employee_id"
                               required
                               class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-xl focus:border-maroon focus:ring-4 focus:ring-maroon/10 bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 transition-all duration-300"
                               placeholder="EMP-XXXX">
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Leave empty to auto-generate</p>
                    </div>
                    <!-- Add this field in the Step 1 form, after phone field -->
<div>
    <label for="employment_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
        Employment Date <span class="text-red-500">*</span>
    </label>
    <input type="date"
           name="employment_date"
           id="employment_date"
           required
           class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-xl focus:border-maroon focus:ring-4 focus:ring-maroon/10 bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-all duration-300">
</div>

                    <!-- First Name -->
                    <div>
                        <label for="first_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            First Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               name="first_name"
                               id="first_name"
                               required
                               class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-xl focus:border-maroon focus:ring-4 focus:ring-maroon/10 bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 transition-all duration-300"
                               placeholder="Enter first name">
                    </div>

                    <!-- Last Name -->
                    <div>
                        <label for="last_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Last Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               name="last_name"
                               id="last_name"
                               required
                               class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-xl focus:border-maroon focus:ring-4 focus:ring-maroon/10 bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 transition-all duration-300"
                               placeholder="Enter last name">
                    </div>

                    <!-- Other Names -->
                    <div>
                        <label for="other_names" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Other Names
                        </label>
                        <input type="text"
                               name="other_names"
                               id="other_names"
                               class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-xl focus:border-maroon focus:ring-4 focus:ring-maroon/10 bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 transition-all duration-300"
                               placeholder="Optional">
                    </div>

                    <!-- Gender -->
                    <div>
                        <label for="gender" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Gender <span class="text-red-500">*</span>
                        </label>
                        <select name="gender"
                                id="gender"
                                required
                                class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-xl focus:border-maroon focus:ring-4 focus:ring-maroon/10 bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-all duration-300">
                            <option value="">Select Gender</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                            <option value="other">Other</option>
                        </select>
                    </div>

                    <!-- Date of Birth -->
                    <div>
                        <label for="date_of_birth" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Date of Birth <span class="text-red-500">*</span>
                        </label>
                        <input type="date"
                               name="date_of_birth"
                               id="date_of_birth"
                               required
                               class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-xl focus:border-maroon focus:ring-4 focus:ring-maroon/10 bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-all duration-300">
                    </div>

                    <!-- Phone -->
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Phone Number <span class="text-red-500">*</span>
                        </label>
                        <input type="tel"
                               name="phone"
                               id="phone"
                               required
                               class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-xl focus:border-maroon focus:ring-4 focus:ring-maroon/10 bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 transition-all duration-300"
                               placeholder="+256 XXX XXX XXX">
                    </div>

                    <!-- Email (Optional - will be auto-generated) -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Email Address
                        </label>
                        <input type="email"
                               name="email"
                               id="email"
                               class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-xl focus:border-maroon focus:ring-4 focus:ring-maroon/10 bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 transition-all duration-300"
                               placeholder="Leave empty for auto-generation">
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Email will be generated from name and employee ID</p>
                    </div>

                   <!-- Role -->
<div>
    <label for="role" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
        Role <span class="text-red-500">*</span>
    </label>
    <select name="role"
            id="role"
            required
            class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-xl focus:border-maroon focus:ring-4 focus:ring-maroon/10 bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-all duration-300">
        <option value="">Select Role</option>
        @foreach($roles as $role)
            <option value="{{ $role->name }}">{{ $role->name }}</option>
        @endforeach
    </select>
</div>
                </div>

                <div class="flex items-center justify-between pt-6 border-t border-gray-100 dark:border-gray-700">
                    <div></div>
                    <div>
                        <button type="button"
                                onclick="nextStep(2)"
                                class="px-6 py-3 bg-gradient-to-r from-maroon to-maroon-dark hover:from-maroon-dark hover:to-maroon text-white font-semibold rounded-xl transition-all duration-300 shadow-lg hover:shadow-xl">
                            Next: Additional Details <i class="fas fa-arrow-right ml-2"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Step 2: Additional Details -->
        <div id="step2Content" class="p-6 hidden">
            <form id="additionalDetailsForm" class="space-y-6">
                @csrf
                <input type="hidden" name="teacher_id" id="teacher_id">

                <!-- Address -->
                <div>
                    <label for="address" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Residential Address <span class="text-red-500">*</span>
                    </label>
                    <textarea name="address"
                              id="address"
                              rows="3"
                              required
                              class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-xl focus:border-maroon focus:ring-4 focus:ring-maroon/10 bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 transition-all duration-300"
                              placeholder="Enter full address"></textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- National ID -->
                    <div>
                        <label for="national_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            National ID / Passport
                        </label>
                        <input type="text"
                               name="national_id"
                               id="national_id"
                               class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-xl focus:border-maroon focus:ring-4 focus:ring-maroon/10 bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 transition-all duration-300"
                               placeholder="Enter national ID">
                    </div>



                    <!-- Designation -->
                    <div>
                        <label for="designation" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Designation
                        </label>
                        <input type="text"
                               name="designation"
                               id="designation"
                               class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-xl focus:border-maroon focus:ring-4 focus:ring-maroon/10 bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 transition-all duration-300"
                               placeholder="e.g., Senior Teacher">
                    </div>
                </div>

                <!-- Qualifications -->
                <div>
                    <label for="qualifications" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Qualifications
                    </label>
                    <textarea name="qualifications"
                              id="qualifications"
                              rows="3"
                              class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-xl focus:border-maroon focus:ring-4 focus:ring-maroon/10 bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 transition-all duration-300"
                              placeholder="List qualifications (e.g., B.Ed, M.Ed, etc.)"></textarea>
                </div>

                <!-- Profile Photo -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-4">
                        Profile Photo
                    </label>
                    <div class="flex items-center space-x-6">
                        <!-- Photo Preview -->
                        <div class="relative">
                            <div id="photoPreview" class="w-32 h-32 bg-gradient-to-r from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-600 rounded-2xl flex items-center justify-center shadow-lg overflow-hidden">
                                <i class="fas fa-user text-gray-400 dark:text-gray-500 text-4xl"></i>
                            </div>
                            <div id="uploadProgress" class="hidden absolute inset-0 bg-black/50 flex items-center justify-center rounded-2xl">
                                <div class="spinner border-2 border-white border-t-transparent rounded-full w-8 h-8 animate-spin"></div>
                            </div>
                        </div>

                        <!-- Upload Controls -->
                        <div class="flex-1">
                            <div class="mb-4">
                                <input type="file"
                                       name="photo"
                                       id="photo"
                                       accept="image/*"
                                       class="hidden"
                                       onchange="previewPhoto(event)">
                                <label for="photo"
                                       class="cursor-pointer inline-flex items-center px-4 py-3 bg-gradient-to-r from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/10 hover:from-blue-100 hover:to-blue-200 dark:hover:from-blue-800/30 dark:hover:to-blue-700/20 text-blue-600 dark:text-blue-400 font-semibold rounded-xl transition-all duration-300 shadow-sm hover:shadow-md">
                                    <i class="fas fa-upload mr-2"></i>
                                    Upload Photo
                                </label>
                                <button type="button"
                                        onclick="clearPhoto()"
                                        class="ml-3 inline-flex items-center px-4 py-3 bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-600 hover:from-gray-100 hover:to-gray-200 dark:hover:from-gray-600 dark:hover:to-gray-500 text-gray-700 dark:text-gray-300 font-semibold rounded-xl transition-all duration-300 shadow-sm hover:shadow-md">
                                    <i class="fas fa-times mr-2"></i>
                                    Remove
                                </button>
                            </div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Recommended: Square image, maximum 2MB, JPG or PNG format
                            </p>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-6 border-t border-gray-100 dark:border-gray-700">
                    <button type="button"
                            onclick="prevStep(1)"
                            class="px-6 py-3 bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-600 hover:from-gray-100 hover:to-gray-200 dark:hover:from-gray-600 dark:hover:to-gray-500 text-gray-700 dark:text-gray-300 font-semibold rounded-xl transition-all duration-300 shadow-sm hover:shadow-md">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Previous
                    </button>
                    <div>
                        <button type="button"
                                onclick="nextStep(3)"
                                class="px-6 py-3 bg-gradient-to-r from-maroon to-maroon-dark hover:from-maroon-dark hover:to-maroon text-white font-semibold rounded-xl transition-all duration-300 shadow-lg hover:shadow-xl">
                            Next: Class Assignments <i class="fas fa-arrow-right ml-2"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Step 3: Class Assignments -->
        <div id="step3Content" class="p-6 hidden">
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Assign Classes</h3>
                <p class="text-gray-600 dark:text-gray-300">Select classes this teacher will be teaching. Check "Class Teacher" for classes where they are the main teacher.</p>
            </div>

            <div class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($classLevels ?? [] as $classLevel)
                        <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-4">
                            <h4 class="font-semibold text-gray-900 dark:text-white mb-3">{{ $classLevel->name }}</h4>
                            <div class="space-y-2">
                                @foreach($classLevel->classes ?? [] as $class)
                                    <div class="flex items-center justify-between p-2 bg-white dark:bg-gray-800 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                        <label class="flex items-center cursor-pointer">
                                            <input type="checkbox"
                                                   name="class_assignments[{{ $loop->index }}][class_id]"
                                                   value="{{ $class->id }}"
                                                   class="class-checkbox h-5 w-5 text-maroon rounded focus:ring-maroon">
                                            <span class="ml-2 text-gray-700 dark:text-gray-300">{{ $class->name }}</span>
                                        </label>
                                        <div class="flex items-center space-x-2">
                                            <input type="checkbox"
                                                   name="class_assignments[{{ $loop->index }}][is_class_teacher]"
                                                   value="1"
                                                   class="class-teacher-checkbox h-4 w-4 text-maroon rounded focus:ring-maroon"
                                                   disabled>
                                            <span class="text-xs text-gray-500 dark:text-gray-400">Class Teacher</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="flex items-center justify-between pt-6 border-t border-gray-100 dark:border-gray-700">
                <button type="button"
                        onclick="prevStep(2)"
                        class="px-6 py-3 bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-600 hover:from-gray-100 hover:to-gray-200 dark:hover:from-gray-600 dark:hover:to-gray-500 text-gray-700 dark:text-gray-300 font-semibold rounded-xl transition-all duration-300 shadow-sm hover:shadow-md">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Previous
                </button>
                <div>
                    <button type="button"
                            onclick="saveClassAssignments()"
                            class="px-6 py-3 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white font-semibold rounded-xl transition-all duration-300 shadow-lg hover:shadow-xl mr-3">
                        Save & Continue
                    </button>
                    <button type="button"
                            onclick="nextStep(4)"
                            class="px-6 py-3 bg-gradient-to-r from-maroon to-maroon-dark hover:from-maroon-dark hover:to-maroon text-white font-semibold rounded-xl transition-all duration-300 shadow-lg hover:shadow-xl">
                        Skip & Next <i class="fas fa-arrow-right ml-2"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Step 4: Subject Assignments -->
        <div id="step4Content" class="p-6 hidden">
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Assign Subjects</h3>
                <p class="text-gray-600 dark:text-gray-300">Select subjects this teacher will be teaching.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($subjects ?? [] as $subject)
                    <div class="flex items-center p-3 bg-white dark:bg-gray-800 border-2 border-gray-200 dark:border-gray-700 rounded-xl hover:border-maroon dark:hover:border-maroon transition-colors">
                        <input type="checkbox"
                               name="subject_ids[]"
                               value="{{ $subject->id }}"
                               id="subject_{{ $subject->id }}"
                               class="h-5 w-5 text-maroon rounded focus:ring-maroon">
                        <label for="subject_{{ $subject->id }}" class="ml-3 flex-1 cursor-pointer">
                            <span class="font-medium text-gray-900 dark:text-white">{{ $subject->name }}</span>
                            <span class="text-sm text-gray-500 dark:text-gray-400 block">{{ $subject->code }}</span>
                        </label>
                    </div>
                @endforeach
            </div>

            <div class="flex items-center justify-between pt-6 border-t border-gray-100 dark:border-gray-700">
                <button type="button"
                        onclick="prevStep(3)"
                        class="px-6 py-3 bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-600 hover:from-gray-100 hover:to-gray-200 dark:hover:from-gray-600 dark:hover:to-gray-500 text-gray-700 dark:text-gray-300 font-semibold rounded-xl transition-all duration-300 shadow-sm hover:shadow-md">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Previous
                </button>
                <div>
                    <button type="button"
                            onclick="saveSubjectAssignments()"
                            class="px-8 py-3 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white font-semibold rounded-xl transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-[1.02]">
                        <i class="fas fa-check mr-2"></i>
                        Complete Registration
                    </button>
                </div>
            </div>
        </div>

        <!-- Success Message -->
        <div id="successMessage" class="p-6 hidden">
            <div class="text-center py-12">
                <div class="w-24 h-24 bg-gradient-to-r from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/10 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-check text-green-600 dark:text-green-400 text-4xl"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-3">Teacher Registered Successfully!</h3>
                <p class="text-gray-600 dark:text-gray-300 mb-8 max-w-lg mx-auto">
                    The teacher has been added to the system. Login credentials have been generated.
                </p>
                <div class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-800 rounded-xl p-6 mb-8 max-w-md mx-auto">
                    <div class="text-left space-y-3">
                        <div>
                            <span class="text-sm text-gray-500 dark:text-gray-400">Employee ID:</span>
                            <p class="font-semibold text-gray-900 dark:text-white" id="successEmployeeId"></p>
                        </div>
                        <div>
                            <span class="text-sm text-gray-500 dark:text-gray-400">Email:</span>
                            <p class="font-semibold text-gray-900 dark:text-white" id="successEmail"></p>
                        </div>
                        <div>
                            <span class="text-sm text-gray-500 dark:text-gray-400">Default Password:</span>
                            <p class="font-semibold text-gray-900 dark:text-white">ASMS@{{ date('Y') }}</p>
                        </div>
                    </div>
                </div>
                <div class="flex items-center justify-center space-x-4">
                    <a href="/admin/teachers"
                       class="px-6 py-3 bg-gradient-to-r from-maroon to-maroon-dark hover:from-maroon-dark hover:to-maroon text-white font-semibold rounded-xl transition-all duration-300 shadow-lg hover:shadow-xl">
                        <i class="fas fa-list mr-2"></i>
                        View All Teachers
                    </a>
                    <button type="button"
                            onclick="resetForm()"
                            class="px-6 py-3 bg-gradient-to-r from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/10 hover:from-blue-100 hover:to-blue-200 dark:hover:from-blue-800/30 dark:hover:to-blue-700/20 text-blue-600 dark:text-blue-400 font-semibold rounded-xl transition-all duration-300 shadow-sm hover:shadow-md">
                        <i class="fas fa-plus mr-2"></i>
                        Add Another Teacher
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let currentStep = 1;
    let teacherId = null;

    // Step titles and descriptions
    const stepData = {
        1: {
            title: 'Basic Information',
            description: 'Enter teacher\'s personal details'
        },
        2: {
            title: 'Additional Details',
            description: 'Add address, qualifications, and photo'
        },
        3: {
            title: 'Class Assignments',
            description: 'Assign classes to teacher'
        },
        4: {
            title: 'Subject Assignments',
            description: 'Assign subjects to teacher'
        }
    };

    // Show step function
    function showStep(step) {
        currentStep = step;

        // Update step indicator
        document.querySelectorAll('.step-circle').forEach((circle, index) => {
            if (index + 1 < step) {
                circle.classList.remove('active');
                circle.querySelector('.step-number').classList.add('hidden');
                circle.querySelector('.step-check').classList.remove('hidden');
            } else if (index + 1 === step) {
                circle.classList.add('active');
                circle.querySelector('.step-number').classList.remove('hidden');
                circle.querySelector('.step-check').classList.add('hidden');
            } else {
                circle.classList.remove('active');
                circle.querySelector('.step-number').classList.remove('hidden');
                circle.querySelector('.step-check').classList.add('hidden');
            }
        });

        // Update content
        document.getElementById('stepTitle').textContent = stepData[step].title;
        document.getElementById('stepDescription').textContent = stepData[step].description;

        // Hide all step contents
        document.querySelectorAll('[id$="Content"]').forEach(el => {
            el.classList.add('hidden');
        });

        // Show current step content
        document.getElementById(`step${step}Content`).classList.remove('hidden');
    }

    // Navigate to next step
    function nextStep(step) {
        if (step === 2) {
            saveBasicInfo();
        } else if (step === 3) {
            saveAdditionalDetails();
        } else {
            showStep(step);
        }
    }

    // Navigate to previous step
    function prevStep(step) {
        showStep(step);
    }

    // Save Basic Information
    function saveBasicInfo() {
        const form = document.getElementById('basicInfoForm');
        const formData = new FormData(form);

        // Auto-generate employee ID if empty
        if (!formData.get('employee_id')) {
            const randomNum = Math.floor(1000 + Math.random() * 9000);
            const year = new Date().getFullYear();
            formData.set('employee_id', `STAFF-${year}-${randomNum}`);
        }

        fetch('{{ route("teachers.store.basic") }}', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                teacherId = data.teacher_id;
                document.getElementById('teacher_id').value = teacherId;
                document.getElementById('teacherId').value = teacherId;
                showStep(2);
                showToast('success', data.message);
            } else {
                showToast('error', data.message || 'Error saving basic information');
                Object.entries(data.errors || {}).forEach(([field, messages]) => {
                    const input = document.getElementById(field);
                    if (input) {
                        input.classList.add('border-red-500');
                        showToast('error', messages[0]);
                    }
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('error', 'Network error. Please try again.');
        });
    }

    // Save Additional Details
    function saveAdditionalDetails() {
        const form = document.getElementById('additionalDetailsForm');
        const formData = new FormData(form);
        const photoInput = document.getElementById('photo');

        if (photoInput.files[0]) {
            formData.append('photo', photoInput.files[0]);
        }

        fetch('{{ route("teachers.store.additional") }}', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showStep(3);
                showToast('success', data.message);
            } else {
                showToast('error', data.message || 'Error saving additional details');
                Object.entries(data.errors || {}).forEach(([field, messages]) => {
                    const input = document.getElementById(field);
                    if (input) {
                        input.classList.add('border-red-500');
                        showToast('error', messages[0]);
                    }
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('error', 'Network error. Please try again.');
        });
    }

    // Save Class Assignments
    function saveClassAssignments() {
        const classCheckboxes = document.querySelectorAll('.class-checkbox:checked');
        const classAssignments = [];

        classCheckboxes.forEach((checkbox, index) => {
            const classId = checkbox.value;
            const classTeacherCheckbox = checkbox.closest('.flex.items-center.justify-between')
                .querySelector('.class-teacher-checkbox');
            const isClassTeacher = classTeacherCheckbox.checked;

            classAssignments.push({
                class_id: classId,
                is_class_teacher: isClassTeacher ? 1 : 0
            });
        });

        fetch('{{ route("teachers.store.classes") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                teacher_id: teacherId,
                class_assignments: classAssignments
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showStep(4);
                showToast('success', data.message);
            } else {
                showToast('error', data.message || 'Error saving class assignments');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('error', 'Network error. Please try again.');
        });
    }

        // Save Subject Assignments AND send notification

        function saveSubjectAssignments() {
            const subjectCheckboxes = document.querySelectorAll('input[name="subject_ids[]"]:checked');
            const subjectIds = Array.from(subjectCheckboxes).map(cb => cb.value);

            fetch('{{ route("teachers.store.subjects") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    teacher_id: teacherId,
                    subject_ids: subjectIds
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // ✅ Notification already sent by backend
                    console.log('✅ Teacher created! Notification sent to', data.users_notified || 0, 'users');

                    // Show success message
                    document.getElementById('step4Content').classList.add('hidden');
                    document.getElementById('successMessage').classList.remove('hidden');
                    document.getElementById('successEmployeeId').textContent = document.getElementById('employee_id').value;
                    document.getElementById('successEmail').textContent = document.getElementById('email').value || data.email;

                    // Add notification info if available
                    if (data.users_notified) {
                        const notificationInfo = document.createElement('div');
                        notificationInfo.className = 'mt-4 p-3 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800';
                        notificationInfo.innerHTML = `
                            <div class="flex items-center text-green-600 dark:text-green-400">
                                <i class="fas fa-bell mr-2"></i>
                                <span class="font-medium">Notification sent to ${data.users_notified} user${data.users_notified !== 1 ? 's' : ''}</span>
                            </div>
                        `;
                        document.getElementById('successMessage').appendChild(notificationInfo);
                    }

                    showToast('success', 'Teacher registration completed successfully!');
                } else {
                    showToast('error', data.message || 'Error saving subject assignments');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('error', 'Network error. Please try again.');
            });
        }



// Photo Preview Functionality
    function previewPhoto(event) {
        const input = event.target;
        const preview = document.getElementById('photoPreview');
        const progress = document.getElementById('uploadProgress');

        if (input.files && input.files[0]) {
            const reader = new FileReader();

            // Show progress
            progress.classList.remove('hidden');

            reader.onload = function(e) {
                preview.innerHTML = `<img src="${e.target.result}" class="w-full h-full object-cover">`;
                progress.classList.add('hidden');
            }

            reader.onerror = function() {
                progress.classList.add('hidden');
                showToast('error', 'Error loading image. Please try again.');
            }

            reader.readAsDataURL(input.files[0]);
        }
    }

    function clearPhoto() {
        document.getElementById('photo').value = '';
        const preview = document.getElementById('photoPreview');
        preview.innerHTML = '<i class="fas fa-user text-gray-400 dark:text-gray-500 text-4xl"></i>';
    }

    // Reset form for new entry
    function resetForm() {
        // Reset all forms
        document.getElementById('basicInfoForm').reset();
        document.getElementById('additionalDetailsForm').reset();
        document.getElementById('teacherId').value = '';
        document.getElementById('teacher_id').value = '';

        // Clear photo
        clearPhoto();

        // Uncheck all checkboxes
        document.querySelectorAll('input[type="checkbox"]').forEach(cb => cb.checked = false);
        document.querySelectorAll('.class-teacher-checkbox').forEach(cb => cb.disabled = true);

        // Reset step indicator
        showStep(1);

        // Hide success message
        document.getElementById('successMessage').classList.add('hidden');

        // Auto-generate new employee ID
        const randomNum = Math.floor(1000 + Math.random() * 9000);
        const year = new Date().getFullYear();
        document.getElementById('employee_id').value = `EMP-${year}-${randomNum}`;

        showToast('success', 'Form reset. Ready to add new teacher.');
    }

    // Enable class teacher checkbox only when class is selected
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-generate employee ID
        const randomNum = Math.floor(1000 + Math.random() * 9000);
        const year = new Date().getFullYear();
        document.getElementById('employee_id').value = `EMP-${year}-${randomNum}`;

        // Set current date for employment date
        document.getElementById('employment_date').valueAsDate = new Date();

        // Handle class checkbox changes
        document.querySelectorAll('.class-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const classTeacherCheckbox = this.closest('.flex.items-center.justify-between')
                    .querySelector('.class-teacher-checkbox');
                classTeacherCheckbox.disabled = !this.checked;
                if (!this.checked) {
                    classTeacherCheckbox.checked = false;
                }
            });
        });
    });

    // Toast notification
    function showToast(type, message) {
        const toast = document.createElement('div');
        toast.className = `fixed top-4 right-4 z-50 px-6 py-4 rounded-xl shadow-xl transform transition-all duration-300 translate-x-full ${type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'}`;
        toast.innerHTML = `
            <div class="flex items-center">
                <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} mr-3"></i>
                <span>${message}</span>
            </div>
        `;
        document.body.appendChild(toast);

        setTimeout(() => {
            toast.classList.remove('translate-x-full');
        }, 10);

        setTimeout(() => {
            toast.classList.add('translate-x-full');
            setTimeout(() => {
                document.body.removeChild(toast);
            }, 300);
        }, 5000);
    }
</script>
@endpush

<style>
    /* Step indicator styles */
    .step-circle {
        width: 3rem;
        height: 3rem;
        border-radius: 50%;
        border: 3px solid #e5e7eb;
        background: white;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        transition: all 0.3s ease;
    }

    .dark .step-circle {
        background: #374151;
        border-color: #4b5563;
    }

    .step-circle.active {
        border-color: #800000;
        background: #800000;
    }

    .step-number {
        font-weight: 600;
        color: #6b7280;
    }

    .dark .step-number {
        color: #d1d5db;
    }

    .step-circle.active .step-number {
        color: white;
    }

    .step-check {
        color: white;
        font-size: 1.25rem;
    }

    .step-label {
        font-size: 0.875rem;
        color: #6b7280;
        font-weight: 500;
    }

    .dark .step-label {
        color: #9ca3af;
    }

    /* Checkbox styling */
    .class-checkbox:checked + span {
        font-weight: 600;
        color: #800000;
    }

    .dark .class-checkbox:checked + span {
        color: #ff6b6b;
    }

    /* Form validation */
    input:invalid, select:invalid, textarea:invalid {
        border-color: #ef4444;
    }

    input:valid, select:valid, textarea:valid {
        border-color: #10b981;
    }

    /* Spinner animation */
    .spinner {
        border: 2px solid rgba(255, 255, 255, 0.3);
        border-top: 2px solid #fff;
        border-radius: 50%;
        width: 24px;
        height: 24px;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
</style>

@if(!request()->header('HX-Request'))
    @endsection
@endif
