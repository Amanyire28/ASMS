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

    {{-- ═══════════════════════════════════════════════════════ --}}
    {{-- Exam Types Card                                         --}}
    {{-- ═══════════════════════════════════════════════════════ --}}
    <div class="bg-white shadow-sm rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 bg-gray-50 flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-layer-group text-indigo-500"></i>
                    Exam Types Configuration
                </h2>
                <p class="text-xs text-gray-500 mt-0.5">
                    Define each examination type (e.g. BOT, Midterm, EOT). These appear as columns on the marks entry form and on report cards.
                    The <strong>Code</strong> must be unique, short, and never changed after marks are entered.
                </p>
            </div>
        </div>

        @if(session('exam_success'))
        <div class="mx-6 mt-4 bg-green-50 border border-green-200 text-green-800 rounded-lg px-4 py-3 flex items-center gap-2 text-sm">
            <i class="fas fa-check-circle text-green-500"></i>{{ session('exam_success') }}
        </div>
        @endif

        <div class="p-6" x-data="{
            rows: {{ Js::from(count($examTypes) ? $examTypes : [['id'=>'Final','label'=>'Final Exam','max_marks'=>100]]) }},
            addRow() { this.rows.push({ id: '', label: '', max_marks: 100 }); },
            removeRow(i) { this.rows.splice(i, 1); }
        }">
            <form action="{{ route('settings.update-exam-types') }}" method="POST">
                @csrf

                <div class="overflow-x-auto rounded-lg border border-gray-200 mb-4">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-200">
                                <th class="px-4 py-2 text-left font-semibold text-gray-600 text-xs uppercase w-4">#</th>
                                <th class="px-4 py-2 text-left font-semibold text-gray-600 text-xs uppercase">
                                    Code <span class="text-red-500">*</span>
                                    <span class="font-normal text-gray-400 ml-1">(stored in DB, never change after use)</span>
                                </th>
                                <th class="px-4 py-2 text-left font-semibold text-gray-600 text-xs uppercase">
                                    Display Label <span class="text-red-500">*</span>
                                </th>
                                <th class="px-4 py-2 text-left font-semibold text-gray-600 text-xs uppercase w-32">
                                    Default Max Marks
                                </th>
                                <th class="px-4 py-2 text-center font-semibold text-gray-600 text-xs uppercase w-16">Remove</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(row, i) in rows" :key="i">
                                <tr class="border-b border-gray-100 last:border-b-0">
                                    <td class="px-4 py-2 text-gray-400 text-xs" x-text="i + 1"></td>
                                    <td class="px-4 py-2">
                                        <input :name="'exam_types['+i+'][id]'"
                                               x-model="row.id"
                                               placeholder="e.g. BOT"
                                               maxlength="30"
                                               pattern="[A-Za-z0-9_\-]+"
                                               title="Letters, numbers, hyphens and underscores only"
                                               required
                                               class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm uppercase focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                    </td>
                                    <td class="px-4 py-2">
                                        <input :name="'exam_types['+i+'][label]'"
                                               x-model="row.label"
                                               placeholder="e.g. Beginning of Term"
                                               maxlength="60"
                                               required
                                               class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                    </td>
                                    <td class="px-4 py-2">
                                        <input :name="'exam_types['+i+'][max_marks]'"
                                               x-model.number="row.max_marks"
                                               type="number" min="1" max="1000" step="0.5"
                                               required
                                               class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm text-center focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                    </td>
                                    <td class="px-4 py-2 text-center">
                                        <button type="button" @click="removeRow(i)"
                                                class="text-red-400 hover:text-red-600 transition-colors"
                                                title="Remove exam type">
                                            <i class="fas fa-times-circle"></i>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                            <tr x-show="rows.length === 0">
                                <td colspan="5" class="px-4 py-6 text-center text-gray-400 text-sm italic">
                                    No exam types defined. Add at least one below.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="flex items-center justify-between">
                    <button type="button" @click="addRow()"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 border border-dashed border-indigo-400
                                   text-indigo-600 hover:bg-indigo-50 rounded-lg text-sm transition-colors">
                        <i class="fas fa-plus text-xs"></i> Add Exam Type
                    </button>
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-5 py-2 bg-indigo-600 hover:bg-indigo-700
                                   text-white text-sm font-semibold rounded-lg transition-colors">
                        <i class="fas fa-save"></i> Save Exam Types
                    </button>
                </div>

                @error('exam_types.*.id')
                <p class="mt-2 text-xs text-red-500">{{ $message }}</p>
                @enderror
                @error('exam_types.*.label')
                <p class="mt-2 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </form>

            <div class="mt-4 bg-amber-50 border border-amber-200 rounded-lg px-4 py-3 text-xs text-amber-800">
                <i class="fas fa-exclamation-triangle mr-1"></i>
                <strong>Important:</strong> Once marks are recorded, do <em>not</em> change or remove exam type codes — doing so will cause historical marks to become unlinked.
                The default code <code class="bg-amber-100 px-1 rounded">Final</code> matches all marks entered before exam types were configured.
            </div>
        </div>
    </div>

    {{-- Report Card Configuration Card --}}
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

            {{-- Logo Header Text --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Header Logo Text
                    <span class="text-gray-400 font-normal text-xs ml-1">— text on each side of the school logo</span>
                </label>
                <div class="flex items-center gap-3">
                    <div class="flex-1">
                        <input type="text" id="logo_left_text" name="logo_left_text"
                               value="{{ old('logo_left_text', $reportSettings['logo_left_text'] ?? '') }}"
                               placeholder="e.g. ASMS"
                               maxlength="100"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('logo_left_text') border-red-400 @enderror">
                        <p class="mt-1 text-xs text-gray-400 text-center">Left of logo</p>
                    </div>
                    <div class="shrink-0 flex flex-col items-center text-gray-400 text-xs px-2">
                        <i class="fas fa-image text-2xl text-gray-300 mb-1"></i>
                        LOGO
                    </div>
                    <div class="flex-1">
                        <input type="text" id="logo_right_text" name="logo_right_text"
                               value="{{ old('logo_right_text', $reportSettings['logo_right_text'] ?? '') }}"
                               placeholder="e.g. High School"
                               maxlength="100"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('logo_right_text') border-red-400 @enderror">
                        <p class="mt-1 text-xs text-gray-400 text-center">Right of logo</p>
                    </div>
                </div>
                <p class="mt-1 text-xs text-gray-400">If left blank, the school name will be auto-split around the logo.</p>
            </div>

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

            {{-- Admin-configurable grade thresholds --}}
            <div class="bg-white rounded-lg border border-gray-200 p-4">
                <h3 class="text-sm font-semibold text-gray-800 mb-2">Grade Thresholds (Admin)</h3>
                <p class="text-xs text-gray-500 mb-3">Set the minimum percentage for each grade. Values must be integers between 0 and 100 and strictly descending (A > B > C > D > E).</p>
                @php
                    $gt = json_decode($reportSettings['grade_thresholds'] ?? 'null', true) ?? [];
                    $ach = json_decode($reportSettings['grade_achievements'] ?? 'null', true) ?? [];
                    $ini = json_decode($reportSettings['grade_initials'] ?? 'null', true) ?? [];
                @endphp
                <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
                    @foreach(['A','B','C','D','E'] as $g)
                    <div>
                        <label class="text-xs font-medium text-gray-700">Grade {{ $g }} (min %)</label>
                        <input type="number" name="grade_thresholds[{{ $g }}]" min="0" max="100" step="1"
                               value="{{ old('grade_thresholds.'.$g, $gt[$g] ?? '') }}"
                               class="w-full border border-gray-300 rounded px-2 py-1 text-sm">
                    </div>
                    @endforeach
                </div>
                <p class="text-xs text-gray-400 mt-2">If left empty, system defaults will be used: A &ge;70, B &ge;60, C &ge;50, D &ge;40, E &ge;30.</p>
            </div>

            {{-- Achievement labels & initials --}}
            <div class="bg-white rounded-lg border border-gray-200 p-4">
                <h3 class="text-sm font-semibold text-gray-800 mb-2">Achievement Labels & Initials</h3>
                <p class="text-xs text-gray-500 mb-3">Customize the word label and one- to three-letter initial for each grade (A–F).</p>
                <div class="grid grid-cols-2 md:grid-cols-6 gap-3">
                    @foreach(['A','B','C','D','E','F'] as $g)
                    <div class="col-span-2">
                        <label class="text-xs font-medium text-gray-700">Grade {{ $g }} label</label>
                        <input type="text" name="grade_achievements[{{ $g }}]" maxlength="60"
                               value="{{ old('grade_achievements.'.$g, $ach[$g] ?? '') }}"
                               class="w-full border border-gray-300 rounded px-2 py-1 text-sm">
                        <label class="text-xs font-medium text-gray-700 mt-1">Initial</label>
                        <input type="text" name="grade_initials[{{ $g }}]" maxlength="3"
                               value="{{ old('grade_initials.'.$g, $ini[$g] ?? '') }}"
                               class="w-20 border border-gray-300 rounded px-2 py-1 text-sm">
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
