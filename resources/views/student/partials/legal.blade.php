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
                <label>Class <span class="login-danger">*</span></label>
                <select class="form-control select @error('class') is-invalid @enderror" name="class">
                    <option selected disabled>Please Select Class</option>
                    @for ($i = 1; $i <= 9; $i++)
                        <option value="Grade {{ $i }}" {{ old('class') == "Grade $i" ? 'selected' : '' }}>Grade {{ $i }}</option>
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
            <label>Parent Name <span class="login-danger">*</span></label>
            <input type="text" name="parent_name" id="parent_name" class="form-control" placeholder="Enter Parent's name" required>
        </div>

        <div class="col-md-6 mb-3">
            <label for="parent_relationship" class="form-label">Relationship</label>
            <input type="text" name="parent_relationship" id="parent_relationship" class="form-control" required>
        </div>

        <div class="col-md-6 mb-3">
            <label for="parent_number" class="form-label">Phone Number</label>
            <input type="text" name="parent_number" id="parent_number" class="form-control" placeholder="+254712345678" required>
            <small class="text-muted">Format: +254712345678</small>
        </div>

        <div class="col-md-6 mb-3">
            <label for="parent_email" class="form-label">Email Address</label>
            <input type="email" name="parent_email" id="parent_email" class="form-control" placeholder="Enter parent email">
        </div>

        <div class="col-md-6 mb-3">
            <label for="guardian_name" class="form-label">Guardian Name</label>
            <input type="text" name="guardian_name" id="guardian_name" class="form-control" placeholder="Enter Guardian Name">
        </div>

        <div class="col-md-6 mb-3">
            <label for="guardian_number" class="form-label">Phone Number</label>
            <input type="text" name="guardian_number" id="guardian_number" class="form-control" placeholder="+254712345678">
            <small class="text-muted">Format: +254712345678</small>
        </div>

        <div class="col-md-6 mb-3">
            <label for="guardian_email" class="form-label">Email Address</label>
            <input type="email" name="guardian_email" id="guardian_email" class="form-control" placeholder="Enter parent email">
        </div>
        <div class="text-end mt-4">
            <button type="button" class="btn btn-primary" id="nextToActivities">
                Next Page →
            </button>
        </div>
    </div>
</div>

<script>
document.getElementById('nextToActivities').addEventListener('click', function() {
    var nextTab = new bootstrap.Tab(document.querySelector('#activities-tab'));
    nextTab.show();
});
</script>
