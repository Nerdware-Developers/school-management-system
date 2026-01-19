<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
use App\Models\InvoiceDetails;
use App\Models\InvoiceDiscount;
use App\Models\InvoiceTotalAmount;
use App\Models\InvoiceCustomerName;
use App\Models\InvoicePaymentDetails;
use App\Models\InvoiceAdditionalCharges;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Storage;

class InvoiceController extends Controller
{
    /** index page */
    public function invoiceList()
    {
        // Get all invoices from invoice_customer_names (the main table) and join related data
        $allInvoices = InvoiceCustomerName::leftJoin('invoice_total_amounts as ita', 'invoice_customer_names.invoice_id', 'ita.invoice_id')
            ->select('invoice_customer_names.invoice_id', 
                     'invoice_customer_names.customer_name', 
                     'ita.total_amount', 
                     'invoice_customer_names.due_date', 
                     'invoice_customer_names.created_at', 
                     'invoice_customer_names.status')
            ->orderBy('invoice_customer_names.created_at', 'desc')
            ->get();
        
        // Get categories for each invoice (first category found)
        $invoiceList = $allInvoices->map(function($invoice) {
            $firstDetail = InvoiceDetails::where('invoice_id', $invoice->invoice_id)->first();
            $invoice->category = $firstDetail ? $firstDetail->category : 'N/A';
            return $invoice;
        });

        // Calculate real statistics
        $allInvoices = InvoiceCustomerName::join('invoice_total_amounts as ita', 'invoice_customer_names.invoice_id', 'ita.invoice_id')
            ->select('invoice_customer_names.invoice_id', 'invoice_customer_names.status', 'invoice_customer_names.due_date', 'ita.total_amount')
            ->get();

        $now = now();

        $stats = [
            'all' => [
                'count' => $allInvoices->count(),
                'total' => $allInvoices->sum('total_amount')
            ],
            'paid' => [
                'count' => $allInvoices->filter(function($invoice) use ($now) {
                    return $invoice->status === 'paid';
                })->count(),
                'total' => $allInvoices->filter(function($invoice) use ($now) {
                    return $invoice->status === 'paid';
                })->sum('total_amount')
            ],
            'unpaid' => [
                'count' => $allInvoices->filter(function($invoice) use ($now) {
                    $status = strtolower($invoice->status ?? '');
                    // Unpaid if status is not paid/cancelled/draft, or if due date has passed and not paid
                    if (in_array($status, ['paid', 'cancelled', 'draft'])) {
                        return false;
                    }
                    // If due date exists and has passed, consider it unpaid/overdue
                    if ($invoice->due_date) {
                        try {
                            $dueDate = Carbon::parse($invoice->due_date);
                            return $dueDate->isPast();
                        } catch (\Exception $e) {
                            return true; // If date parsing fails, consider unpaid
                        }
                    }
                    // If no status and no due date, consider unpaid
                    return empty($status);
                })->count(),
                'total' => $allInvoices->filter(function($invoice) use ($now) {
                    $status = strtolower($invoice->status ?? '');
                    if (in_array($status, ['paid', 'cancelled', 'draft'])) {
                        return false;
                    }
                    if ($invoice->due_date) {
                        try {
                            $dueDate = \Carbon\Carbon::parse($invoice->due_date);
                            return $dueDate->isPast();
                        } catch (\Exception $e) {
                            return true;
                        }
                    }
                    return empty($status);
                })->sum('total_amount')
            ],
            'cancelled' => [
                'count' => $allInvoices->filter(function($invoice) {
                    return strtolower($invoice->status ?? '') === 'cancelled';
                })->count(),
                'total' => $allInvoices->filter(function($invoice) {
                    return strtolower($invoice->status ?? '') === 'cancelled';
                })->sum('total_amount')
            ]
        ];

        return view('invoices.list_invoices',compact('invoiceList', 'stats'));
    }

    /** invoice paid page */
    public function invoicePaid()
    {
        return view('invoices.tab.paid_invoices');
    }

    /** incoice overdue page*/
    public function invoiceOverdue()
    {
        return view('invoices.tab.overdue_invoices');
    }

    /** invoice draft */
    public function invoiceDraft()
    {
        return view('invoices.tab.draft_invoices');
    }

    /** recurring invoices.blade */
    public function invoiceRecurring()
    {
        return view('invoices.tab.recurring_invoices');
    }

    /** invoice cancelled */
    public function invoiceCancelled()
    {
        return view('invoices.tab.cancelled_invoices');
    }

    /** invoice grid */
    public function invoiceGrid()
    {
        // Get all invoices from invoice_customer_names (the main table) and join related data
        $invoiceList = InvoiceCustomerName::leftJoin('invoice_total_amounts as ita', 'invoice_customer_names.invoice_id', 'ita.invoice_id')
            ->select('invoice_customer_names.invoice_id',
                     'invoice_customer_names.customer_name', 
                     'ita.total_amount', 
                     'invoice_customer_names.due_date')
            ->orderBy('invoice_customer_names.created_at', 'desc')
            ->get();
        return view('invoices.grid_invoice',compact('invoiceList'));
    }
    
    /** invoice add page */
    public function invoiceAdd()
    {
        return view('invoices.invoice_add');
    }

    /** save record incoice */
    public function saveRecord(Request $request)
    {
        // Log incoming request for debugging
        \Log::info('Invoice save request received', [
            'customer_name' => $request->customer_name,
            'po_number' => $request->po_number,
            'items_count' => count($request->items ?? []),
            'items' => $request->items,
            'categories' => $request->category,
        ]);
        
        $request->validate([
            'customer_name' => 'required|string',
            'po_number'  => 'required|string',
            'due_date'   => 'required|string',
            'items'      => 'required|array',
            'items.*'    => 'nullable|string',
            'category.*' => 'nullable|string',
            'quantity.*' => 'nullable|string',
            'price.*'    => 'nullable|string',
            'amount.*'   => 'nullable|string',
            'discount.*' => 'nullable|string',
            'name_of_the_signatuaory' => 'nullable|string',
            'total_amount' => 'nullable|numeric',
        ], [
            'customer_name.required' => 'Customer name is required.',
            'po_number.required' => 'PO Number is required.',
            'due_date.required' => 'Due date is required.',
            'items.required' => 'At least one item is required.',
        ]);
        
        DB::beginTransaction();
        try {

            $customerName                    = new InvoiceCustomerName;
            $customerName->customer_name     = $request->customer_name;
            $customerName->po_number         = $request->po_number;
            $customerName->date              = $request->date ?? date('d-m-Y');
            $customerName->due_date          = $request->due_date;
            $customerName->enable_tax        = $request->enable_tax;
            $customerName->recurring_incoice = $request->recurring_incoice;
            $customerName->by_month          = $request->by_month;
            $customerName->month             = $request->month;
            $customerName->invoice_from      = $request->invoice_from;
            $customerName->invoice_to        = $request->invoice_to;
            $customerName->status            = 'unpaid'; // Default status
            $customerName->save();
            
            // Refresh to ensure invoice_id is available (it's auto-generated in boot method)
            $customerName->refresh();

            // Get invoice_id from the saved model
            $invoiceId = $customerName->invoice_id;
            
            // Ensure invoice_id was generated
            if (empty($invoiceId)) {
                DB::rollback();
                \Log::error('Invoice ID was not generated after save', [
                    'customer_name' => $request->customer_name,
                    'model_id' => $customerName->id,
                    'invoice_id' => $invoiceId
                ]);
                Toastr::error('Failed to generate invoice ID. Please try again.', 'Error');
                return redirect()->back()->withInput();
            }

            // Filter out empty items before saving
            $items = $request->items ?? [];
            $categories = $request->category ?? [];
            $quantities = $request->quantity ?? [];
            $prices = $request->price ?? [];
            $amounts = $request->amount ?? [];
            $discounts = $request->discount ?? [];

            // Ensure at least one valid item (item name is required, category is optional for now)
            $hasValidItem = false;
            $validItemsCount = 0;
            foreach ($items as $key => $item) {
                $itemTrimmed = trim($item ?? '');
                if (!empty($itemTrimmed)) {
                    $hasValidItem = true;
                    $validItemsCount++;
                }
            }

            if (!$hasValidItem) {
                DB::rollback();
                \Log::warning('Invoice save failed: No valid items', [
                    'items' => $items,
                    'categories' => $categories,
                    'invoice_id' => $invoiceId
                ]);
                Toastr::error('Please add at least one item with a name.', 'Validation Error');
                return redirect()->back()->withInput();
            }
            
            \Log::info('Valid items found', [
                'count' => $validItemsCount,
                'invoice_id' => $invoiceId
            ]);

            foreach ($items as $key => $item) {
                // Skip empty rows (only item name is required, category is optional)
                $itemTrimmed = trim($item ?? '');
                if (empty($itemTrimmed)) {
                    continue;
                }

                $InvoiceDetails             = new InvoiceDetails;
                $InvoiceDetails->invoice_id = $invoiceId;
                $InvoiceDetails->items      = $itemTrimmed;
                $InvoiceDetails->category   = trim($categories[$key] ?? '');
                $InvoiceDetails->quantity   = trim($quantities[$key] ?? '0');
                $InvoiceDetails->price      = trim($prices[$key] ?? '0');
                $InvoiceDetails->amount     = trim($amounts[$key] ?? '0');
                $InvoiceDetails->discount   = trim($discounts[$key] ?? '0');
                
                try {
                    $InvoiceDetails->save();
                    \Log::info('Invoice detail saved', [
                        'invoice_id' => $invoiceId,
                        'item' => $itemTrimmed,
                        'detail_id' => $InvoiceDetails->id
                    ]);
                } catch (\Exception $e) {
                    DB::rollback();
                    \Log::error('Failed to save invoice detail', [
                        'invoice_id' => $invoiceId,
                        'item' => $itemTrimmed,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                    Toastr::error('Failed to save invoice item: ' . $e->getMessage(), 'Error');
                    return redirect()->back()->withInput();
                }
            }

            if ($request->hasFile('upload_sign')) {
                $file        = $request->file('upload_sign');
                $upload_sign = $file->store('public/upload_sign','local'); // 'local' disk corresponds to the storage/app directory    
            } else {
                $upload_sign = 'NULL';
            }

            // Calculate total amount if not provided
            $calculatedTotal = 0;
            foreach ($items as $key => $item) {
                $itemTrimmed = trim($item ?? '');
                if (!empty($itemTrimmed)) {
                    $calculatedTotal += floatval($amounts[$key] ?? 0);
                }
            }

            /** InvoiceTotalAmount */
            try {
                $InvoiceTotalAmount                          = new InvoiceTotalAmount;
                $InvoiceTotalAmount->invoice_id              = $invoiceId;
                $InvoiceTotalAmount->taxable_amount          = $request->taxable_amount ?? $calculatedTotal;
                $InvoiceTotalAmount->round_off               = $request->round_off ?? 0;
                $InvoiceTotalAmount->total_amount            = $request->total_amount ?? $calculatedTotal;
                $InvoiceTotalAmount->upload_sign             = $upload_sign;
                $InvoiceTotalAmount->name_of_the_signatuaory = $request->name_of_the_signatuaory ?? '';
                $InvoiceTotalAmount->save();
                \Log::info('InvoiceTotalAmount saved', ['invoice_id' => $invoiceId]);
            } catch (\Exception $e) {
                DB::rollback();
                \Log::error('Failed to save InvoiceTotalAmount', [
                    'invoice_id' => $invoiceId,
                    'error' => $e->getMessage()
                ]);
                Toastr::error('Failed to save invoice total: ' . $e->getMessage(), 'Error');
                return redirect()->back()->withInput();
            }

            /** InvoiceAdditionalCharges */
            if(!empty($request->service_charge)) {
                foreach ($request->service_charge as $key => $values) {
                    $InvoiceAdditionalCharges                 = new InvoiceAdditionalCharges;
                    $InvoiceAdditionalCharges->invoice_id     = $invoiceId;
                    $InvoiceAdditionalCharges->service_charge = $request->service_charge[$key];
                    $InvoiceAdditionalCharges->save();
                }
            }
            /** InvoiceDiscount */
            if (!empty($request->offer_new)) {
                foreach ($request->offer_new as $key => $values) {
                    $InvoiceDiscount             = new InvoiceDiscount;
                    $InvoiceDiscount->invoice_id = $invoiceId;
                    $InvoiceDiscount->offer_new  = $request->offer_new[$key];
                    $InvoiceDiscount->save();
                }
            }

            /** InvoicePaymentDetails */
            try {
                $InvoicePaymentDetails                            = new InvoicePaymentDetails;
                $InvoicePaymentDetails->invoice_id                = $invoiceId;
                $InvoicePaymentDetails->account_holder_name       = $request->account_holder_name ?? null;
                $InvoicePaymentDetails->bank_name                 = $request->bank_name ?? null;
                $InvoicePaymentDetails->ifsc_code                 = $request->ifsc_code ?? null;
                $InvoicePaymentDetails->account_number            = $request->account_number ?? null;
                $InvoicePaymentDetails->add_terms_and_Conditions  = $request->add_terms_and_Conditions ?? null;
                $InvoicePaymentDetails->add_notes                 = $request->add_notes ?? null;
                $InvoicePaymentDetails->save();
                \Log::info('InvoicePaymentDetails saved', ['invoice_id' => $invoiceId]);
            } catch (\Exception $e) {
                DB::rollback();
                \Log::error('Failed to save InvoicePaymentDetails', [
                    'invoice_id' => $invoiceId,
                    'error' => $e->getMessage()
                ]);
                Toastr::error('Failed to save payment details: ' . $e->getMessage(), 'Error');
                return redirect()->back()->withInput();
            }

            DB::commit();
            
            // Log success for debugging
            \Log::info('Invoice created successfully', [
                'invoice_id' => $invoiceId,
                'customer_name' => $request->customer_name,
                'items_count' => count(array_filter($items, function($item) { return !empty($item); }))
            ]);
            
            Toastr::success('Invoice has been added successfully!','Success');
            return redirect()->route('invoice/list/page');
        } catch(\Exception $e) {
            DB::rollback();
            \Log::error('Invoice save failed: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            \Log::error('Request data: ', $request->all());
            Toastr::error('Failed to add invoice: ' . $e->getMessage(),'Error');
            return redirect()->back()->withInput();
        }
    }

    /** invoice edit */
    public function invoiceEdit($invoice_id)
    {
        $invoiceView = InvoiceDetails::join('invoice_customer_names as icn', 'invoice_details.invoice_id', 'icn.invoice_id')
            ->join('invoice_total_amounts as ita', 'invoice_details.invoice_id', 'ita.invoice_id')
            ->join('invoice_payment_details as ipd', 'invoice_details.invoice_id', 'ipd.invoice_id')
            ->select('invoice_details.*','icn.customer_name','icn.po_number',
            'icn.date','icn.due_date','icn.enable_tax','icn.recurring_incoice','icn.by_month'
            ,'icn.month','icn.invoice_from','icn.invoice_to','ita.*','ita.name_of_the_signatuaory','ipd.*')
            ->distinct('invoice_details.invoice_id')
            ->where('icn.invoice_id',$invoice_id)
            ->first();

        $invoiceDetails    = InvoiceDetails::where('invoice_id',$invoice_id)->get();
        $AdditionalCharges = InvoiceAdditionalCharges::where('invoice_id',$invoice_id)->get();
        $InvoiceDiscount   = InvoiceDiscount::where('invoice_id',$invoice_id)->get();

        return view('invoices.invoice_edit',compact('invoiceView','invoiceDetails','AdditionalCharges','InvoiceDiscount'));
    }

    /** update record */
    public function updateRecord(Request $request)
    {
        try {

            $customerName                    = InvoiceCustomerName::where('invoice_id',$request->invoice_id)->firstOrFail();
            $customerName->customer_name     = $request->customer_name;
            $customerName->po_number         = $request->po_number;
            $customerName->date              = $request->date;
            $customerName->due_date          = $request->due_date;
            $customerName->enable_tax        = $request->enable_tax;
            $customerName->recurring_incoice = $request->recurring_incoice;
            $customerName->by_month          = $request->by_month;
            $customerName->month             = $request->month;
            $customerName->invoice_from      = $request->invoice_from;
            $customerName->invoice_to        = $request->invoice_to;
            $customerName->save();

            foreach ($request->items as $key => $values) {
                $InvoiceDetails             = InvoiceDetails::where('invoice_id',$request->invoice_id)->firstOrFail();
                $InvoiceDetails->items      = $request->items[$key];
                $InvoiceDetails->category   = $request->category[$key];
                $InvoiceDetails->quantity   = $request->quantity[$key];
                $InvoiceDetails->price      = $request->price[$key];
                $InvoiceDetails->amount     = $request->amount[$key];
                $InvoiceDetails->discount   = $request->discount[$key];
                $InvoiceDetails->save();
            }

            /** InvoiceAdditionalCharges */
            if(!empty($request->service_charge)) {
                foreach ($request->service_charge as $key => $values) {
                    $InvoiceAdditionalCharges                 = InvoiceAdditionalCharges::where('invoice_id',$request->invoice_id)->firstOrFail();
                    $InvoiceAdditionalCharges->service_charge = $request->service_charge[$key];
                    $InvoiceAdditionalCharges->save();
                }
            }
            /** InvoiceDiscount */
            if (!empty($request->offer_new)) {
                foreach ($request->offer_new as $key => $values) {
                    $InvoiceDiscount             = InvoiceDiscount::where('invoice_id',$request->invoice_id)->firstOrFail();
                    $InvoiceDiscount->offer_new  = $request->offer_new[$key];
                    $InvoiceDiscount->save();
                }
            }

            $InvoicePaymentDetails                            = InvoicePaymentDetails::where('invoice_id',$request->invoice_id)->firstOrFail();
            $InvoicePaymentDetails->account_holder_name       = $request->account_holder_name;
            $InvoicePaymentDetails->bank_name                 = $request->bank_name;
            $InvoicePaymentDetails->ifsc_code                 = $request->ifsc_code;
            $InvoicePaymentDetails->account_number            = $request->account_number;
            $InvoicePaymentDetails->add_terms_and_Conditions  = $request->add_terms_and_Conditions;
            $InvoicePaymentDetails->add_notes                 = $request->add_notes;
            $InvoicePaymentDetails->save();

            if(!empty($request->upload_sign)) {
                $file = $request->upload_sign_unlink;
                if (Storage::exists($file)) {
                    unlink(Storage::path($file));
                }
            } 
            if ($request->hasFile('upload_sign')) {
                $file        = $request->file('upload_sign');
                $upload_sign = $file->store('public/upload_sign','local'); // 'local' disk corresponds to the storage/app directory    
            } else {
                $upload_sign = $request->upload_sign_unlink;
            }
            
            /** InvoiceTotalAmount */
            $InvoiceTotalAmount                          = InvoiceTotalAmount::where('invoice_id',$request->invoice_id)->firstOrFail();
            $InvoiceTotalAmount->taxable_amount          = $request->taxable_amount;
            $InvoiceTotalAmount->round_off               = $request->round_off;
            $InvoiceTotalAmount->total_amount            = $request->total_amount;
            $InvoiceTotalAmount->upload_sign             = $upload_sign;
            $InvoiceTotalAmount->name_of_the_signatuaory = $request->name_of_the_signatuaory;
            $InvoiceTotalAmount->save();

            Toastr::success('Has been updated successfully :)','Success');
            return redirect()->back();
        } catch(\Exception $e) {
            \Log::info($e);
            Toastr::error('fail, update record  :)','Error');
            return redirect()->back();
        }
    }

    /** delete record */
    public function deleteRecord(Request $request)
    {
        DB::beginTransaction();
        try {
            InvoiceCustomerName::where('invoice_id',$request->invoice_id)->delete();
            InvoiceDetails::where('invoice_id',$request->invoice_id)->delete();
            InvoiceTotalAmount::where('invoice_id',$request->invoice_id)->delete();
            InvoiceAdditionalCharges::where('invoice_id',$request->invoice_id)->delete();
            InvoiceDiscount::where('invoice_id',$request->invoice_id)->delete();
            InvoicePaymentDetails::where('invoice_id',$request->invoice_id)->delete();

            $file = $request->upload_sign;
            if (Storage::exists($file)) {
                unlink(Storage::path($file));
            }

            DB::commit();
            Toastr::success('Record deleted successfully :)','Success');
            return redirect()->route('invoice/list/page');
        } catch(\Exception $e) {
            DB::rollback();
            Toastr::error('Record deleted fail :)','Error');
            return redirect()->back();
        }

    }

    /** invoice view */
    public function invoiceView($invoice_id)
    {
        $invoiceView = InvoiceDetails::join('invoice_customer_names as icn', 'invoice_details.invoice_id', 'icn.invoice_id')
            ->join('invoice_total_amounts as ita', 'invoice_details.invoice_id', 'ita.invoice_id')
            ->join('invoice_payment_details as ide', 'invoice_details.invoice_id', 'ide.invoice_id')
            ->select('invoice_details.*','icn.customer_name','ita.total_amount',
            'icn.due_date','icn.po_number','icn.enable_tax','ita.round_off','icn.recurring_incoice',
            'icn.by_month','icn.month','icn.invoice_from','icn.invoice_to'
            ,'ide.bank_name','ide.account_number','ita.total_amount','ita.upload_sign','ita.name_of_the_signatuaory')
            ->distinct('invoice_details.invoice_id')
            ->where('icn.invoice_id',$invoice_id)
            ->first();
        $invoiceDetails = InvoiceDetails::where('invoice_id',$invoice_id)->get();

        return view('invoices.invoice_view',compact('invoiceView','invoiceDetails'));
    }

    /** invoice settings */
    public function invoiceSettings()
    {
        return view('invoices.settings.settings_invoices');
    }

    /** invoice settingst tax */
    public function invoiceSettingsTax()
    {
        return view('invoices.settings.settings_tax');
    }

    /** invoice settings bank */
    public function invoiceSettingsBank()
    {
        return view('invoices.settings.settings_bank');
    }
}
