@extends('layouts.app')
@section('title', 'Patients')
@section('page-title', 'Patients')

@section('content')
@php $role = auth()->user()->role; @endphp

<div class="page-content">
    <div class="page-header-row">
        <h1 class="page-heading">Patient Registry</h1>
        @if(in_array($role, ['secretary', 'doctor']))
            <a href="{{ route("{$role}.patients.create") }}" class="btn btn-primary">
                <i class="bi bi-person-plus-fill me-1"></i>New Patient
            </a>
        @endif
    </div>

    <div class="card-panel">
        {{-- Search bar --}}
        <div class="card-panel-toolbar">
            <form method="GET" class="d-flex gap-2">
                <div class="input-icon-wrapper" style="flex:1">
                    <i class="bi bi-search input-icon"></i>
                    <input type="text" name="search" class="form-control input-with-icon"
                           placeholder="Search by name, phone, or email..."
                           value="{{ $search }}">
                </div>
                <button type="submit" class="btn btn-primary">Search</button>
                @if($search)
                    <a href="{{ request()->url() }}" class="btn btn-outline-secondary">Clear</a>
                @endif
            </form>
        </div>

        <div class="table-responsive">
            <table class="table data-table">
                <thead>
                    <tr>
                        <th>Patient</th>
                        <th>Age / Gender</th>
                        <th>Phone</th>
                        <!-- <th>Blood Type</th> -->
                        <th>Visits</th>
                        <th>Registered</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($patients as $patient)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="table-avatar">
                                        {{ strtoupper(substr($patient->first_name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="fw-semibold">{{ $patient->full_name }}</div>
                                        <div class="text-muted small">{{ $patient->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $patient->age }} · {{ ucfirst($patient->gender) }}</td>
                            <td>{{ $patient->phone }}</td>
                            <!-- <td>
                                @if($patient->blood_type)
                                    <span class="badge bg-danger">{{ $patient->blood_type }}</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif -->
                            </td>
                            <td>{{ $patient->visits_count }}</td>
                            <td>{{ $patient->created_at->format('M j, Y') }}</td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="{{ route("{$role}.patients.show", $patient) }}"
                                       class="btn btn-sm btn-outline-primary" title="View">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route("{$role}.patients.edit", $patient) }}"
                                       class="btn btn-sm btn-outline-secondary" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button type="button"
                                            class="btn btn-sm btn-outline-danger"
                                            title="Delete"
                                            onclick="confirmDeletePatient({{ $patient->id }}, '{{ addslashes($patient->full_name) }}')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                    <form method="POST" id="deletePatient-{{ $patient->id }}"
                                          action="{{ route("{$role}.patients.destroy", $patient) }}">
                                        @csrf @method('DELETE')
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-people fs-2 d-block mb-2"></i>
                                {{ $search ? "No patients match \"$search\"" : 'No patients registered yet' }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($patients->hasPages())
            <div class="card-panel-footer">
                {{ $patients->links() }}
            </div>
        @endif
    </div>
</div>

{{-- Delete Confirmation Modal --}}
<div class="modal fade" id="deletePatientModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title text-danger">
                    <i class="bi bi-exclamation-triangle me-2"></i>Delete Patient
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="mb-0">Delete <strong id="deletePatientName"></strong>? This removes all their data including visits and appointments.</p>
                <p class="text-danger small mt-2 mb-0">
                    <i class="bi bi-info-circle me-1"></i>Patients with finalized visit records cannot be deleted.
                </p>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeletePatientBtn">
                    <i class="bi bi-trash me-1"></i>Delete
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let pendingDeletePatientId = null;
function confirmDeletePatient(id, name) {
    pendingDeletePatientId = id;
    document.getElementById('deletePatientName').textContent = name;
    new bootstrap.Modal(document.getElementById('deletePatientModal')).show();
}
document.getElementById('confirmDeletePatientBtn').addEventListener('click', function () {
    if (pendingDeletePatientId) {
        document.getElementById('deletePatient-' + pendingDeletePatientId).submit();
    }
});
</script>
@endpush
@endsection