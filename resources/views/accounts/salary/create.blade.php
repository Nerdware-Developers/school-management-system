@extends('layouts.master')

@section('content')
<div class="page-wrapper">
    <div class="content container-fluid">
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">Record Salary Payment</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('account/salary') }}">Salary</a></li>
                        <li class="breadcrumb-item active">Record Salary</li>
                    </ul>
                </div>
                <div class="col-auto">
                    <a href="{{ route('account/salary') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back to Salary
                    </a>
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body">
                <form method="POST" action="{{ route('account/salary/store') }}">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Staff Name <span class="text-danger">*</span></label>
                            <div class="position-relative">
                                <input type="text" 
                                       id="staff_name" 
                                       name="staff_name" 
                                       class="form-control @error('staff_name') is-invalid @enderror"
                                       value="{{ old('staff_name') }}" 
                                       placeholder="Type to search staff name..."
                                       autocomplete="off">
                                <div id="staff_suggestions" class="list-group position-absolute w-100" style="z-index: 1000; display: none; max-height: 300px; overflow-y: auto; border: 1px solid #ddd; border-radius: 4px; background: white; box-shadow: 0 2px 8px rgba(0,0,0,0.1);"></div>
                            </div>
                            @error('staff_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Role/Position</label>
                            <input type="text" name="role" class="form-control @error('role') is-invalid @enderror"
                                   value="{{ old('role') }}" placeholder="e.g., Teacher">
                            @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Month</label>
                            <input type="month" name="month_reference" class="form-control @error('month_reference') is-invalid @enderror"
                                   value="{{ old('month_reference') }}">
                            @error('month_reference')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Payment Date <span class="text-danger">*</span></label>
                            <input type="date" name="payment_date" class="form-control @error('payment_date') is-invalid @enderror"
                                   value="{{ old('payment_date', now()->toDateString()) }}">
                            @error('payment_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Amount (Ksh) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="amount" class="form-control @error('amount') is-invalid @enderror"
                                   value="{{ old('amount') }}" placeholder="0.00">
                            @error('amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Payment Method</label>
                            <select name="payment_method" class="form-select @error('payment_method') is-invalid @enderror">
                                <option value="" selected disabled>Select method</option>
                                @foreach (['Bank Transfer','Cash','M-pesa','Cheque','Other'] as $method)
                                    <option value="{{ $method }}" {{ old('payment_method') == $method ? 'selected' : '' }}>{{ $method }}</option>
                                @endforeach
                            </select>
                            @error('payment_method')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Reference</label>
                            <input type="text" name="reference" class="form-control @error('reference') is-invalid @enderror"
                                   value="{{ old('reference') }}" placeholder="Transaction/Reference number">
                            @error('reference')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label">Notes</label>
                            <textarea name="notes" rows="3" class="form-control @error('notes') is-invalid @enderror"
                                      placeholder="Optional notes">{{ old('notes') }}</textarea>
                            @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Record Salary
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@section('script')
<style>
    #staff_suggestions .list-group-item {
        cursor: pointer;
        padding: 10px 15px;
        border: none;
        border-bottom: 1px solid #eee;
    }
    #staff_suggestions .list-group-item:hover,
    #staff_suggestions .list-group-item.active {
        background-color: #f8f9fa;
        color: #0d6efd;
    }
    #staff_suggestions .list-group-item:last-child {
        border-bottom: none;
    }
    .staff-type {
        font-size: 0.85em;
        color: #6c757d;
        font-weight: normal;
    }
</style>
<script>
    $(document).ready(function() {
        let searchTimeout;
        const $input = $('#staff_name');
        const $suggestions = $('#staff_suggestions');

        // Hide suggestions when clicking outside
        $(document).on('click', function(e) {
            if (!$(e.target).closest('#staff_name, #staff_suggestions').length) {
                $suggestions.hide();
            }
        });

        // Handle input
        $input.on('input', function() {
            const query = $(this).val().trim();
            
            clearTimeout(searchTimeout);
            
            if (query.length < 2) {
                $suggestions.hide().empty();
                return;
            }

            // Debounce search
            searchTimeout = setTimeout(function() {
                searchStaff(query);
            }, 300);
        });

        // Handle keyboard navigation
        let selectedIndex = -1;
        $input.on('keydown', function(e) {
            const $items = $suggestions.find('.list-group-item');
            
            if (e.key === 'ArrowDown') {
                e.preventDefault();
                selectedIndex = Math.min(selectedIndex + 1, $items.length - 1);
                updateSelection($items, selectedIndex);
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                selectedIndex = Math.max(selectedIndex - 1, -1);
                updateSelection($items, selectedIndex);
            } else if (e.key === 'Enter' && selectedIndex >= 0) {
                e.preventDefault();
                $items.eq(selectedIndex).click();
            } else if (e.key === 'Escape') {
                $suggestions.hide();
                selectedIndex = -1;
            }
        });

        function updateSelection($items, index) {
            $items.removeClass('active');
            if (index >= 0 && index < $items.length) {
                $items.eq(index).addClass('active');
                // Scroll into view
                const $selected = $items.eq(index);
                const container = $suggestions[0];
                const itemTop = $selected.position().top + container.scrollTop;
                const itemBottom = itemTop + $selected.outerHeight();
                const containerTop = container.scrollTop;
                const containerBottom = containerTop + container.clientHeight;
                
                if (itemTop < containerTop) {
                    container.scrollTop = itemTop;
                } else if (itemBottom > containerBottom) {
                    container.scrollTop = itemBottom - container.clientHeight;
                }
            }
        }

        function searchStaff(query) {
            $.ajax({
                url: '{{ route("account/salary/search-staff") }}',
                method: 'GET',
                data: { q: query },
                success: function(data) {
                    if (data.length === 0) {
                        $suggestions.html('<div class="list-group-item text-muted">No staff found</div>').show();
                        return;
                    }

                    let html = '';
                    data.forEach(function(staff) {
                        html += `<div class="list-group-item" data-name="${staff.name}">
                                    <strong>${staff.name}</strong>
                                    <span class="staff-type"> - ${staff.type}</span>
                                 </div>`;
                    });
                    
                    $suggestions.html(html).show();
                    selectedIndex = -1;

                    // Handle click on suggestion
                    $suggestions.find('.list-group-item').on('click', function() {
                        const name = $(this).data('name');
                        $input.val(name);
                        $suggestions.hide();
                        selectedIndex = -1;
                    });
                },
                error: function() {
                    $suggestions.html('<div class="list-group-item text-danger">Error loading suggestions</div>').show();
                }
            });
        }
    });
</script>
@endsection

