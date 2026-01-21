<div class="card p-4 shadow-sm rounded-3">
    <h4 class="mb-3">Medical Information</h4>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="blood_group" class="form-label">Blood Group</label>
            <select name="blood_group" id="blood_group" class="form-select">
                <option value="">-- Select Blood Group --</option>
                <option value="A+">A+</option>
                <option value="A-">A-</option>
                <option value="B+">B+</option>
                <option value="B-">B-</option>
                <option value="AB+">AB+</option>
                <option value="AB-">AB-</option>
                <option value="O+">O+</option>
                <option value="O-">O-</option>
            </select>
        </div>

        <div class="col-md-6 mb-3">
            <label for="known_allergies" class="form-label">Known Allergies</label>
            <input type="text" name="known_allergies" id="known_allergies" class="form-control" placeholder="E.g., Peanuts, Dust, etc.">
        </div>

        <div class="col-md-6 mb-3">
            <label for="medical_condition" class="form-label">Medical Conditions</label>
            <input type="text" name="medical_condition" id="medical_condition" class="form-control" placeholder="E.g., Asthma, Diabetes, etc.">
        </div>

        <div class="col-md-6 mb-3">
            <label for="doctor_contact" class="form-label">Doctor Contact (Optional)</label>
            <input type="text" name="doctor_contact" id="doctor_contact" class="form-control" placeholder="Doctor name & phone number">
        </div>

        <div class="col-md-12 mb-3">
            <label class="form-label">Does the child suffer any ailment? <span class="login-danger">*</span></label>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="has_ailment" id="has_ailment_yes" value="1" {{ old('has_ailment') == '1' ? 'checked' : '' }}>
                <label class="form-check-label" for="has_ailment_yes">Yes</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="has_ailment" id="has_ailment_no" value="0" {{ old('has_ailment') == '0' || old('has_ailment') == null ? 'checked' : '' }}>
                <label class="form-check-label" for="has_ailment_no">No</label>
            </div>
        </div>

        <div class="col-md-12 mb-3" id="ailment_details_container" style="display: none;">
            <label for="ailment_details" class="form-label">If Yes, Give Details</label>
            <textarea name="ailment_details" id="ailment_details" class="form-control" rows="3" placeholder="Enter ailment details">{{ old('ailment_details') }}</textarea>
        </div>

        <div class="col-md-12 mb-3">
            <label class="form-label">In case of any emergency, who should we contact? <span class="login-danger">*</span></label>
        </div>

        <div class="col-md-6 mb-3">
            <label for="emergency_contact_name" class="form-label">Emergency Contact Name <span class="login-danger">*</span></label>
            <input type="text" name="emergency_contact_name" id="emergency_contact_name" class="form-control @error('emergency_contact_name') is-invalid @enderror" required placeholder="Enter contact name" value="{{ old('emergency_contact_name') }}">
            @error('emergency_contact_name')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <div class="col-md-6 mb-3">
            <label for="emergency_contact_telephone" class="form-label">Emergency Contact Telephone <span class="login-danger">*</span></label>
            <input type="text" name="emergency_contact_telephone" id="emergency_contact_telephone" class="form-control @error('emergency_contact_telephone') is-invalid @enderror" required placeholder="0726554037" value="{{ old('emergency_contact_telephone') }}">
            <small class="text-muted">Format: 0726554037</small>
            @error('emergency_contact_telephone')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <div class="col-md-12 mb-3">
            <label for="emergency_contact" class="form-label">Emergency Contact (Additional Notes)</label>
            <input type="text" name="emergency_contact" id="emergency_contact" class="form-control" placeholder="Additional emergency contact information" value="{{ old('emergency_contact') }}">
        </div>
    </div>

    <!-- Navigation Buttons -->
    <div class="d-flex justify-content-between mt-4">
        <button type="button" class="btn btn-secondary" id="backToActivities">← Back</button>
        <button type="button" class="btn btn-primary" id="nextToFinance">Next Page →</button>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Back button → go to Activities tab
    document.getElementById('backToActivities').addEventListener('click', function() {
        var prevTab = new bootstrap.Tab(document.querySelector('#activities-tab'));
        prevTab.show();
    });

    // Next button → go to Finance tab
    document.getElementById('nextToFinance').addEventListener('click', function() {
        var nextTab = new bootstrap.Tab(document.querySelector('#finance-tab'));
        nextTab.show();
    });

    // Show/hide ailment details based on radio selection
    const ailmentYes = document.getElementById('has_ailment_yes');
    const ailmentNo = document.getElementById('has_ailment_no');
    const ailmentDetailsContainer = document.getElementById('ailment_details_container');

    function toggleAilmentDetails() {
        if (ailmentYes.checked) {
            ailmentDetailsContainer.style.display = 'block';
        } else {
            ailmentDetailsContainer.style.display = 'none';
        }
    }

    ailmentYes.addEventListener('change', toggleAilmentDetails);
    ailmentNo.addEventListener('change', toggleAilmentDetails);
    
    // Initialize on page load
    toggleAilmentDetails();
});
</script>
