<div class="card p-4 shadow-sm rounded-3">
    <h4 class="mb-3">Co-Curricular Activities</h4>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="sports" class="form-label">Sports Involved</label>
            <select class="form-control select @error('sports') is-invalid @enderror" name="sports">
                <option selected disabled>Select Sport</option>
                <option value="Football">Football</option>
                <option value="Rugby" >Rugby</option>
                <option value="Volleyball">Volleyball</option>
                <option value="Chess" >Chess</option>
                <option value="Tennis" >Tenis</option>
                <option value="Swimming">Swimming</option>
                <option value="others" >others</option>
            </select>
            <small class="text-muted">Hold Ctrl (Windows) or Command (Mac) to select multiple.</small>
        </div>

        <div class="col-md-6 mb-3">
            <label for="clubs" class="form-label">Clubs / Societies</label>
            <select name="clubs" id="clubs" class="form-select" >
                <option value="Science Club">Science Club</option>
                <option value="Music Club">Music Club</option>
                <option value="Drama Club">Drama Club</option>
                <option value="Debate Club">Debate Club</option>
                <option value="Environment Club">Environment Club</option>
                <option value="Other">Other</option>
            </select>
            <small class="text-muted">You can select more than one club.</small>
        </div>

        <div class="col-md-12 mb-3">
            <label for="talents" class="form-label">Special Talents / Interests</label>
            <textarea name="talents" id="talents" class="form-control" rows="3"
                placeholder="E.g., Singing, Coding, Drawing..."></textarea>
        </div>
    </div>

    <!-- Navigation Buttons -->
    <div class="d-flex justify-content-between mt-4">
        <button type="button" class="btn btn-secondary" id="backToLegal">← Back</button>
        <button type="button" class="btn btn-primary" id="nextToMedical">Next Page →</button>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Back button → go to Legal tab
    document.getElementById('backToLegal').addEventListener('click', function() {
        var prevTab = new bootstrap.Tab(document.querySelector('#legal-tab'));
        prevTab.show();
    });

    // Next button → go to Medical tab
    document.getElementById('nextToMedical').addEventListener('click', function() {
        var nextTab = new bootstrap.Tab(document.querySelector('#medical-tab'));
        nextTab.show();
    });
});
</script>
