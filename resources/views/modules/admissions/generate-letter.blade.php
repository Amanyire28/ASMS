@extends('layouts.app')

@section('title', 'Generate Admission Letter')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-6">
    {{-- Header --}}
    <div class="mb-6">
        <a href="{{ route('students.show', $student) }}" class="text-blue-600 hover:text-blue-700 flex items-center mb-4">
            <i class="fas fa-arrow-left mr-2"></i> Back to Student
        </a>
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
            <i class="fas fa-envelope mr-2"></i> Generate Admission Letter
        </h1>
        <p class="text-gray-600 dark:text-gray-400 mt-2">
            Student: <strong>{{ $student->full_name }}</strong> ({{ $student->student_id }})
        </p>
    </div>

    @if($errors->any())
    <div class="mb-6 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
        <ul class="list-disc list-inside text-red-700 dark:text-red-300">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    {{-- Previous Letters --}}
    @if($latestLetter)
    <div class="mb-6 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
        <h3 class="font-semibold text-blue-900 dark:text-blue-100 mb-2">
            <i class="fas fa-history mr-2"></i> Previous Admission Letter
        </h3>
        <p class="text-sm text-blue-800 dark:text-blue-200 mb-3">
            Generated on {{ $latestLetter->generated_at->format('F d, Y \a\t h:i A') }}
        </p>
        <div class="flex gap-2">
            <a href="{{ route('admissions.letter.view', $latestLetter) }}" 
               class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm transition-colors">
                <i class="fas fa-eye mr-1"></i> View
            </a>
            <a href="{{ route('admissions.letter.print', $latestLetter) }}" 
               class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg text-sm transition-colors" target="_blank">
                <i class="fas fa-print mr-1"></i> Print
            </a>
        </div>
    </div>
    @endif

    {{-- Generate Form --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
        <form method="POST" action="{{ route('admissions.letter.store', $student) }}">
            @csrf

            {{-- Student Info Display --}}
            <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Full Name</p>
                        <p class="font-semibold text-gray-900 dark:text-white">{{ $student->full_name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Student ID</p>
                        <p class="font-semibold text-gray-900 dark:text-white">{{ $student->student_id }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Class</p>
                        <p class="font-semibold text-gray-900 dark:text-white">{{ $student->class?->name ?? 'Not Assigned' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Date of Birth</p>
                        <p class="font-semibold text-gray-900 dark:text-white">{{ $student->date_of_birth?->format('F d, Y') ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>

            {{-- Remarks --}}
            <div class="mb-6">
                <label for="remarks" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Additional Remarks (Optional)
                </label>
                <textarea name="remarks" id="remarks" rows="4"
                    class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-3
                           dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500
                           focus:border-transparent"
                    placeholder="Any special notes or conditions for this admission..."></textarea>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    These remarks will be included in the admission letter
                </p>
            </div>

            {{-- Actions --}}
            <div class="flex gap-3">
                <button type="submit"
                    class="flex-1 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold
                           rounded-lg transition-colors flex items-center justify-center">
                    <i class="fas fa-magic mr-2"></i> Generate Letter
                </button>
                <a href="{{ route('students.show', $student) }}"
                   class="px-6 py-3 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600
                          text-gray-800 dark:text-white font-semibold rounded-lg transition-colors">
                    Cancel
                </a>
            </div>
        </form>
    </div>

    {{-- Info Box --}}
    <div class="mt-6 p-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg">
        <p class="text-sm text-yellow-800 dark:text-yellow-200">
            <i class="fas fa-info-circle mr-2"></i>
            <strong>Note:</strong> A new admission letter will be generated each time you click "Generate Letter".
            You can view, print, or regenerate admission letters anytime from the student's profile.
        </p>
    </div>
</div>
@endsection
