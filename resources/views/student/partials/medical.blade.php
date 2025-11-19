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
            <label for="emergency_contact" class="form-label">Emergency Contact</label>
            <input type="text" name="emergency_contact" id="emergency_contact" class="form-control" required placeholder="Name & phone of person to contact">
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
    // Back button → go to Legal tab
    document.getElementById('backToActivities').addEventListener('click', function() {
        var prevTab = new bootstrap.Tab(document.querySelector('#activities-tab'));
        prevTab.show();
    });

    // Next button → go to Medical tab
    document.getElementById('nextToFinance').addEventListener('click', function() {
        var nextTab = new bootstrap.Tab(document.querySelector('#finance-tab'));
        nextTab.show();
    });
});
</script>
