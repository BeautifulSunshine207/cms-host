@extends('layouts.app')

@section('content')
@php
    $activeTab = request('tab', 'payroll');
@endphp
<style>
    .print-only { display: none; }
    .screen-only { display: block; }

    @media print {
        .screen-only { display: none !important; }
        .print-only { display: block !important; }

        .print-page {
            font-family: "Times New Roman", Times, serif;
            color: #000;
            max-width: 7.5in;
            margin: 0 auto;
        }
        .print-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            font-size: 12pt;
            margin-bottom: 14pt;
        }
        .print-title {
            font-weight: bold;
        }
        .print-section-title {
            font-size: 12pt;
            margin: 10pt 0 6pt;
        }
        .print-section-title strong {
            font-weight: bold;
        }
        .print-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11pt;
            margin-bottom: 14pt;
        }
        .print-table th,
        .print-table td {
            border: 1px solid #000;
            padding: 4px 6px;
            vertical-align: top;
        }
        .print-table th {
            text-align: center;
            font-weight: bold;
        }
        .print-footer {
            margin-top: 12pt;
            font-size: 13pt;
            font-weight: bold;
        }
    }
</style>
<div class="print-only">
    <div class="print-page">
        <div class="print-header">
            <div class="print-title">Finance Report</div>
            <div>{{ now('Asia/Manila')->format('F d, Y') }}</div>
        </div>

        <div class="print-section-title">
            <strong>Total Payroll Cost:</strong> {{ $stats['payroll_cost'] ?? "\u{20B1}0" }}
        </div>
        <div class="print-section-title">Current Payroll Request:</div>
        <table class="print-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Rate</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($payrollRequests as $request)
                    <tr>
                        <td>{{ $request->name }}</td>
                        <td>{{ $request->rate }}</td>
                        <td>{{ optional($request->date)->format('m-d-y') }}</td>
                        <td>Waiting to be approved</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4">No payroll requests found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="print-section-title">
            <strong>Total Expenses:</strong> {{ $stats['total_expenses'] ?? "\u{20B1}0" }}
        </div>
        <div class="print-section-title">Current Expense Request:</div>
        <table class="print-table">
            <thead>
                <tr>
                    <th>Materials</th>
                    <th>Stock-in</th>
                    <th>Price</th>
                    <th>Total</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($expenseRequests as $request)
                    <tr>
                        <td>{{ $request->materials }}</td>
                        <td>{{ $request->quantity }}</td>
                        <td>{{ $request->price_per_unit }}</td>
                        <td>{{ $request->total }}</td>
                        <td>{{ optional($request->date)->format('m-d-y') }}</td>
                        <td>Waiting to be approved</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">No expense requests found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="print-footer">Company Remaining Funds: {{ $stats['funds_released'] ?? "\u{20B1}0" }}</div>
    </div>
</div>
<div class="screen-only">
<div class="min-h-screen bg-[#ECECEC] px-8 py-8">
    <!--heder row-->
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-4">
            <h1 class="text-2xl font-bold text-gray-900">Finance Management</h1>
        </div>

        <!--serch right-->
        <div class="w-[520px] max-w-full flex items-center gap-4">
            <button
                type="button"
                data-report="print"
                class="no-print inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-[#f6c915] text-black font-semibold hover:brightness-95 transition"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Generate Report
            </button>
            <div class="relative flex-1">
                <input
                    type="text"
                    placeholder="Search"
                    class="w-full rounded-full border border-gray-400 bg-[#EDEDED] px-5 py-2 pr-12 text-sm outline-none"
                >
                <div class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-600">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                        <path d="M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15Z" stroke="currentColor" stroke-width="2"/>
                        <path d="M16.5 16.5 21 21" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>


    @if(session('success'))
        <div class="mb-6 rounded-xl bg-green-100 px-4 py-3 text-sm font-semibold text-green-800">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-6 rounded-xl bg-red-100 px-4 py-3 text-sm font-semibold text-red-800">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-5 gap-6 mb-8">
        <!--payroll cost-->
        <div class="rounded-2xl bg-[#3E3E3E] p-5 shadow-[0_12px_24px_rgba(0,0,0,0.18)]">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-gray-300 text-sm">Payroll Cost</p>
                    <p class="text-white text-2xl font-bold mt-2">{{ $stats['payroll_cost'] ?? "\u{20B1}0" }}</p>
                    <p class="text-xs text-gray-300 mt-3">Total approved payroll this period</p>
                    <p class="text-xs text-green-400 mt-2">▲ 1.3% Up from last month</p>
                </div>
                <div class="text-[#FFCC00]">
                    <svg class="w-10 h-10" viewBox="0 0 24 24" fill="none">
                        <path d="M12 3a9 9 0 1 0 9 9" stroke="currentColor" stroke-width="2"/>
                        <path d="M12 7v6l4 2" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        <path d="M21 12V3h-9" stroke="currentColor" stroke-width="2"/>
                    </svg>
                </div>
            </div>
        </div>

        <!--total expense-->
        <div class="rounded-2xl bg-[#3E3E3E] p-5 shadow-[0_12px_24px_rgba(0,0,0,0.18)]">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-gray-300 text-sm">Total Expenses</p>
                    <p class="text-white text-2xl font-bold mt-2">{{ $stats['total_expenses'] ?? "\u{20B1}0" }}</p>
                    <p class="text-xs text-gray-300 mt-3">All approved expenses this period</p>
                    <p class="text-xs text-green-400 mt-2">▲ 8.5% Up from last month</p>
                </div>
                <div class="text-[#FFCC00]">
                    <!--resit icon-->
                    <svg class="w-10 h-10" viewBox="0 0 24 24" fill="none">
                        <path d="M6 3h12v18l-2-1-2 1-2-1-2 1-2-1-2 1V3Z" stroke="currentColor" stroke-width="2"/>
                        <path d="M9 7h6M9 11h6M9 15h4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </div>
            </div>
        </div>

        <!--pending reqs-->
        <div class="rounded-2xl bg-[#3E3E3E] p-5 shadow-[0_12px_24px_rgba(0,0,0,0.18)]">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-gray-300 text-sm">Pending Requests</p>
                    <p class="text-white text-2xl font-bold mt-2">{{ $stats['pending_requests'] ?? 0 }}</p>
                    <div class="text-xs text-gray-300 mt-3 space-y-1">
                        <p class="flex items-center gap-2"><span class="text-red-400">●</span> {{ $stats['expenses_count'] ?? 0 }} expenses</p>
                        <p class="flex items-center gap-2"><span class="text-gray-200">●</span> {{ $stats['payroll_entries'] ?? 0 }} payroll entries</p>
                    </div>
                </div>
                <div class="text-[#FFCC00]">
                    <svg class="w-10 h-10" viewBox="0 0 24 24" fill="none">
                        <path d="M12 8v5l3 2" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        <path d="M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" stroke="currentColor" stroke-width="2"/>
                    </svg>
                </div>
            </div>
        </div>

        <!--last report gen-->
        <div class="rounded-2xl bg-[#3E3E3E] p-5 shadow-[0_12px_24px_rgba(0,0,0,0.18)]">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-gray-300 text-sm">Last Report<br>Generated</p>
                    <p class="text-white text-xl font-bold mt-4">{{ $stats['last_report'] ?? '—' }}</p>
                </div>
                <div class="text-[#FFCC00]">
                    <svg class="w-10 h-10" viewBox="0 0 24 24" fill="none">
                        <path d="M7 3h7l3 3v15a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1Z" stroke="currentColor" stroke-width="2"/>
                        <path d="M14 3v4h4" stroke="currentColor" stroke-width="2"/>
                    </svg>
                </div>
            </div>
        </div>

        <!--funds release-->
        <div class="relative rounded-2xl bg-[#3E3E3E] p-5 shadow-[0_12px_24px_rgba(0,0,0,0.18)]">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-gray-300 text-sm">Funds Released</p>
                    <p class="text-white text-2xl font-bold mt-2">{{ $stats['funds_released'] ?? "\u{20B1}0" }}</p>
                    <p class="text-xs text-gray-300 mt-3">Remaining Money</p>
                </div>
                <div class="text-[#FFCC00]">
                    <svg class="w-10 h-10" viewBox="0 0 24 24" fill="none">
                        <path d="M12 1v22" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7H14a3.5 3.5 0 0 1 0 7H7" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </div>
            </div>
            <button
                type="button"
                class="absolute bottom-4 right-4 inline-flex items-center gap-2 rounded-full px-3 py-1.5 text-xs font-semibold border border-white/20 text-[#FFCC00] shadow hover:brightness-50 transition"
                onclick="openFundsModal()"
            >
                <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-black/10">
                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none">
                        <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </span>
                Add Funds
            </button>
        </div>
    </div>

    <!--tabs parehas sa screen-->
    <div class="flex items-center gap-16 mb-3">
        <button class="finance-tab font-semibold {{ $activeTab === 'payroll' ? 'active text-gray-900' : 'text-gray-400' }}" onclick="switchTab(event,'payroll')">Payroll Request</button>
        <button class="finance-tab font-semibold {{ $activeTab === 'expense' ? 'active text-gray-900' : 'text-gray-400' }}" onclick="switchTab(event,'expense')">Expense Request</button>
        <button class="finance-tab font-semibold {{ $activeTab === 'manual' ? 'active text-gray-900' : 'text-gray-400' }}" onclick="switchTab(event,'manual')">Manual Record</button>
    </div>

    <!--container gaya sa screen-->
    <div class="rounded-2xl border border-gray-400/60 bg-[#ECECEC] p-4">
        <!--note lang-->
        <div class="mb-4 rounded-lg bg-gray-200 px-4 py-3 text-xs text-gray-700 flex items-center gap-2">
            <span class="text-red-500 font-bold">!</span>
            Requests marked as Complete are saved as paid and will appear in finance summary reports.
        </div>

        <!--payrol tab-->
        <div id="payrollTab" class="{{ $activeTab === 'payroll' ? '' : 'hidden' }}">
            <div class="rounded-2xl bg-[#3E3E3E] overflow-hidden">
                <table class="w-full text-sm">
                    <thead class="text-white">
                        <tr class="border-b border-white/10">
                            <th class="text-left px-6 py-4 font-semibold">Name</th>
                            <th class="text-left px-6 py-4 font-semibold">Files/Proof</th>
                            <th class="text-left px-6 py-4 font-semibold">Check</th>
                            <th class="text-left px-6 py-4 font-semibold">Rate</th>
                            <th class="text-left px-6 py-4 font-semibold">Date</th>
                            <th class="text-left px-6 py-4 font-semibold">Action</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-200">
                        @forelse($payrollRequests as $request)
                            <tr class="border-b border-white/10">
                                <td class="px-6 py-5">{{ $request->name }}</td>
                                <td class="px-6 py-5">
                                    <div class="flex items-center gap-3">
                                        <span class="inline-flex items-center justify-center w-8 h-8 rounded bg-white/10">
                                            <span class="text-xs font-bold text-red-400">PDF</span>
                                        </span>
                                        <span>{{ $request->file_name }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-5">
                                    <a href="{{ $request->file_path ? asset($request->file_path) : '#' }}"
                                       class="text-blue-500 font-semibold hover:underline"
                                       target="_blank"
                                    >VIEW</a>
                                </td>
                                <td class="px-6 py-5 text-green-500 font-semibold">{{ $request->rate }}</td>
                                <td class="px-6 py-5">{{ optional($request->date)->format('m-d-y') }}</td>
                                <td class="px-6 py-5">
                                    <form action="{{ route('finance.payroll.complete', $request->id) }}" method="POST">
                                        @csrf
                                        <button class="inline-flex items-center gap-2 rounded bg-[#FFCC00] px-4 py-2 text-black font-semibold hover:brightness-95">
                                            <span class="inline-flex items-center justify-center w-6 h-6 rounded bg-black/10">
                                                ✓
                                            </span>
                                            Complete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-10 text-center text-gray-300">No payroll requests found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!--expens tab-->
        <div id="expenseTab" class="{{ $activeTab === 'expense' ? '' : 'hidden' }}">
            <div class="rounded-2xl bg-[#3E3E3E] overflow-hidden">
                <table class="w-full text-sm">
                    <thead class="text-white">
                        <tr class="border-b border-white/10">
                            <th class="text-left px-6 py-4 font-semibold">Materials</th>
                            <th class="text-left px-6 py-4 font-semibold">Quantity (Stock-in)</th>
                            <th class="text-left px-6 py-4 font-semibold">Price per Unit</th>
                            <th class="text-left px-6 py-4 font-semibold">Total</th>
                            <th class="text-left px-6 py-4 font-semibold">Date</th>
                            <th class="text-left px-6 py-4 font-semibold">Action</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-200">
                        @forelse($expenseRequests as $request)
                            <tr class="border-b border-white/10">
                                <td class="px-6 py-5">{{ $request->materials }}</td>
                                <td class="px-6 py-5">{{ $request->quantity }}</td>
                                <td class="px-6 py-5 text-gray-300">{{ $request->price_per_unit }}</td>
                                <td class="px-6 py-5 text-green-500 font-semibold">{{ $request->total }}</td>
                                <td class="px-6 py-5">{{ optional($request->date)->format('m-d-y') }}</td>
                                <td class="px-6 py-5">
                                    <form action="{{ route('finance.expense.complete', $request->id) }}" method="POST">
                                        @csrf
                                        <button class="inline-flex items-center gap-2 rounded bg-[#FFCC00] px-4 py-2 text-black font-semibold hover:brightness-95">
                                            <span class="inline-flex items-center justify-center w-6 h-6 rounded bg-black/10">
                                                ✓
                                            </span>
                                            Complete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-10 text-center text-gray-300">No expense requests found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!--manual tab-->
        <div id="manualTab" class="{{ $activeTab === 'manual' ? '' : 'hidden' }}">
            <div class="grid grid-cols-12 gap-6">
                <div class="col-span-12 xl:col-span-8">
                    <div class="rounded-2xl border border-gray-500/40 bg-[#3E3E3E] p-6">
                        <form action="{{ route('finance.manual-record.store') }}" method="POST" class="grid grid-cols-2 gap-6" id="manualForm">
                            @csrf

                            <div>
                                <label class="text-sm text-gray-200">Expense Name</label>
                                <input name="materials" required class="mt-2 w-full bg-transparent border-b border-gray-500 outline-none text-white py-2" placeholder=" ">
                            </div>

                            <div>
                                <label class="text-sm text-gray-200">Date</label>
                                <input type="date" name="date" required class="mt-2 w-full bg-transparent border-b border-gray-500 outline-none text-white py-2">
                            </div>

                            <div>
                                <label class="text-sm text-gray-200">Quantity (Stock-in)</label>
                                <input name="quantity" required class="mt-2 w-full bg-transparent border-b border-gray-500 outline-none text-white py-2" placeholder=" ">
                            </div>

                            <div>
                                <label class="text-sm text-gray-200">Requested by</label>
                                <input name="requested_by" class="mt-2 w-full bg-transparent border-b border-gray-500 outline-none text-white py-2" placeholder=" ">
                            </div>

                            <div>
                                <label class="text-sm text-gray-200">Price per unit</label>
                                <input name="price_per_unit" id="pricePerUnit" required class="mt-2 w-full bg-transparent border-b border-gray-500 outline-none text-white py-2" placeholder=" ">
                            </div>

                            <div>
                                <label class="text-sm text-gray-200">Total</label>
                                <input name="total" id="totalAmount" readonly class="mt-2 w-full bg-transparent border-b border-gray-500 outline-none text-white py-2" placeholder=" ">
                            </div>

                            <div class="col-span-2 flex justify-end mt-4">
                                <button class="inline-flex items-center gap-2 rounded bg-[#FFCC00] px-8 py-3 text-black font-semibold hover:brightness-95">
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded bg-black/10">✓</span>
                                    Save
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="col-span-12 xl:col-span-4 space-y-6">
                    <div class="rounded-2xl bg-white p-5 shadow">
                        <p class="text-sm font-semibold text-gray-900">Payroll Distribution by Project this Month</p>
                        <div class="mt-4 h-40 rounded-xl bg-gray-100 flex items-center justify-center text-gray-500 text-sm">
                            Chart placeholder
                        </div>
                    </div>
                    <div class="rounded-2xl bg-white p-5 shadow">
                        <p class="text-sm font-semibold text-gray-900">Payroll Distribution – Past Few Months</p>
                        <div class="mt-4 h-40 rounded-xl bg-gray-100 flex items-center justify-center text-gray-500 text-sm">
                            Chart placeholder
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
</div>

<!--add funds modal-->
<div id="fundsModal" class="fixed inset-0 hidden z-50">
    <div class="absolute inset-0 bg-black/40"></div>
    <div class="relative max-w-md mx-auto mt-40 rounded-3xl overflow-hidden shadow-[0_20px_50px_rgba(0,0,0,0.35)]">
        <div class="bg-[#3E3E3E] p-6 text-white">
            <div class="flex items-center justify-between">
                <div class="text-lg font-semibold">Add Funds</div>
                <button class="text-2xl text-gray-300" onclick="closeFundsModal()">&times;</button>
            </div>
            <p class="mt-2 text-xs text-gray-300">Update the total released funds.</p>
        </div>
        <div class="bg-white p-6">
            <form action="{{ route('finance.funds.add') }}" method="POST">
                @csrf
                <input type="hidden" name="tab" id="fundsTab" value="{{ $activeTab }}">
                <label class="text-xs font-semibold text-gray-700">Amount</label>
                <input
                    type="number"
                    name="amount"
                    step="0.01"
                    min="0.01"
                    required
                    class="mt-2 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-yellow-200"
                    placeholder="0.00"
                >
                <button class="mt-4 w-full rounded-xl bg-[#FFCC00] py-3 text-sm font-semibold text-black hover:brightness-95">
                    Add Funds
                </button>
            </form>
        </div>
    </div>
</div>

<style>
    
    .finance-tab { position: relative; padding-bottom: 10px; }
    .finance-tab.active { color: #111827; }
    .finance-tab.active::after{
        content:"";
        position:absolute;
        left:0; right:0; bottom:0;
        height:2px;
        background:#111827;
        border-radius:999px;
    }
</style>

<script>
    function switchTab(e, tab) {
        //buttons lang
        document.querySelectorAll('.finance-tab').forEach(btn => {
            btn.classList.remove('active');
            btn.classList.add('text-gray-400');
        });
        e.currentTarget.classList.add('active');
        e.currentTarget.classList.remove('text-gray-400');

        //panels lang
        document.getElementById('payrollTab').classList.toggle('hidden', tab !== 'payroll');
        document.getElementById('expenseTab').classList.toggle('hidden', tab !== 'expense');
        document.getElementById('manualTab').classList.toggle('hidden', tab !== 'manual');

        const tabInput = document.getElementById('fundsTab');
        if (tabInput) {
            tabInput.value = tab;
        }

        try {
            const url = new URL(window.location.href);
            url.searchParams.set('tab', tab);
            window.history.replaceState({}, '', url.toString());
        } catch (e) {
            // ignore url update errors
        }
    }

    function openFundsModal() {
        const modal = document.getElementById('fundsModal');
        modal?.classList.remove('hidden');
    }

    function closeFundsModal() {
        const modal = document.getElementById('fundsModal');
        modal?.classList.add('hidden');
    }

    //manual record compute total, simple lang
    const qtyEl = document.querySelector('#manualForm input[name="quantity"]');
    const priceEl = document.getElementById('pricePerUnit');
    const totalEl = document.getElementById('totalAmount');

    function parseNumber(v){
        if(!v) return 0;
        return Number(String(v).replace(/[^\d.]/g,'') || 0);
    }

    function updateTotal(){
        const q = parseNumber(qtyEl?.value);
        const p = parseNumber(priceEl?.value);
        const total = q * p;
        totalEl.value = total ? `₱${total.toLocaleString()}` : '';
    }

    qtyEl?.addEventListener('input', updateTotal);
    priceEl?.addEventListener('input', updateTotal);
</script>
@endsection



