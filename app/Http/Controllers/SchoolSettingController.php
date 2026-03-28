<?php

namespace App\Http\Controllers;

use App\Models\SchoolSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class SchoolSettingController extends Controller
{
    /**
     * Display the school profile settings page
     */
    public function edit()
    {
        // Check permission
        abort_unless(auth()->user()->can('system.settings'), 403);

        // Get all settings grouped
        $basicSettings = SchoolSetting::getByGroup('basic')->pluck('value', 'key');
        $contactSettings = SchoolSetting::getByGroup('contact')->pluck('value', 'key');
        $academicSettings = SchoolSetting::getByGroup('academic')->pluck('value', 'key');
        $emailSettings = SchoolSetting::getByGroup('email')->pluck('value', 'key');
        $reportSettings = SchoolSetting::getByGroup('report')->pluck('value', 'key');

        // Decode term dates JSON
        $termDates = json_decode($academicSettings['term_dates'] ?? '[]', true);

        // List of countries for dropdown
        $countries = $this->getCountries();

        return view('modules.settings.school-profile', compact(
            'basicSettings',
            'contactSettings',
            'academicSettings',
            'emailSettings',
            'reportSettings',
            'termDates',
            'countries'
        ));
    }

    /**
     * Update basic information settings
     */
    public function updateBasicInfo(Request $request)
    {
        abort_unless(auth()->user()->can('system.settings'), 403);

        $validator = Validator::make($request->all(), [
            'school_name' => 'required|string|max:255',
            'school_motto' => 'nullable|string|max:500',
            'school_logo' => 'nullable|image|mimes:png,jpg,jpeg|max:2048', // 2MB max
            'registration_number' => 'nullable|string|max:100',
            'established_year' => 'nullable|integer|min:1900|max:' . date('Y'),
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Please correct the errors in the Basic Information section.');
        }

        // Handle logo upload
        if ($request->hasFile('school_logo')) {
            // Delete old logo
            $oldLogo = school_setting('school_logo');
            if ($oldLogo && Storage::disk('public')->exists($oldLogo)) {
                Storage::disk('public')->delete($oldLogo);
            }

            // Upload new logo
            $logoPath = $request->file('school_logo')->store('logos', 'public');
            SchoolSetting::set('school_logo', $logoPath, 'image', 'basic');
        }

        // Update other basic settings
        SchoolSetting::set('school_name', $request->school_name, 'text', 'basic');
        SchoolSetting::set('school_motto', $request->school_motto, 'text', 'basic');
        SchoolSetting::set('registration_number', $request->registration_number, 'text', 'basic');
        SchoolSetting::set('established_year', $request->established_year, 'integer', 'basic');

        return back()->with('success', 'Basic information updated successfully!');
    }

    /**
     * Update contact information settings
     */
    public function updateContactInfo(Request $request)
    {
        abort_unless(auth()->user()->can('system.settings'), 403);

        $validator = Validator::make($request->all(), [
            'physical_address' => 'required|string|max:500',
            'city' => 'required|string|max:100',
            'country' => 'required|string|max:100',
            'phone_primary' => 'required|regex:/^[0-9+\-\s()]+$/',
            'phone_secondary' => 'nullable|regex:/^[0-9+\-\s()]+$/',
            'official_email' => 'required|email|max:255',
            'website_url' => 'nullable|url|max:255',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Please correct the errors in the Contact Information section.');
        }

        // Update contact settings
        SchoolSetting::set('physical_address', $request->physical_address, 'text', 'contact');
        SchoolSetting::set('city', $request->city, 'text', 'contact');
        SchoolSetting::set('country', $request->country, 'text', 'contact');
        SchoolSetting::set('phone_primary', $request->phone_primary, 'text', 'contact');
        SchoolSetting::set('phone_secondary', $request->phone_secondary, 'text', 'contact');
        SchoolSetting::set('official_email', $request->official_email, 'text', 'contact');
        SchoolSetting::set('website_url', $request->website_url, 'text', 'contact');

        return back()->with('success', 'Contact information updated successfully!');
    }

    /**
     * Update academic structure settings
     */
    public function updateAcademicStructure(Request $request)
    {
        abort_unless(auth()->user()->can('system.settings'), 403);

        $validator = Validator::make($request->all(), [
            'current_academic_year' => 'required|regex:/^\d{4}\/\d{4}$/',
            'term_system' => 'required|in:3_terms,2_semesters',
            'current_term' => 'required|integer|min:1|max:3',
            'term_dates' => 'required|array',
            'term_dates.*.name' => 'required|string|max:100',
            'term_dates.*.start_date' => 'required|date',
            'term_dates.*.end_date' => 'required|date|after:term_dates.*.start_date',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Please correct the errors in the Academic Structure section.');
        }

        // Update academic settings
        SchoolSetting::set('current_academic_year', $request->current_academic_year, 'text', 'academic');
        SchoolSetting::set('term_system', $request->term_system, 'text', 'academic');
        SchoolSetting::set('current_term', $request->current_term, 'integer', 'academic');
        SchoolSetting::set('term_dates', $request->term_dates, 'json', 'academic');

        return back()->with('success', 'Academic structure updated successfully!');
    }

    /**
     * Update email configuration settings
     */
    public function updateEmailConfig(Request $request)
    {
        abort_unless(auth()->user()->can('system.settings'), 403);

        $validator = Validator::make($request->all(), [
            'email_domain' => 'required|regex:/^[a-z0-9\-\.]+\.[a-z]{2,}$/',
            'email_format' => 'required|in:firstname.lastname,staffid',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Please correct the errors in the Email Configuration section.');
        }

        // Update email settings
        SchoolSetting::set('email_domain', $request->email_domain, 'text', 'email');
        SchoolSetting::set('email_format', $request->email_format, 'text', 'email');

        // Update config cache
        config(['app.school_domain' => $request->email_domain]);

        return back()->with('success', 'Email configuration updated successfully!');
    }

    /**
     * Show report card settings page
     */
    public function editReportCardSettings()
    {
        abort_unless(auth()->user()->can('system.settings'), 403);

        $reportSettings = SchoolSetting::getByGroup('report')->pluck('value', 'key');

        return view('modules.settings.report-card', compact('reportSettings'));
    }

    /**
     * Update report card settings
     */
    public function updateReportCardSettings(Request $request)
    {
        abort_unless(auth()->user()->can('system.settings'), 403);

        $validator = Validator::make($request->all(), [
            'letterhead_text' => 'nullable|string|max:1000',
            'report_footer_text' => 'nullable|string|max:500',
            'principal_name' => 'required|string|max:255',
            'principal_signature' => 'nullable|image|mimes:png,jpg,jpeg|max:1024', // 1MB max
            'headteacher_signature' => 'nullable|image|mimes:png,jpg,jpeg|max:1024',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Please correct the errors in the Report Card Settings section.');
        }

        // Handle principal signature upload
        if ($request->hasFile('principal_signature')) {
            // Delete old signature
            $oldSignature = school_setting('principal_signature');
            if ($oldSignature && Storage::disk('public')->exists($oldSignature)) {
                Storage::disk('public')->delete($oldSignature);
            }

            // Upload new signature
            $signaturePath = $request->file('principal_signature')->store('signatures', 'public');
            SchoolSetting::set('principal_signature', $signaturePath, 'image', 'report');
        }

        // Handle headteacher signature upload
        if ($request->hasFile('headteacher_signature')) {
            // Delete old signature
            $oldSignature = school_setting('headteacher_signature');
            if ($oldSignature && Storage::disk('public')->exists($oldSignature)) {
                Storage::disk('public')->delete($oldSignature);
            }

            // Upload new signature
            $signaturePath = $request->file('headteacher_signature')->store('signatures', 'public');
            SchoolSetting::set('headteacher_signature', $signaturePath, 'image', 'report');
        }

        // Update other report settings
        SchoolSetting::set('letterhead_text', $request->letterhead_text, 'text', 'report');
        SchoolSetting::set('report_footer_text', $request->report_footer_text, 'text', 'report');
        SchoolSetting::set('principal_name', $request->principal_name, 'text', 'report');

        return back()->with('success', 'Report card settings updated successfully!');
    }

    /**
     * Delete uploaded logo
     */
    public function deleteLogo()
    {
        abort_unless(auth()->user()->can('system.settings'), 403);

        $logo = school_setting('school_logo');

        if ($logo && Storage::disk('public')->exists($logo)) {
            Storage::disk('public')->delete($logo);
            SchoolSetting::set('school_logo', null, 'image', 'basic');

            return back()->with('success', 'School logo deleted successfully!');
        }

        return back()->with('error', 'No logo found to delete.');
    }

    /**
     * Delete signature
     */
    public function deleteSignature(Request $request)
    {
        abort_unless(auth()->user()->can('system.settings'), 403);

        $type = $request->type; // 'principal' or 'headteacher'
        $key = $type . '_signature';

        $signature = school_setting($key);

        if ($signature && Storage::disk('public')->exists($signature)) {
            Storage::disk('public')->delete($signature);
            SchoolSetting::set($key, null, 'image', 'report');

            return back()->with('success', ucfirst($type) . ' signature deleted successfully!');
        }

        return back()->with('error', 'No signature found to delete.');
    }

    /**
     * Get list of countries
     */
    private function getCountries()
    {
        return [
            'Uganda',
            'Kenya',
            'Tanzania',
            'Rwanda',
            'Burundi',
            'South Sudan',
            'Ethiopia',
            'Somalia',
            'Democratic Republic of Congo',
            'Nigeria',
            'Ghana',
            'South Africa',
            'Egypt',
            'Morocco',
            'Algeria',
            'Zimbabwe',
            'Zambia',
            'Malawi',
            'Mozambique',
            'Botswana',
            'United Kingdom',
            'United States',
            'Canada',
            'Australia',
            'India',
            'China',
            // Add more countries as needed
        ];
    }
}
