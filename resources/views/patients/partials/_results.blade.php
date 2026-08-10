<div class="table-responsive">
    <table class="table data-table">
        <thead>
            <tr>
                <th class="sortable-col" style="cursor:pointer">
                    <button type="button" class="btn btn-link p-0 text-decoration-none text-reset fw-semibold" data-sort="last_name">Patient <span class="sort-indicator" data-sort-key="last_name"></span></button>
                </th>
                <th class="sortable-col" style="cursor:pointer">
                    <button type="button" class="btn btn-link p-0 text-decoration-none text-reset fw-semibold" data-sort="date_of_birth">Age / Gender <span class="sort-indicator" data-sort-key="date_of_birth"></span></button>
                </th>
                <th>Phone</th>
                <th>Visits</th>
                <th class="sortable-col" style="cursor:pointer">
                    <button type="button" class="btn btn-link p-0 text-decoration-none text-reset fw-semibold" data-sort="created_at">Registered <span class="sort-indicator" data-sort-key="created_at"></span></button>
                </th>
                <th></th>
            </tr>
        </thead>
        <tbody>@forelse($patients as $patient)
            <tr class="patient-row" data-href="{{ route("{$role}.patients.show", $patient) }}" style="cursor:pointer">
                <td>
                    <div class="d-flex align-items-center gap-2"><span class="repeat-patient-indicator"
                            style="display:{{ $patient->visits_this_month_count >= 2 ? 'inline-flex' : 'none' }}"
                            title="Repeat patient: {{ $patient->visits_this_month_count }} visits this month"></span>
                        <div class="table-avatar">
                            {{strtoupper(substr($patient->last_name, 0, 1))}}
                        </div>
                        <div>
                            <div class="fw-semibold">
                                {{$patient->last_name}},{{$patient->first_name}}
                            </div>
                            <div class="text-muted small">
                                {{$patient->email}}
                            </div>
                            @if($patient->visits_this_month_count >= 2)
                                <div class="repeat-patient-label">Repeat this
                            month</div>@endif
                        </div>
                    </div>
                </td>
                <td>{{$patient->age}}·{{ucfirst($patient->gender)}}</td>
                <td>{{$patient->phone}}</td>
                <td>{{$patient->visits_count}}</td>
                <td>{{$patient->created_at->format('M j,Y')}}</td>
                <td>
                    <div class="d-flex gap-1">
                        <button type="button" class="btn btn-sm btn-outline-success edit-patient-btn"
                            data-update-url="{{ route("{$role}.patients.update", $patient) }}"
                            data-patient='{!! json_encode($patient->only(["first_name", "last_name", "date_of_birth", "gender", "religion", "phone", "occupation", "province", "city", "address", "allergies", "insurance_info", "medical_history"])) !!}'>
                            <i class="bi bi-pencil"></i> Edit
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-danger"
                            onclick="confirmDeletePatient({{$patient->id}},'{{addslashes($patient->full_name)}}')">
                            <i class="bi bi-trash"></i> Delete
                        </button>
                        <form method="POST" id="deletePatient-{{$patient->id}}"
                            action="{{ route("{$role}.patients.destroy", $patient) }}">
                            @csrf @method('DELETE')
                        </form>
                    </div>
                </td>
        </tr>@empty<tr>
                <td colspan="7" class="text-center py-5 text-muted"><i
                        class="bi bi-people fs-2 d-block mb-2"></i>{{$search ? "No patients match \"$search\"" : 'No patients registered yet'}}
                </td>
            </tr>@endforelse
        </tbody>
    </table>
</div>
@if($patients->hasPages())
    <div class="card-panel-footer">
        <div style="display:flex;justify-content:start;">{{$patients->links('pagination::bootstrap-4')}}
        </div>
    </div>
@endif