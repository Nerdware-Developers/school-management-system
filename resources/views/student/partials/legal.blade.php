<div class="card p-4 shadow-sm rounded-3">
    <h4 class="mb-3">Student Information</h4>

    <div class="row">
        <div class="col-12 col-sm-4">
            <div class="form-group local-forms">
                <label>First Name <span class="login-danger">*</span></label>
                <input type="text" class="form-control @error('first_name') is-invalid @enderror" name="first_name" placeholder="Enter First Name" value="{{ old('first_name') }}">
                @error('first_name')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>

        <div class="col-12 col-sm-4">
            <div class="form-group local-forms">
                <label>Last Name <span class="login-danger">*</span></label>
                <input type="text" class="form-control @error('last_name') is-invalid @enderror" name="last_name" placeholder="Enter Last Name" value="{{ old('last_name') }}">
                @error('last_name')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>

        <div class="col-12 col-sm-4">
            <div class="form-group local-forms">
                <label>Gender <span class="login-danger">*</span></label>
                <select class="form-control select @error('gender') is-invalid @enderror" name="gender">
                    <option selected disabled>Select Gender</option>
                    <option value="Female" {{ old('gender') == 'Female' ? "selected" :"" }}>Female</option>
                    <option value="Male" {{ old('gender') == 'Male' ? "selected" :"" }}>Male</option>
                    <option value="Others" {{ old('gender') == 'Others' ? "selected" :"" }}>Others</option>
                </select>
                @error('gender')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>

        <div class="col-12 col-sm-4">
            <div class="form-group local-forms calendar-icon">
                <label>Date Of Birth <span class="login-danger">*</span></label>
                <input class="form-control datetimepicker @error('date_of_birth') is-invalid @enderror" name="date_of_birth" type="text" placeholder="DD-MM-YYYY" value="{{ old('date_of_birth') }}">
                @error('date_of_birth')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>

        <div class="col-12 col-sm-4">
            <div class="form-group local-forms">
                <label>Roll <span class="login-danger">*</span></label>
                <input class="form-control @error('roll') is-invalid @enderror" type="text" name="roll" placeholder="Enter Roll Number" value="{{ old('roll') }}">
                @error('roll')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>

        <div class="col-12 col-sm-4">
            <div class="form-group local-forms">
                <label>Grade <span class="login-danger">*</span></label>
                <small class="text-muted d-block mb-1" id="grade-status" style="display: none;"></small>
                <select class="form-control select @error('class') is-invalid @enderror" name="class" id="class">
                    <option value="" selected disabled>Please Select Grade</option>
                    <option value="PLAY GROUP" {{ old('class') == 'PLAY GROUP' ? 'selected' : '' }}>PLAY GROUP</option>
                    <option value="PP1" {{ old('class') == 'PP1' ? 'selected' : '' }}>PP1</option>
                    <option value="PP2" {{ old('class') == 'PP2' ? 'selected' : '' }}>PP2</option>
                    @for ($i = 1; $i <= 9; $i++)
                        <option value="GRADE {{ $i }}" {{ old('class') == "GRADE $i" ? 'selected' : '' }}>GRADE {{ $i }}</option>
                    @endfor
                </select>
                @error('class')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>

        <div class="col-12 col-sm-4">
            <div class="form-group local-forms">
                <label>Term <span class="login-danger">*</span></label>
                <select class="form-control select @error('term') is-invalid @enderror" name="term">
                    <option selected disabled>Please Select Term</option>
                    <option value="Term 1" {{ old('term') == 'Term 1' ? 'selected' : '' }}>Term 1</option>
                    <option value="Term 2" {{ old('term') == 'Term 2' ? 'selected' : '' }}>Term 2</option>
                    <option value="Term 3" {{ old('term') == 'Term 3' ? 'selected' : '' }}>Term 3</option>
                </select>
                @error('term')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>

        <div class="col-12 col-sm-4">
            <div class="form-group local-forms">
                <label>Name of Former School</label>
                <input type="text" class="form-control @error('former_school') is-invalid @enderror" name="former_school" placeholder="Enter Former School" value="{{ old('former_school') }}">
                @error('former_school')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>

        <div class="col-12 col-sm-4">
            <div class="form-group local-forms">
                <label>Residence</label>
                <input type="text" class="form-control @error('residence') is-invalid @enderror" name="residence" placeholder="Enter Residence" value="{{ old('residence') }}">
                @error('residence')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>

        <div class="col-12 col-sm-4">
            <div class="form-group local-forms">
                <label>Admission ID <span class="login-danger">*</span></label>
                <input type="text" class="form-control @error('admission_number') is-invalid @enderror"
                    name="admission_number" value="{{ old('admission_number', 'STD-' . rand(10000,99999)) }}" placeholder="Enter Admission Number">
                @error('admission_number')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>

        <div class="col-12 col-sm-4">
            <div class="form-group local-forms">
                <label>Address <span class="login-danger">*</span></label>
                <input type="text" name="address" id="address" class="form-control" required>
            </div>
        </div>
        <div class="col-12 col-sm-4">
            <div class="form-group students-up-files">
                <label>Upload Student Photo (150px X 150px)</label>
                <div class="image">
                    <label class="file-upload image-upbtn mb-0 @error('image') is-invalid @enderror">
                        Choose File <input type="file" name="image">
                    </label>
                    @error('image')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
                </div>
            </div>
        </div>
    </div>

    <hr class="my-4">

    <h4 class="mb-3">Parent / Guardian Information</h4>
    <div class="row">
        <div class="col-md-6 mb-3">
            <label>Father's Name <span class="login-danger">*</span></label>
            <input type="text" name="father_name" id="father_name" class="form-control @error('father_name') is-invalid @enderror" placeholder="Enter Father's Name" value="{{ old('father_name') }}" required>
            @error('father_name')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <div class="col-md-6 mb-3">
            <label for="father_telephone" class="form-label">Father's Telephone No. <span class="login-danger">*</span></label>
            <input type="text" name="father_telephone" id="father_telephone" class="form-control @error('father_telephone') is-invalid @enderror" placeholder="0726554037" value="{{ old('father_telephone') }}" required>
            <small class="text-muted">Format: 0726554037</small>
            @error('father_telephone')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <div class="col-md-6 mb-3">
            <label>Mother's Name <span class="login-danger">*</span></label>
            <input type="text" name="mother_name" id="mother_name" class="form-control @error('mother_name') is-invalid @enderror" placeholder="Enter Mother's Name" value="{{ old('mother_name') }}" required>
            @error('mother_name')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <div class="col-md-6 mb-3">
            <label for="mother_telephone" class="form-label">Mother's Telephone No. <span class="login-danger">*</span></label>
            <input type="text" name="mother_telephone" id="mother_telephone" class="form-control @error('mother_telephone') is-invalid @enderror" placeholder="0723341935" value="{{ old('mother_telephone') }}" required>
            <small class="text-muted">Format: 0723341935</small>
            @error('mother_telephone')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <div class="col-md-6 mb-3">
            <label for="occupation" class="form-label">Occupation</label>
            <input type="text" name="occupation" id="occupation" class="form-control @error('occupation') is-invalid @enderror" placeholder="Enter Occupation" value="{{ old('occupation') }}">
            @error('occupation')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <div class="col-md-6 mb-3">
            <label for="religion" class="form-label">Religion</label>
            <input type="text" name="religion" id="religion" class="form-control @error('religion') is-invalid @enderror" placeholder="Enter Religion" value="{{ old('religion') }}">
            @error('religion')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <div class="col-md-6 mb-3">
            <label>Parent/Guardian Name (Alternative)</label>
            <input type="text" name="parent_name" id="parent_name" class="form-control" placeholder="Enter Parent/Guardian Name" value="{{ old('parent_name') }}">
        </div>

        <div class="col-md-6 mb-3">
            <label for="parent_number" class="form-label">Alternative Phone Number</label>
            <input type="text" name="parent_number" id="parent_number" class="form-control" placeholder="0726554037" value="{{ old('parent_number') }}">
            <small class="text-muted">Format: 0726554037</small>
        </div>

        <div class="col-md-6 mb-3">
            <label for="parent_email" class="form-label">Email Address</label>
            <input type="email" name="parent_email" id="parent_email" class="form-control" placeholder="Enter email" value="{{ old('parent_email') }}">
        </div>

        <div class="col-md-6 mb-3">
            <label for="guardian_name" class="form-label">Guardian Name</label>
            <input type="text" name="guardian_name" id="guardian_name" class="form-control" placeholder="Enter Guardian Name" value="{{ old('guardian_name') }}">
        </div>

        <div class="col-md-6 mb-3">
            <label for="guardian_number" class="form-label">Guardian Phone Number</label>
            <input type="text" name="guardian_number" id="guardian_number" class="form-control" placeholder="0726554037" value="{{ old('guardian_number') }}">
            <small class="text-muted">Format: 0726554037</small>
        </div>

        <div class="col-md-6 mb-3">
            <label for="guardian_email" class="form-label">Guardian Email Address</label>
            <input type="email" name="guardian_email" id="guardian_email" class="form-control" placeholder="Enter email" value="{{ old('guardian_email') }}">
        </div>
        <div class="text-end mt-4">
            <button type="button" class="btn btn-primary" id="nextToActivities">
                Next Page →
            </button>
        </div>
    </div>
</div>

<script>
// Immediate test - this should show up right away
console.log('🚀 Legal form script loading...');
console.log('🚀 Current URL:', window.location.href);
console.log('🚀 Document ready state:', document.readyState);

// Test if script is running
window.testGradeScript = function() {
    console.log('✅ Script is accessible');
    const gradeSelect = document.getElementById('class');
    console.log('Grade select element:', gradeSelect);
    return gradeSelect;
};

document.addEventListener('DOMContentLoaded', function() {
    console.log('✅ DOM Content Loaded - Legal form script');
    
    // Next button to activities tab
    const nextButton = document.getElementById('nextToActivities');
    if (nextButton) {
        nextButton.addEventListener('click', function() {
            var nextTab = new bootstrap.Tab(document.querySelector('#activities-tab'));
            nextTab.show();
        });
    }

    // Function to get fee amount field (works even if in hidden tab)
    function getFeeAmountField() {
        // Try multiple selectors to find the field
        let field = document.querySelector('input[name="fee_amount"]');
        if (!field) {
            field = document.querySelector('#finance input[name="fee_amount"]');
        }
        if (!field) {
            field = document.querySelector('.tab-pane#finance input[name="fee_amount"]');
        }
        // Last resort: search all inputs
        if (!field) {
            const allInputs = document.querySelectorAll('input[name="fee_amount"]');
            field = allInputs.length > 0 ? allInputs[0] : null;
        }
        return field;
    }

    // Auto-populate fee amount when grade is selected
    // Wait a bit for Select2 or other libraries to initialize
    setTimeout(function() {
        const gradeSelect = document.getElementById('class');
        console.log('🔍 Looking for grade select element...');
        console.log('Grade select element found:', gradeSelect);
        console.log('Grade select type:', typeof gradeSelect);
        
        if (!gradeSelect) {
            console.error('❌ Grade select element NOT FOUND!');
            console.log('Available elements with id="class":', document.querySelectorAll('#class'));
            return;
        }
        
        console.log('✅ Grade select found, attaching change event listener');
        
        // Remove any existing listeners by cloning the element
        const newSelect = gradeSelect.cloneNode(true);
        gradeSelect.parentNode.replaceChild(newSelect, gradeSelect);
        
        // Now attach listener to the new element
        newSelect.addEventListener('change', function(e) {
            console.log('=== GRADE CHANGE EVENT TRIGGERED ===');
            console.log('Event:', e);
            console.log('Select element:', this);
            console.log('Selected index:', this.selectedIndex);
            console.log('Selected option:', this.options[this.selectedIndex]);
            
            const selectedOption = this.options[this.selectedIndex];
            const selectedGrade = this.value;
            
            console.log('Selected grade value:', selectedGrade);
            console.log('Option disabled?', selectedOption.disabled);
            console.log('Option value:', selectedOption.value);
            
            // Skip if placeholder/disabled option is selected
            if (!selectedGrade || 
                selectedGrade === '' || 
                selectedGrade === 'Please Select Grade' ||
                selectedOption.disabled ||
                selectedOption.value === '') {
                console.log('❌ Skipping - placeholder option selected');
                return;
            }
            
            console.log('✅ Valid grade selected:', selectedGrade);
            
            // Show status message
            const statusEl = document.getElementById('grade-status');
            if (statusEl) {
                statusEl.style.display = 'block';
                statusEl.textContent = 'Loading fee for ' + selectedGrade + '...';
                statusEl.className = 'text-muted d-block mb-1';
            }
            
            if (selectedGrade && selectedGrade !== '') {
                // Show loading indicator
                let feeAmountField = getFeeAmountField();
                if (feeAmountField) {
                    feeAmountField.value = 'Loading...';
                    feeAmountField.disabled = true;
                } else {
                    console.warn('⚠️ Fee amount field not found. It may be in the Finance tab.');
                    if (statusEl) {
                        statusEl.textContent = '⚠️ Fee field not found. Please check Finance tab.';
                        statusEl.className = 'text-warning d-block mb-1';
                    }
                }

                // Make AJAX request to get fee for grade
                const routeUrl = '{{ route("student.get-fee-by-grade") }}';
                const url = `${routeUrl}?grade=${encodeURIComponent(selectedGrade)}`;
                console.log('🔍 Fetching fee for grade:', selectedGrade);
                console.log('📡 URL:', url);
                console.log('📡 Route URL:', routeUrl);
                
                fetch(url, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                    credentials: 'same-origin'
                })
                .then(response => {
                    console.log('📥 Response received:', response);
                    console.log('📥 Response status:', response.status);
                    console.log('📥 Response ok:', response.ok);
                    
                    if (!response.ok) {
                        console.error('❌ Response not OK');
                        return response.json().then(err => {
                            console.error('❌ Error data:', err);
                            throw new Error(err.error || `HTTP error! status: ${response.status}`);
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('✅ Fee data received:', data);
                    console.log('✅ Success:', data.success);
                    console.log('✅ Fee amount:', data.fee_amount);
                    feeAmountField = getFeeAmountField();
                    
                    if (data.success && data.fee_amount > 0) {
                        // Update fee amount field in finance tab
                        if (feeAmountField) {
                            const feeValue = parseFloat(data.fee_amount).toFixed(2);
                            feeAmountField.value = feeValue;
                            feeAmountField.disabled = false;
                            
                            // Remove any error styling
                            feeAmountField.classList.remove('is-invalid');
                            
                            // Trigger input event to update total fee calculation
                            const inputEvent = new Event('input', { bubbles: true });
                            feeAmountField.dispatchEvent(inputEvent);
                            
                            // Also trigger change event
                            const changeEvent = new Event('change', { bubbles: true });
                            feeAmountField.dispatchEvent(changeEvent);
                            
                            console.log('✅ Fee amount updated to:', feeValue);
                            
                            // Update status message
                            if (statusEl) {
                                statusEl.textContent = '✓ Fee set to Ksh ' + feeValue;
                                statusEl.className = 'text-success d-block mb-1';
                            }
                            
                            // Show success message (optional - using Toastr if available)
                            if (typeof toastr !== 'undefined') {
                                toastr.success('Fee amount automatically set to Ksh ' + feeValue, 'Fee Updated');
                            }
                        } else {
                            console.error('❌ Fee amount field not found in DOM. Please check the Finance tab.');
                            if (statusEl) {
                                statusEl.textContent = '❌ Fee field not found. Please check Finance tab.';
                                statusEl.className = 'text-danger d-block mb-1';
                            }
                            alert('Fee amount field not found. Please navigate to the Finance tab to see the fee amount.');
                        }
                    } else {
                        // If no fee found, show helpful message
                        if (feeAmountField) {
                            feeAmountField.value = '';
                            feeAmountField.disabled = false;
                        }
                        
                        console.warn('⚠️ No fee found for grade:', selectedGrade, data.message || '');
                        
                        let errorMsg = 'No fee structure found for ' + selectedGrade;
                        if (data.available_grades && data.available_grades.length > 0) {
                            errorMsg += '. Available grades: ' + data.available_grades.join(', ');
                        }
                        
                        // Update status message
                        if (statusEl) {
                            statusEl.textContent = '❌ ' + errorMsg;
                            statusEl.className = 'text-danger d-block mb-1';
                        }
                        
                        if (typeof toastr !== 'undefined') {
                            toastr.warning(errorMsg, 'Fee Not Found', {timeOut: 10000});
                        } else {
                            alert(errorMsg);
                        }
                    }
                })
                .catch(error => {
                    console.error('❌ Error fetching fee:', error);
                    console.error('❌ Error stack:', error.stack);
                    feeAmountField = getFeeAmountField();
                    if (feeAmountField) {
                        feeAmountField.value = '';
                        feeAmountField.disabled = false;
                    }
                    
                    const errorMsg = 'Error loading fee: ' + error.message;
                    console.error('❌ Full error message:', errorMsg);
                    
                    if (typeof toastr !== 'undefined') {
                        toastr.error(errorMsg, 'Error', {timeOut: 10000});
                    } else {
                        alert(errorMsg);
                    }
                });
            } else {
                // Clear fee amount if no grade selected
                const feeAmountField = getFeeAmountField();
                if (feeAmountField) {
                    feeAmountField.value = '';
                    feeAmountField.disabled = false;
                }
            }
        });
        
        // Also trigger on page load if grade is already selected (and not placeholder)
        if (newSelect.value && 
            newSelect.value !== '' && 
            newSelect.value !== 'Please Select Grade' &&
            !newSelect.options[newSelect.selectedIndex].disabled) {
            console.log('🔄 Auto-triggering change event for pre-selected grade');
            newSelect.dispatchEvent(new Event('change'));
        }
        
        // Also try jQuery change event in case Select2 is being used
        if (typeof jQuery !== 'undefined') {
            console.log('📦 jQuery detected, also attaching jQuery change handler');
            jQuery(newSelect).on('change', function() {
                console.log('📦 jQuery change event triggered');
                newSelect.dispatchEvent(new Event('change'));
            });
        }
    }, 500); // Wait 500ms for other scripts to initialize
});
</script>
