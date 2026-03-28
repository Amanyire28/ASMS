@if(!request()->header('HX-Request'))
@extends('layouts.app')
@section('title', 'Report Card Settings')
@section('content')
@endif

<div class="space-y-6">
    {{-- Header --}}
    <div class="flex items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Report Card Settings</h1>
            <p class="text-sm text-gray-500 mt-1">Configure letterhead, signatures, and footer text for generated reports</p>
        </div>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-800 rounded-lg px-4 py-3 flex items-center gap-2">
        <i class="fas fa-check-circle text-green-500"></i>
        {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="bg-red-50 border border-red-200 text-red-800 rounded-lg px-4 py-3 flex items-center gap-2">
        <i class="fas fa-exclamation-circle text-red-500"></i>
        {{ session('error') }}
    </div>
    @endif

    {{-- Settings Form --}}
    <div class="bg-white shadow-sm rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 bg-gray-50">
            <h2 class="font-semibold text-gray-800 flex items-center gap-2">
                <i class="fas fa-file-alt text-blue-500"></i>
                Report Card Configuration
            </h2>
        </div>

        <form action="{{ route('settings.update-report-card') }}" method="POST"
              enctype="multipart/form-data" class="p-6 space-y-6">
            @csrf

            {{-- Letterhead Text --}}
            <div>
                <label for="letterhead_text" class="block text-sm font-medium text-gray-700 mb-1">
                    Letterhead / Tagline
                </label>
                <input type="text" id="letterhead_text" name="letterhead_text"
                       value="{{ old('letterhead_text', $reportSettings['letterhead_text'] ?? '') }}"
                       placeholder="e.g. Excellence in Education | P.O Box 123, Kampala"
                       maxlength="1000"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('letterhead_text') border-red-400 @enderror">
                @error('letterhead_text')
                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-gray-400">Appears beneath the school name on the report header.</p>
            </div>

            {{-- Principal Name --}}
            <div>
                <label for="principal_name" class="block text-sm font-medium text-gray-700 mb-1">
                    Principal's Name <span class="text-red-500">*</span>
                </label>
                <input type="text" id="principal_name" name="principal_name" required
                       value="{{ old('principal_name', $reportSettings['principal_name'] ?? '') }}"
                       placeholder="e.g. Mr. John Doe"
                       maxlength="255"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('principal_name') border-red-400 @enderror">
                @error('principal_name')
                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Signatures --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                {{-- Principal Signature --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Principal's Signature</label>
                    @if(!empty($reportSettings['principal_signature']) && \Illuminate\Support\Facades\Storage::disk('public')->exists($reportSettings['principal_signature']))
                    <div class="mb-3">
                        <p class="text-xs text-gray-500 mb-1">Current signature:</p>
                        <img src="{{ asset('storage/' . $reportSettings['principal_signature']) }}"
                             alt="Principal Signature"
                             class="h-16 border border-gray-200 rounded p-1 bg-gray-50 object-contain">
                        <form action="{{ route('settings.delete-signature') }}" method="POST" class="mt-1">
                            @csrf @method('DELETE')
                            <input type="hidden" name="type" value="principal">
                            <button type="submit" onclick="return confirm('Remove this signature?')"
                                    class="text-xs text-red-500 hover:underline">
                                Remove
                            </button>
                        </form>
                    </div>
                    @endif
                    <input type="file" id="principal_signature" name="principal_signature"
                           accept="image/png,image/jpeg,image/jpg"
                           class="w-full text-sm text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 @error('principal_signature') border border-red-400 rounded @enderror">
                    @error('principal_signature')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-400">PNG/JPG, max 1 MB. Use a transparent background for best results.</p>
                </div>

                {{-- Head Teacher Signature --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Head Teacher's Signature</label>
                    @if(!empty($reportSettings['headteacher_signature']) && \Illuminate\Support\Facades\Storage::disk('public')->exists($reportSettings['headteacher_signature']))
                    <div class="mb-3">
                        <p class="text-xs text-gray-500 mb-1">Current signature:</p>
                        <img src="{{ asset('storage/' . $reportSettings['headteacher_signature']) }}"
                             alt="Head Teacher Signature"
                             class="h-16 border border-gray-200 rounded p-1 bg-gray-50 object-contain">
                        <form action="{{ route('settings.delete-signature') }}" method="POST" class="mt-1">
                            @csrf @method('DELETE')
                            <input type="hidden" name="type" value="headteacher">
                            <button type="submit" onclick="return confirm('Remove this signature?')"
                                    class="text-xs text-red-500 hover:underline">
                                Remove
                            </button>
                        </form>
                    </div>
                    @endif
                    <input type="file" id="headteacher_signature" name="headteacher_signature"
                           accept="image/png,image/jpeg,image/jpg"
                           class="w-full text-sm text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 @error('headteacher_signature') border border-red-400 rounded @enderror">
                    @error('headteacher_signature')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-400">PNG/JPG, max 1 MB.</p>
                </div>
            </div>

            {{-- Report Footer Text --}}
            <div>
                <label for="report_footer_text" class="block text-sm font-medium text-gray-700 mb-1">
                    Footer Text
                </label>
                <textarea id="report_footer_text" name="report_footer_text" rows="3" maxlength="500"
                          placeholder="e.g. This report is computer-generated and is valid without a stamp."
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('report_footer_text') border-red-400 @enderror">{{ old('report_footer_text', $reportSettings['report_footer_text'] ?? '') }}</textarea>
                @error('report_footer_text')
                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-gray-400">Appears at the bottom of each generated report. Max 500 characters.</p>
            </div>

            {{-- Grade Scale Reference --}}
            <div class="bg-gray-50 rounded-lg border border-gray-200 px-4 py-4">
                <h3 class="text-xs font-semibold text-gray-600 uppercase tracking-wide mb-3">
                    <i class="fas fa-info-circle mr-1"></i> Grade Scale (System Default)
                </h3>
                <div class="grid grid-cols-4 md:grid-cols-8 gap-2">
                    @foreach(['A+' => '≥90%', 'A' => '≥80%', 'B+' => '≥70%', 'B' => '≥60%', 'C+' => '≥50%', 'C' => '≥40%', 'D' => '≥30%', 'F' => '<30%'] as $grade => $range)
                    <div class="text-center bg-white rounded border border-gray-200 py-2 px-1">
                        <div class="font-bold text-blue-700 text-sm">{{ $grade }}</div>
                        <div class="text-xs text-gray-500">{{ $range }}</div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Submit --}}
            <div class="flex justify-end pt-2">
                <button type="submit"
                        class="inline-flex items-center gap-2 px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition-colors">
                    <i class="fas fa-save"></i>
                    Save Settings
                </button>
            </div>
        </form>
    </div>
</div>

@if(!request()->header('HX-Request'))
@endsection
@endif
