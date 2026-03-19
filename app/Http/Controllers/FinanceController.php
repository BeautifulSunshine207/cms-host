<?php

namespace App\Http\Controllers;

use App\Models\PayrollRequest;
use App\Models\ExpenseRequest;
use App\Models\FinanceFund;
use App\Models\Inventory;
use App\Models\InventoryTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class FinanceController extends Controller
{
    public function index()
    {
        $stats = $this->getFinanceStats();
        $payrollRequests = PayrollRequest::where('status', 'pending')->get();
        $expenseRequests = ExpenseRequest::where('status', 'pending')->get();
        
        return view('admin.finance.index', compact('stats', 'payrollRequests', 'expenseRequests'));
    }

    private function getFinanceStats()
    {
        $totalExpenses = ExpenseRequest::where('status', 'complete')
            ->get()
            ->sum(function($request) {
                $raw = (string) ($request->total ?? '');
                $clean = preg_replace('/[^0-9.]/', '', $raw);
                return (float) $clean;
            });

        $payrollCost = PayrollRequest::where('status', 'complete')
            ->get()
            ->sum(function($request) {
                $raw = (string) ($request->rate ?? '');
                $clean = preg_replace('/[^0-9.]/', '', $raw);
                return (float) $clean;
            });

        $pendingRequests = PayrollRequest::where('status', 'pending')->count() + 
                          ExpenseRequest::where('status', 'pending')->count();

        $fund = FinanceFund::query()->first() ?? FinanceFund::create(['balance' => 1200000]);
        $fundsReleased = (float) ($fund->balance ?? 0);

        $lastReport = Carbon::now('Asia/Manila')->format('F d, Y');

        return [
            'total_expenses' => "\u{20B1}" . number_format($totalExpenses, 2),
            'payroll_cost' => "\u{20B1}" . number_format($payrollCost, 2),
            'pending_requests' => $pendingRequests,
            'last_report' => $lastReport,
            'funds_released' => "\u{20B1}" . number_format($fundsReleased, 2),
            'expenses_count' => ExpenseRequest::where('status', 'pending')->count(),
            'payroll_entries' => PayrollRequest::where('status', 'pending')->count(),
        ];
    }

    private function parseMoney($value): float
    {
        $raw = (string) ($value ?? '');
        $clean = preg_replace('/[^0-9.]/', '', $raw);
        return (float) $clean;
    }

    public function completePayroll($id)
    {
        return DB::transaction(function () use ($id) {
            $payroll = PayrollRequest::whereKey($id)->lockForUpdate()->firstOrFail();
            $amount = $this->parseMoney($payroll->rate);

            $fund = FinanceFund::lockForUpdate()->first();
            if (!$fund) {
                $fund = FinanceFund::create(['balance' => 1200000]);
            }

            if ($fund->balance < $amount) {
                return redirect()->route('finance.index', ['tab' => 'payroll'])
                    ->with('error', 'Insufficient funds released for this payroll request.');
            }

            $fund->balance = (float) $fund->balance - $amount;
            $fund->save();
            $payroll->update(['status' => 'complete']);
            
            return redirect()->route('finance.index', ['tab' => 'payroll'])
                ->with('success', 'Payroll request completed');
        });
    }

    public function completeExpense($id)
    {
        return DB::transaction(function () use ($id) {
            $expense = ExpenseRequest::whereKey($id)->lockForUpdate()->firstOrFail();
            if ($expense->status === 'complete') {
                return redirect()->route('finance.index', ['tab' => 'expense'])
                    ->with('success', 'Expense request already completed');
            }
            $amount = $this->parseMoney($expense->total);

            $fund = FinanceFund::lockForUpdate()->first();
            if (!$fund) {
                $fund = FinanceFund::create(['balance' => 1200000]);
            }

            if ($fund->balance < $amount) {
                return redirect()->route('finance.index', ['tab' => 'expense'])
                    ->with('error', 'Insufficient funds released for this expense request.');
            }

            $fund->balance = (float) $fund->balance - $amount;
            $fund->save();

            if ($expense->material_id && $expense->quantity_value) {
                $inventory = Inventory::lockForUpdate()->firstOrCreate(
                    ['material_id' => $expense->material_id],
                    ['current_quantity' => 0, 'threshold_quantity' => 0, 'max_threshold' => null]
                );

                $inventory->current_quantity = (float) $inventory->current_quantity + (float) $expense->quantity_value;
                $inventory->save();

                InventoryTransaction::create([
                    'material_id' => $expense->material_id,
                    'type' => 'stock_in',
                    'quantity' => $expense->quantity_value,
                    'remaining_stock' => $inventory->current_quantity,
                    'project_id' => null,
                    'remarks' => 'stock in',
                ]);
            }

            $expense->update(['status' => 'complete']);
            
            return redirect()->route('finance.index', ['tab' => 'expense'])
                ->with('success', 'Expense request completed');
        });
    }

    public function addFunds(Request $request)
    {
        $data = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01'],
            'tab' => ['nullable', 'in:payroll,expense,manual'],
        ]);

        DB::transaction(function () use ($data) {
            $fund = FinanceFund::lockForUpdate()->first();
            if (!$fund) {
                $fund = FinanceFund::create(['balance' => 1200000]);
            }
            $fund->balance = (float) $fund->balance + (float) $data['amount'];
            $fund->save();
        });

        $tab = $data['tab'] ?? 'payroll';
        return redirect()->route('finance.index', ['tab' => $tab])
            ->with('success', 'Funds released updated.');
    }

    public function storeManualRecord(Request $request)
    {
        $validated = $request->validate([
            'materials' => 'required|string',
            'date' => 'required|date',
            'quantity' => 'required|string',
            'requested_by' => 'required|string',
            'price_per_unit' => 'required|string',
            'total' => 'required|string',
        ]);

        $validated['status'] = 'complete';

        ExpenseRequest::create($validated);
        
        return redirect()->back()->with('success', 'Manual record saved');
    }
}
