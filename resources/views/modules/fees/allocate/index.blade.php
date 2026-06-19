@extends('layouts.app')

@section('title', 'Allocate Fees')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Allocate Fees & Record Payments</h1>
        <p class="text-gray-600 mt-2">View student balances and record fee payments</p>
    </div>

    @if (session('success'))
        <div class="mb-4 px-4 py-3 bg-green-100 border border-green-400 text-green-700 rounded">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mb-4 px-4 py-3 bg-red-100 border border-red-400 text-red-700 rounded">
            {{ session('error') }}
        </div>
    @endif

    <!-- Summary Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-600">
            <div class="text-sm text-gray-600 uppercase tracking-wide">Total Students</div>
            <div class="text-3xl font-bold text-blue-600 mt-2" id="totalStudentsCount">{{ count($studentData) }}</div>
        </div>
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-yellow-600">
            <div class="text-sm text-gray-600 uppercase tracking-wide">Total Due</div>
            <div class="text-3xl font-bold text-yellow-600 mt-2" id="totalDueAmount">
                {{ number_format($studentData->sum('total_due'), 2) }}
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-red-600">
            <div class="text-sm text-gray-600 uppercase tracking-wide">Outstanding Balance</div>
            <div class="text-3xl font-bold text-red-600 mt-2" id="outstandingBalance">
                {{ number_format($studentData->sum('balance'), 2) }}
            </div>
        </div>
    </div>

    <!-- Search Box -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <div class="relative">
            <input 
                type="text" 
                id="studentSearch" 
                class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                placeholder="Search by student name, ID, or email... (type to search)"
            >
            <i class="fas fa-search absolute right-4 top-3.5 text-gray-400"></i>
            <div id="searchResults" class="absolute top-full left-0 right-0 mt-2 bg-white border border-gray-300 rounded-lg shadow-lg z-50 max-h-96 overflow-y-auto hidden">
                <!-- Results will be populated here -->
            </div>
        </div>
    </div>

    <!-- Students Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">Student Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">Student ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">Class</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-600 uppercase">Total Due</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-600 uppercase">Amount Paid</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-600 uppercase">Balance</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-600 uppercase">Status</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-600 uppercase">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y" id="studentsTableBody">
                @forelse ($studentData as $data)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">{{ $data['student']->full_name }}</div>
                            <div class="text-xs text-gray-500">{{ $data['student']->email }}</div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $data['student']->student_id ?? 'N/A' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $data['student']->class->name ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 text-right">
                            <span class="text-sm font-semibold text-gray-900">{{ number_format($data['total_due'], 2) }}</span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <span class="text-sm font-semibold text-green-600">{{ number_format($data['amount_paid'], 2) }}</span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <span class="text-sm font-semibold {{ $data['balance'] > 0 ? 'text-red-600' : 'text-green-600' }}">
                                {{ number_format($data['balance'], 2) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if ($data['balance'] <= 0)
                                <span class="px-3 py-1 text-xs font-semibold bg-green-100 text-green-800 rounded-full">
                                    Paid Up
                                </span>
                            @elseif ($data['amount_paid'] > 0)
                                <span class="px-3 py-1 text-xs font-semibold bg-yellow-100 text-yellow-800 rounded-full">
                                    Partial
                                </span>
                            @else
                                <span class="px-3 py-1 text-xs font-semibold bg-red-100 text-red-800 rounded-full">
                                    Outstanding
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            <button class="inline-flex items-center px-3 py-1 bg-blue-600 text-white rounded text-xs font-medium hover:bg-blue-700 transition view-details-btn" data-student-id="{{ $data['student']->id }}">
                                <i class="fas fa-eye mr-1"></i> View
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-8 text-center text-gray-500">
                            <i class="fas fa-inbox text-3xl text-gray-300 mb-2"></i>
                            <p>No students found</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Student Details Modal -->
<div id="detailsModal" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full mx-4 max-h-96 overflow-y-auto">
        <div class="sticky top-0 bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4 flex justify-between items-center">
            <h2 class="text-xl font-bold text-white">Student Fee Details</h2>
            <button onclick="closeModal()" class="text-white hover:text-gray-200 transition">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div id="modalContent" class="p-6">
            <!-- Content will be loaded here -->
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('studentSearch');
    const searchResults = document.getElementById('searchResults');
    let searchTimeout;

    // Search functionality
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        const query = this.value.trim();

        if (query.length < 1) {
            searchResults.classList.add('hidden');
            return;
        }

        searchTimeout = setTimeout(() => {
            fetch(`{{ route('fees.search-students') }}?q=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.data.length > 0) {
                        let html = '';
                        data.data.forEach(student => {
                            html += `
                                <div class="px-4 py-3 border-b hover:bg-blue-50 cursor-pointer transition student-result" data-student-id="${student.id}">
                                    <div class="font-medium text-gray-900">${student.name}</div>
                                    <div class="text-xs text-gray-500">${student.student_id} • ${student.class}</div>
                                    <div class="text-xs mt-1">
                                        <span class="text-gray-600">Due: Sh ${parseFloat(student.total_due).toFixed(2)}</span> | 
                                        <span class="text-green-600">Paid: Sh ${parseFloat(student.amount_paid).toFixed(2)}</span> | 
                                        <span class="text-red-600">Balance: Sh ${parseFloat(student.balance).toFixed(2)}</span>
                                    </div>
                                </div>
                            `;
                        });
                        searchResults.innerHTML = html;
                        searchResults.classList.remove('hidden');

                        // Add click handlers to search results
                        document.querySelectorAll('.student-result').forEach(el => {
                            el.addEventListener('click', function() {
                                const studentId = this.dataset.studentId;
                                searchInput.value = '';
                                searchResults.classList.add('hidden');
                                showStudentDetails(studentId);
                            });
                        });
                    } else {
                        searchResults.innerHTML = '<div class="px-4 py-3 text-center text-gray-500">No students found</div>';
                        searchResults.classList.remove('hidden');
                    }
                })
                .catch(error => {
                    console.error('Search error:', error);
                    searchResults.innerHTML = '<div class="px-4 py-3 text-center text-red-500">Search failed</div>';
                    searchResults.classList.remove('hidden');
                });
        }, 300);
    });

    // Close search results when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('#studentSearch') && !e.target.closest('#searchResults')) {
            searchResults.classList.add('hidden');
        }
    });

    // View details button handlers
    document.querySelectorAll('.view-details-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const studentId = this.dataset.studentId;
            showStudentDetails(studentId);
        });
    });
});

function showStudentDetails(studentId) {
    // Get student data from search results or make a call
    fetch(`{{ route('fees.search-students') }}?q=${studentId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.data.length > 0) {
                const student = data.data[0];
                const statusBadge = student.balance <= 0 ? 'Paid Up' : (student.amount_paid > 0 ? 'Partial' : 'Outstanding');
                const statusColor = student.balance <= 0 ? 'bg-green-100 text-green-800' : (student.amount_paid > 0 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800');
                
                let feesHtml = '';
                student.fees.forEach(fee => {
                    const feePaid = parseFloat(fee.paid).toFixed(2);
                    const feeBalance = (parseFloat(fee.amount) - feePaid).toFixed(2);
                    feesHtml += `
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-4 py-2 text-sm text-gray-900">${fee.name}</td>
                            <td class="px-4 py-2 text-sm text-right">Sh ${parseFloat(fee.amount).toFixed(2)}</td>
                            <td class="px-4 py-2 text-sm text-right text-green-600">Sh ${feePaid}</td>
                            <td class="px-4 py-2 text-sm text-right ${feeBalance > 0 ? 'text-red-600' : 'text-green-600'}">Sh ${feeBalance}</td>
                        </tr>
                    `;
                });

                const html = `
                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-xs text-gray-500 uppercase">Student Name</p>
                                <p class="text-lg font-semibold text-gray-900">${student.name}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 uppercase">Student ID</p>
                                <p class="text-lg font-semibold text-gray-900">${student.student_id}</p>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-xs text-gray-500 uppercase">Class</p>
                                <p class="text-lg font-semibold text-gray-900">${student.class}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 uppercase">Email</p>
                                <p class="text-sm text-gray-900">${student.email}</p>
                            </div>
                        </div>

                        <div class="bg-gray-50 p-4 rounded-lg">
                            <p class="text-xs text-gray-500 uppercase mb-3 font-semibold">Fee Summary</p>
                            <div class="grid grid-cols-3 gap-4">
                                <div>
                                    <p class="text-xs text-gray-500">Total Due</p>
                                    <p class="text-xl font-bold text-gray-900">Sh ${parseFloat(student.total_due).toFixed(2)}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Amount Paid</p>
                                    <p class="text-xl font-bold text-green-600">Sh ${parseFloat(student.amount_paid).toFixed(2)}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Outstanding</p>
                                    <p class="text-xl font-bold text-red-600">Sh ${parseFloat(student.balance).toFixed(2)}</p>
                                </div>
                            </div>
                            <div class="mt-3">
                                <span class="px-3 py-1 text-xs font-semibold ${statusColor} rounded-full">${statusBadge}</span>
                            </div>
                        </div>

                        <div>
                            <p class="text-xs text-gray-500 uppercase font-semibold mb-2">Assigned Fees</p>
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm">
                                    <thead class="bg-gray-100">
                                        <tr>
                                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600">Fee Type</th>
                                            <th class="px-4 py-2 text-right text-xs font-semibold text-gray-600">Amount</th>
                                            <th class="px-4 py-2 text-right text-xs font-semibold text-gray-600">Paid</th>
                                            <th class="px-4 py-2 text-right text-xs font-semibold text-gray-600">Balance</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        ${feesHtml}
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="flex gap-2">
                            <a href="/admin/fees/allocate-fees/${student.id}" class="flex-1 text-center px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition text-sm font-medium">
                                <i class="fas fa-receipt mr-1"></i> View Full Ledger
                            </a>
                            <button onclick="closeModal()" class="flex-1 px-4 py-2 bg-gray-300 text-gray-900 rounded hover:bg-gray-400 transition text-sm font-medium">
                                Close
                            </button>
                        </div>
                    </div>
                `;
                
                document.getElementById('modalContent').innerHTML = html;
                document.getElementById('detailsModal').classList.remove('hidden');
            }
        })
        .catch(error => console.error('Error loading details:', error));
}

function closeModal() {
    document.getElementById('detailsModal').classList.add('hidden');
}

// Close modal when clicking outside
document.getElementById('detailsModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal();
    }
});
</script>

@endsection
