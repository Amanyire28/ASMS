@extends('layouts.app')

@section('title', 'View Admission Letter')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-6">
    {{-- Navigation --}}
    <div class="mb-6">
        <a href="{{ route('students.show', $letter->student) }}" class="text-blue-600 hover:text-blue-700 flex items-center">
            <i class="fas fa-arrow-left mr-2"></i> Back to Student Profile
        </a>
    </div>

    {{-- Letter Display --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-8 min-h-screen">
        {{-- Print Header --}}
        <div class="mb-6 pb-6 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Admission Letter</h1>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                    Student: <strong>{{ $letter->student->full_name }}</strong>
                </p>
            </div>
            <div class="text-right">
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Generated: {{ $letter->generated_at->format('F d, Y \a\t h:i A') }}
                </p>
            </div>
        </div>

        {{-- Letter Content --}}
        <div class="letter-preview mb-8" style="line-height: 1.8; font-size: 14px;">
            @include('modules.admissions.letter-template', ['student' => $letter->student, 'remarks' => $letter->remarks, 'schoolSettings' => $schoolSettings])
        </div>

        {{-- Action Buttons --}}
        <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700 flex gap-3">
            <button onclick="window.print()" 
                class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition-colors flex items-center">
                <i class="fas fa-print mr-2"></i> Print Letter
            </button>
            <a href="{{ route('admissions.letter.create', $letter->student) }}"
               class="px-6 py-3 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600
                      text-gray-800 dark:text-white font-semibold rounded-lg transition-colors flex items-center">
                <i class="fas fa-sync mr-2"></i> Regenerate
            </a>
            <form method="POST" action="{{ route('admissions.letter.destroy', $letter) }}" 
                  class="inline"
                  onsubmit="return confirm('Are you sure you want to delete this admission letter?');">
                @csrf
                @method('DELETE')
                <button type="submit"
                    class="px-6 py-3 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg transition-colors flex items-center">
                    <i class="fas fa-trash mr-2"></i> Delete
                </button>
            </form>
        </div>
    </div>
</div>

<style media="print">
    body {
        background: white;
    }
    .mb-6, .mt-8, .pt-6, button, a {
        display: none !important;
    }
    .letter-preview {
        padding: 0;
    }
</style>
@endsection
