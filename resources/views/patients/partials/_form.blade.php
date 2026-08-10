@php $patient = $patient ?? null; @endphp
<div class="row g-4">

    <div class="col-lg-8">
        <div class="card-panel">
            <div class="card-panel-header">
                <div class="card-panel-title"><i class="bi bi-person me-2"></i>Personal Information</div>
            </div>
            <div class="card-panel-body">
                <div class="row g-3">

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">First Name<span class="text-danger">*</span></label>
                        <input type="text" name="first_name"
                            class="form-control @error('first_name') is-invalid @enderror"
                            value="{{ old('first_name', $patient->first_name ?? '') }}" required>
                        @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Last Name<span class="text-danger">*</span></label>
                        <input type="text" name="last_name"
                            class="form-control @error('last_name') is-invalid @enderror"
                            value="{{ old('last_name', $patient->last_name ?? '') }}" required>
                        @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Date of Birth<span class="text-danger">*</span></label>
                        <input type="date" name="date_of_birth" id="dobInput" min="1900-01-01" max="{{ date('Y-m-d') }}"
                            class="form-control @error('date_of_birth') is-invalid @enderror"
                            value="{{ old('date_of_birth', optional($patient?->date_of_birth)->format('Y-m-d')) }}"
                            required onchange="calcAge(this.value)">
                        @error('date_of_birth')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-2">
                        <label class="form-label fw-semibold">Age</label>
                        <input type="text" id="ageDisplay" class="form-control" readonly
                            value="{{ $patient ? $patient->age . ' yrs' : '' }}"
                            placeholder="Auto" style="background:#f8fafc">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Gender<span class="text-danger">*</span></label>
                        <select name="gender" class="form-select @error('gender') is-invalid @enderror" required>
                            <option value="">Select...</option>
                            @foreach(['male' => 'Male', 'female' => 'Female', 'other' => 'Other'] as $v => $l)
                                <option value="{{ $v }}" {{ old('gender', $patient->gender ?? '') === $v ? 'selected' : '' }}>{{ $l }}</option>
                            @endforeach
                        </select>
                        @error('gender')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Religion</label>
                        <input type="text" name="religion" class="form-control"
                            value="{{ old('religion', $patient->religion ?? '') }}" placeholder="e.g. Catholic, Muslim">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Phone Number<span class="text-danger">*</span></label>
                        <input type="text" name="phone"
                            class="form-control @error('phone') is-invalid @enderror"
                            value="{{ old('phone', $patient->phone ?? '') }}" required placeholder="e.g. 09XX-XXX-XXXX">
                        @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Occupation</label>
                        <input type="text" name="occupation" class="form-control"
                            value="{{ old('occupation', $patient->occupation ?? '') }}" placeholder="e.g. Teacher, Engineer">
                    </div>

                </div>
            </div>
        </div>

        <div class="card-panel mt-4">
            <div class="card-panel-header">
                <div class="card-panel-title"><i class="bi bi-geo-alt me-2"></i>Address — Philippines</div>
            </div>
            <div class="card-panel-body">
                <div class="row g-3">

                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Province</label>
                        <select name="province" id="provinceSelect" class="form-select"
                            onchange="loadCities(this.value)">
                            <option value="">Select Province...</option>
                            @foreach(\App\Helpers\PhilippinesHelper::provinces() as $prov)
                                <option value="{{ $prov }}" {{ old('province', $patient->province ?? '') === $prov ? 'selected' : '' }}>{{ $prov }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold">City / Municipality</label>
                        <select name="city" id="citySelect" class="form-select">
                            <option value="">Select Province first...</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Street / Barangay</label>
                        <input type="text" name="address" class="form-control"
                            value="{{ old('address', $patient->address ?? '') }}" placeholder="House No., Street, Barangay">
                    </div>

                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card-panel">
            <div class="card-panel-header">
                <div class="card-panel-title"><i class="bi bi-shield-plus me-2"></i>Medical Info</div>
            </div>
            <div class="card-panel-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Known Allergies</label>
                    <textarea name="allergies" class="form-control" rows="2"
                        placeholder="e.g. Penicillin, Aspirin, None">{{ old('allergies', $patient->allergies ?? '') }}</textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Insurance Information</label>
                    <input type="text" name="insurance_info" class="form-control"
                        value="{{ old('insurance_info', $patient->insurance_info ?? '') }}" placeholder="Provider / Policy No.">
                </div>

                <div>
                    <label class="form-label fw-semibold">Medical & Vaccine History</label>
                    <textarea name="medical_history" class="form-control" rows="5"
                        placeholder="Past illnesses, surgeries, vaccinations...">{{ old('medical_history', $patient->medical_history ?? '') }}</textarea>
                </div>
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
function calcAge(dob) {
    if (!dob) return;
    const today = new Date(), birth = new Date(dob);
    let age = today.getFullYear() - birth.getFullYear();
    if (today.getMonth() - birth.getMonth() < 0 ||
        (today.getMonth() === birth.getMonth() && today.getDate() < birth.getDate())) {
        age--;
    }
    document.getElementById('ageDisplay').value = age >= 0 ? age + ' yrs' : '';
}

const citiesData = @json(\App\Helpers\PhilippinesHelper::cities());

function loadCities(province, selected = '') {
    const sel = document.getElementById('citySelect');
    const cities = citiesData[province] || [];
    sel.innerHTML = cities.length
        ? '<option value="">Select City...</option>' +
          cities.map(c => `<option value="${c}" ${c === selected ? 'selected' : ''}>${c}</option>`).join('')
        : '<option value="">No cities found</option>';
}

@if(old('province', $patient->province ?? null))
    loadCities('{{ old('province', $patient->province ?? '') }}', '{{ old('city', $patient->city ?? '') }}');
@endif
</script>
@endpush