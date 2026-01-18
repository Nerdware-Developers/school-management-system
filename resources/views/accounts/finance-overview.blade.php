@extends('layouts.master')

@section('content')
<div class="page-wrapper">
    <div class="content container-fluid">
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">Finance Overview</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('account/fees/collections/page') }}">Accounts</a></li>
                        <li class="breadcrumb-item active">Finance Overview</li>
                    </ul>
                </div>
                <div class="col-auto">
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <form method="GET" class="d-flex align-items-center me-3">
                            <label class="me-2 mb-0 text-muted">Academic Year</label>
                            <select name="year" class="form-select" style="min-width: 140px" onchange="this.form.submit()">
                                @forelse ($availableYears as $year)
                                    <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>
                                        {{ $year }}
                                    </option>
                                @empty
                                    <option value="">No Years</option>
                                @endforelse
                            </select>
                        </form>
                        <form method="GET" id="balanceFilterForm" class="d-flex align-items-center gap-2">
                            <input type="hidden" name="year" value="{{ $selectedYear }}">
                            <select name="balance_operator" class="form-select" style="min-width: 150px">
                                <option value="">Balance Filter</option>
                                <option value="greater" {{ request('balance_operator') == 'greater' ? 'selected' : '' }}>> Greater Than</option>
                                <option value="greater_equal" {{ request('balance_operator') == 'greater_equal' ? 'selected' : '' }}>>= Greater or Equal</option>
                                <option value="less" {{ request('balance_operator') == 'less' ? 'selected' : '' }}>< Less Than</option>
                                <option value="less_equal" {{ request('balance_operator') == 'less_equal' ? 'selected' : '' }}><= Less or Equal</option>
                                <option value="equal" {{ request('balance_operator') == 'equal' ? 'selected' : '' }}>= Equal To</option>
                                <option value="not_zero" {{ request('balance_operator') == 'not_zero' ? 'selected' : '' }}>≠ Not Zero</option>
                                <option value="zero" {{ request('balance_operator') == 'zero' ? 'selected' : '' }}>= Zero</option>
                            </select>
                            <input type="number" name="balance_amount" class="form-control" 
                                placeholder="Amount..." value="{{ request('balance_amount') }}" 
                                step="0.01" min="0" style="min-width: 120px">
                            <button type="submit" class="btn btn-primary">Filter</button>
                            <a href="#" id="downloadBalanceBtn" class="btn btn-outline-primary">
                                <i class="fas fa-download"></i> Download
                            </a>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100" style="background-color:#e0f2fe;">
                    <div class="card-body">
                        <p class="text-muted mb-1">Total Received</p>
                        <h3 class="fw-bold mb-0">Ksh{{ number_format($totalPaid, 2) }}</h3>
                        <small class="text-muted">Income ({{ $selectedYear ?? 'All Years' }})</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100" style="background-color:#fee2e2;">
                    <div class="card-body">
                        <p class="text-muted mb-1">Total Salaries</p>
                        <h3 class="fw-bold text-danger mb-0">Ksh{{ number_format($totalSalaries, 2) }}</h3>
                        <small class="text-muted">Staff payments</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100" style="background-color:#fef3c7;">
                    <div class="card-body">
                        <p class="text-muted mb-1">Total Expenses</p>
                        <h3 class="fw-bold text-warning mb-0">Ksh{{ number_format($totalExpenses, 2) }}</h3>
                        <small class="text-muted">Other expenses</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100" style="background-color:{{ $netProfit >= 0 ? '#dcfce7' : '#fee2e2' }};">
                    <div class="card-body">
                        <p class="text-muted mb-1">Net Profit</p>
                        <h3 class="fw-bold {{ $netProfit >= 0 ? 'text-success' : 'text-danger' }} mb-0">Ksh{{ number_format($netProfit, 2) }}</h3>
                        <small class="text-muted">{{ number_format($profitMargin, 1) }}% margin</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100" style="background-color:#f3f4f6;">
                    <div class="card-body">
                        <p class="text-muted mb-1">Total Expected</p>
                        <h4 class="fw-bold mb-0">Ksh{{ number_format($totalExpected, 2) }}</h4>
                        <small class="text-muted">Fee expectations</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100" style="background-color:#fee2e2;">
                    <div class="card-body">
                        <p class="text-muted mb-1">Outstanding</p>
                        <h4 class="fw-bold text-danger mb-0">Ksh{{ number_format($totalOutstanding, 2) }}</h4>
                        <small class="text-muted">Unpaid balances</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100" style="background-color:#fff7e6;">
                    <div class="card-body">
                        <p class="text-muted mb-1">Collection Rate</p>
                        <h4 class="fw-bold text-warning mb-0">{{ number_format($collectionRate, 1) }}%</h4>
                        <small class="text-muted">Expected vs received</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-lg-8">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold mb-0">Term Performance ({{ $selectedYear ?? 'All Years' }})</h5>
                            <span class="badge bg-light text-dark">{{ $termStats->count() }} terms</span>
                        </div>

                        @if($termStats->isEmpty())
                            <p class="text-muted mb-0">No finance data available for the selected year.</p>
                        @else
                            <div class="table-responsive">
                                <table class="table table-striped align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th>Term</th>
                                            <th>Expected</th>
                                            <th>Received</th>
                                            <th>Outstanding</th>
                                            <th>Credit</th>
                                            <th>Students</th>
                                            <th>Collection</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($termStats as $term)
                                            @php
                                                $termCollection = $term->expected_total > 0
                                                    ? ($term->paid_total / $term->expected_total) * 100
                                                    : 0;
                                            @endphp
                                            <tr>
                                                <td>
                                                    <strong>{{ $term->term_name }}</strong>
                                                    <div class="text-muted small">{{ $term->academic_year }}</div>
                                                </td>
                                                <td>Ksh{{ number_format($term->expected_total, 2) }}</td>
                                                <td class="text-success">Ksh{{ number_format($term->paid_total, 2) }}</td>
                                                <td class="text-danger">Ksh{{ number_format($term->outstanding_total, 2) }}</td>
                                                <td class="text-primary">Ksh{{ number_format($term->credit_total, 2) }}</td>
                                                <td>{{ $term->students_count }}</td>
                                                <td style="min-width:180px;">
                                                    <div class="d-flex align-items-center">
                                                        <div class="progress w-100 me-2" style="height:6px;">
                                                            <div class="progress-bar bg-success" role="progressbar"
                                                                 style="width: {{ min($termCollection, 100) }}%;"></div>
                                                        </div>
                                                        <span class="fw-semibold">{{ number_format($termCollection, 0) }}%</span>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <h5 class="fw-bold mb-3">Top Contributors</h5>
                        @if($topStudents->isEmpty())
                            <p class="text-muted mb-0">No payments recorded for the selected period.</p>
                        @else
                            <ul class="list-group list-group-flush">
                                @foreach($topStudents as $index => $student)
                                    <li class="list-group-item px-0">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <span class="badge bg-primary-soft text-primary me-2">{{ $index + 1 }}</span>
                                                <strong>{{ $student->student_name ?? 'Unknown' }}</strong>
                                            </div>
                                            <span>Ksh{{ number_format($student->total_paid, 2) }}</span>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-lg-8">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <h5 class="fw-bold mb-3">Income vs Expenses ({{ $selectedYear ?? 'All Years' }})</h5>
                        <div id="incomeExpensesChart" style="min-height:320px;">
                            @if($monthlyPayments->isEmpty() && $monthlySalaries->isEmpty() && $monthlyExpenses->isEmpty())
                                <p class="text-muted mb-0">No financial data available.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <h5 class="fw-bold mb-3">Expense Categories</h5>
                        <div id="expenseCategoriesChart" style="min-height:320px;">
                            @if($expenseCategories->isEmpty())
                                <p class="text-muted mb-0">No expense data available.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-6">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <h5 class="fw-bold mb-3">Monthly Receipts ({{ $selectedYear ?? 'All Years' }})</h5>
                        <div id="financeMonthlyChart" style="min-height:320px;">
                            @if($monthlyPayments->isEmpty())
                                <p class="text-muted mb-0">No payment data available.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <h5 class="fw-bold mb-3">Payment Methods Split</h5>
                        <div id="financeMethodsChart" style="min-height:320px;">
                            @if($paymentMethods->isEmpty())
                                <p class="text-muted mb-0">No payment data available.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

@section('script')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    // Download button for balance filter
    $('#downloadBalanceBtn').on('click', function(e) {
        e.preventDefault();
        
        // Get current filter values from form
        var balanceOperator = $('select[name="balance_operator"]').val() || '';
        var balanceAmount = $('input[name="balance_amount"]').val() || '';
        
        // Build query string
        var params = {};
        if (balanceOperator) {
            params.balance_operator = balanceOperator;
        }
        if (balanceAmount) {
            params.balance_amount = balanceAmount;
        }
        
        // Build URL with query parameters
        var url = '{{ route("account/finance/export-balance") }}';
        if (Object.keys(params).length > 0) {
            url += '?' + $.param(params);
        }
        
        // Navigate to export URL
        window.location.href = url;
    });
});
</script>
<script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Income vs Expenses chart
    @php
        // Combine all months from payments, salaries, and expenses
        $allMonthKeys = collect();
        foreach ($monthlyPayments as $p) {
            $allMonthKeys->push($p->month_key);
        }
        foreach ($monthlySalaries as $s) {
            if (!$allMonthKeys->contains($s->month_key)) {
                $allMonthKeys->push($s->month_key);
            }
        }
        foreach ($monthlyExpenses as $e) {
            if (!$allMonthKeys->contains($e->month_key)) {
                $allMonthKeys->push($e->month_key);
            }
        }
        $allMonthKeys = $allMonthKeys->unique()->sort()->values();
        
        $allMonths = collect();
        $incomeData = [];
        $salaryData = [];
        $expenseData = [];
        
        foreach ($allMonthKeys as $monthKey) {
            $payment = $monthlyPayments->firstWhere('month_key', $monthKey);
            $salary = $monthlySalaries->firstWhere('month_key', $monthKey);
            $expense = $monthlyExpenses->firstWhere('month_key', $monthKey);
            
            $label = $payment ? $payment->month_label : ($salary ? $salary->month_label : ($expense ? $expense->month_label : ''));
            $allMonths->push($label);
            
            $incomeData[] = $payment ? (float)$payment->total : 0;
            $salaryData[] = $salary ? (float)$salary->total : 0;
            $expenseData[] = $expense ? (float)$expense->total : 0;
        }
    @endphp
    
    @if($allMonths->isNotEmpty())
    var incomeExpensesChart = new ApexCharts(document.querySelector("#incomeExpensesChart"), {
        chart: {
            type: 'bar',
            height: 320,
            toolbar: { show: false },
            stacked: false
        },
        series: [
            {
                name: 'Income',
                data: @json($incomeData)
            },
            {
                name: 'Salaries',
                data: @json($salaryData)
            },
            {
                name: 'Expenses',
                data: @json($expenseData)
            }
        ],
        xaxis: {
            categories: @json($allMonths)
        },
        colors: ['#10b981', '#ef4444', '#fbbf24'],
        dataLabels: {
            enabled: false
        },
        tooltip: {
            y: {
                formatter: function (val) {
                    return 'Ksh' + val.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                }
            }
        },
        legend: {
            position: 'top'
        }
    });
    incomeExpensesChart.render();
    @endif

    // Expense categories chart
    @if($expenseCategories->isNotEmpty())
    var expenseCategoriesChart = new ApexCharts(document.querySelector("#expenseCategoriesChart"), {
        chart: {
            type: 'donut',
            height: 320
        },
        labels: @json($expenseCategories->pluck('category')),
        series: @json($expenseCategories->pluck('total')),
        colors: ['#ef4444', '#f97316', '#fbbf24', '#84cc16', '#10b981', '#3b82f6'],
        legend: {
            position: 'bottom'
        },
        dataLabels: {
            enabled: true,
            formatter: function (val, opts) {
                return opts.w.config.series[opts.seriesIndex].toLocaleString('en-US', { minimumFractionDigits: 0 });
            }
        },
        tooltip: {
            y: {
                formatter: function (val) {
                    return 'Ksh' + val.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                }
            }
        }
    });
    expenseCategoriesChart.render();
    @endif

    // Monthly receipts line chart
    @if($monthlyPayments->isNotEmpty())
    var monthlyChart = new ApexCharts(document.querySelector("#financeMonthlyChart"), {
        chart: {
            type: 'line',
            height: 320,
            toolbar: { show: false }
        },
        series: [{
            name: 'Amount Received',
            data: @json($monthlyPayments->pluck('total'))
        }],
        xaxis: {
            categories: @json($monthlyPayments->pluck('month_label'))
        },
        stroke: {
            curve: 'smooth',
            width: 3
        },
        colors: ['#0ea5e9'],
        dataLabels: {
            enabled: true,
            formatter: function (val) {
                return 'Ksh' + (val/1000).toFixed(1) + 'K';
            }
        },
        tooltip: {
            y: {
                formatter: function (val) {
                    return 'Ksh' + val.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                }
            }
        }
    });
    monthlyChart.render();
    @endif

    // Payment method donut chart
    @if($paymentMethods->isNotEmpty())
    var methodsChart = new ApexCharts(document.querySelector("#financeMethodsChart"), {
        chart: {
            type: 'donut',
            height: 320
        },
        labels: @json($paymentMethods->pluck('fees_type')),
        series: @json($paymentMethods->pluck('total')),
        colors: ['#3b82f6', '#10b981', '#fbbf24', '#ef4444', '#8b5cf6', '#ec4899'],
        legend: {
            position: 'bottom'
        },
        dataLabels: {
            enabled: true,
            formatter: function (val, opts) {
                return opts.w.config.series[opts.seriesIndex].toLocaleString('en-US', { minimumFractionDigits: 0 });
            }
        },
        tooltip: {
            y: {
                formatter: function (val) {
                    return 'Ksh' + val.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                }
            }
        }
    });
    methodsChart.render();
    @endif
});
</script>
@endsection
@endsection

