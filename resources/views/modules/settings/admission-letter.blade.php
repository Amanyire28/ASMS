@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    {{-- Header --}}
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Admission Letter Settings</h1>
            <p class="text-gray-500 mt-1">Customize the content and template of admission letters</p>
        </div>
    </div>

    {{-- Messages --}}
    @if ($errors->any())
    <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
        <p class="text-sm font-medium text-red-800 mb-2">Please correct the following errors:</p>
        <ul class="text-sm text-red-700 space-y-1">
            @foreach ($errors->all() as $error)
                <li>• {{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    @if (session('admission_success'))
    <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg">
        <p class="text-sm font-medium text-green-800">✓ {{ session('admission_success') }}</p>
    </div>
    @endif

    {{-- Admission Letter Settings Form --}}
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900">Letter Template Content</h2>
            <p class="text-sm text-gray-500 mt-1">Configure the content that appears in admission letters</p>
        </div>

        <form method="POST" action="{{ route('settings.update-admission-letter') }}" class="p-6 space-y-6">
            @csrf

            {{-- Opening Paragraph --}}
            <div>
                <label for="admission_letter_opening" class="block text-sm font-medium text-gray-700 mb-2">
                    Opening Paragraph <span class="text-red-500">*</span>
                </label>
                <textarea 
                    id="admission_letter_opening"
                    name="admission_letter_opening"
                    rows="4"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('admission_letter_opening') border-red-500 @enderror"
                    placeholder="Enter the opening paragraph of the admission letter..."
                    required>{{ old('admission_letter_opening', $admissionSettings['admission_letter_opening'] ?? '') }}</textarea>
                <p class="text-sm text-gray-500 mt-2">
                    This paragraph appears after "Dear [Student Name]," and should welcome the student and inform them about their admission.
                </p>
                @error('admission_letter_opening')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Requirements Section --}}
            <div>
                <label for="admission_letter_requirements" class="block text-sm font-medium text-gray-700 mb-2">
                    Admission Requirements <span class="text-red-500">*</span>
                </label>
                <textarea 
                    id="admission_letter_requirements"
                    name="admission_letter_requirements"
                    rows="5"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('admission_letter_requirements') border-red-500 @enderror font-mono text-sm"
                    placeholder="Enter requirements, one per line&#10;Use • for bullet points&#10;Lines will be preserved in the letter"
                    required>{{ old('admission_letter_requirements', $admissionSettings['admission_letter_requirements'] ?? '') }}</textarea>
                <p class="text-sm text-gray-500 mt-2">
                    List the requirements students must complete. Each line will appear on a new line with bullet points. Use the format:
                </p>
                <div class="mt-2 p-3 bg-gray-50 border border-gray-200 rounded text-sm font-mono text-gray-700">
                    • Complete admission documentation<br>
                    • Submit certified academic records<br>
                    • Pay admission and registration fees
                </div>
                @error('admission_letter_requirements')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Closing Paragraph --}}
            <div>
                <label for="admission_letter_closing" class="block text-sm font-medium text-gray-700 mb-2">
                    Closing Paragraph <span class="text-red-500">*</span>
                </label>
                <textarea 
                    id="admission_letter_closing"
                    name="admission_letter_closing"
                    rows="4"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('admission_letter_closing') border-red-500 @enderror"
                    placeholder="Enter the closing paragraph of the admission letter..."
                    required>{{ old('admission_letter_closing', $admissionSettings['admission_letter_closing'] ?? '') }}</textarea>
                <p class="text-sm text-gray-500 mt-2">
                    This paragraph appears after the requirements and should encourage the student and offer support.
                </p>
                @error('admission_letter_closing')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Contact Information --}}
            <div>
                <label for="admission_letter_contact_info" class="block text-sm font-medium text-gray-700 mb-2">
                    Contact Information <span class="text-gray-400">(Optional)</span>
                </label>
                <input 
                    type="text"
                    id="admission_letter_contact_info"
                    name="admission_letter_contact_info"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('admission_letter_contact_info') border-red-500 @enderror"
                    placeholder="e.g., Admissions Office: +256 700 000 000 | Email: admissions@school.ac.ug"
                    value="{{ old('admission_letter_contact_info', $admissionSettings['admission_letter_contact_info'] ?? '') }}">
                <p class="text-sm text-gray-500 mt-2">
                    Additional contact information for the admissions office (optional).
                </p>
                @error('admission_letter_contact_info')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Preview Section --}}
            <div class="mt-8 pt-6 border-t border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Preview</h3>
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-6 space-y-3 text-sm text-gray-700 font-serif">
                    <p>Dear [Student Name],</p>
                    <p class="italic text-gray-600">{{ old('admission_letter_opening', $admissionSettings['admission_letter_opening'] ?? '[Opening paragraph will appear here]') }}</p>
                    <p class="font-semibold mt-4">To finalize your admission:</p>
                    <div class="whitespace-pre-wrap text-gray-600 ml-4">{{ old('admission_letter_requirements', $admissionSettings['admission_letter_requirements'] ?? '[Requirements will appear here]') }}</div>
                    <p class="italic text-gray-600 mt-4">{{ old('admission_letter_closing', $admissionSettings['admission_letter_closing'] ?? '[Closing paragraph will appear here]') }}</p>
                    <p class="mt-6">Yours sincerely,</p>
                </div>
            </div>

            {{-- Submit Button --}}
            <div class="flex justify-end pt-6 border-t border-gray-200">
                <button 
                    type="submit"
                    class="inline-flex items-center px-6 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Save Changes
                </button>
            </div>
        </form>
    </div>

    {{-- Info Box --}}
    <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
        <h4 class="text-sm font-medium text-blue-900 mb-2">💡 Tips for Writing Admission Letters</h4>
        <ul class="text-sm text-blue-800 space-y-1">
            <li>• Keep the opening paragraph welcoming and congratulatory</li>
            <li>• List requirements in a clear, numbered or bulleted format</li>
            <li>• Make the closing paragraph encouraging and supportive</li>
            <li>• Include contact information for student inquiries</li>
            <li>• Keep the tone professional yet warm</li>
            <li>• Mention the school name only in the opening paragraph (it's added automatically)</li>
        </ul>
    </div>
</div>
@endsection
