{{-- resources/views/modules/students/import.blade.php --}}

@if(!request()->header('HX-Request'))
    @extends('layouts.app')
    @section('title', 'Import Students')
    @section('content')
@endif

<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Import Students</h1>
    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Upload a CSV file to add multiple students at once.</p>
</div>

<div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
    <div class="p-6">
        @if(session('error'))
            <div class="mb-4 p-4 rounded-lg bg-red-50 dark:bg-red-900 text-red-700 dark:text-red-100">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('students.import.submit') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <div class="space-y-2">
                <label for="students_file" class="block text-sm font-medium text-gray-700 dark:text-gray-300">CSV File</label>
                <input type="file" name="students_file" id="students_file" accept=".csv" required
                       class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-maroon focus:border-transparent dark:bg-gray-700 dark:text-white @error('students_file') border-red-500 @enderror">
                @error('students_file')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div class="rounded-lg border border-dashed border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-900 p-4 text-sm text-gray-600 dark:text-gray-300">
                <p class="font-semibold mb-2">CSV format guide</p>
                <p class="mb-1">Your file should contain a header row with any of the following columns. Only <strong>first_name</strong>, <strong>last_name</strong>, <strong>date_of_birth</strong>, <strong>gender</strong>, and <strong>enrollment_date</strong> are required.</p>
                <ul class="list-disc pl-5 space-y-1">
                    <li><strong>first_name</strong></li>
                    <li><strong>last_name</strong></li>
                    <li><strong>date_of_birth</strong> (YYYY-MM-DD)</li>
                    <li><strong>gender</strong> (male, female, other)</li>
                    <li><strong>email</strong></li>
                    <li><strong>phone</strong></li>
                    <li><strong>address</strong></li>
                    <li><strong>parent_name</strong></li>
                    <li><strong>parent_phone</strong></li>
                    <li><strong>parent_email</strong></li>
                    <li><strong>class_id</strong> or <strong>class_name</strong></li>
                    <li><strong>enrollment_date</strong></li>
                    <li><strong>is_active</strong> (true/false)</li>
                </ul>
                <p class="mt-3">Example row:</p>
                <pre class="bg-gray-900 text-white text-xs p-3 rounded">first_name,last_name,date_of_birth,gender,email,class_name,enrollment_date,is_active
Jane,Doe,2010-05-20,female,jane.doe@example.com,Grade 7,2026-01-15,true</pre>
            </div>

            <div class="flex items-center gap-3">
                <button type="submit" class="inline-flex items-center px-5 py-3 bg-maroon hover:bg-maroon-dark text-white rounded-lg transition-colors">
                    <i class="fas fa-file-upload mr-2"></i>
                    Upload and Import
                </button>
                <a href="{{ route('students.index') }}"
                   hx-get="{{ route('students.index') }}"
                   hx-target="#page-content"
                   hx-push-url="true"
                   hx-indicator="#loading-indicator"
                   class="inline-flex items-center px-5 py-3 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
                    Cancel
                </a>
            </div>
        </form>

        @if(session('import_errors'))
            <div class="mt-6 bg-yellow-50 dark:bg-yellow-900 border border-yellow-200 dark:border-yellow-700 rounded-lg p-4 text-sm text-yellow-800 dark:text-yellow-100">
                <p class="font-semibold mb-2">Import issues</p>
                <ul class="list-disc pl-5 space-y-1">
                    @foreach(session('import_errors') as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
</div>

@if(!request()->header('HX-Request'))
    @endsection
@endif
