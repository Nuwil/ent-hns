@extends('layouts.app')
@section('title', 'Edit Patient')
@section('page-title', 'Edit Patient')

@section('content')
@php $role = auth()->user()->role; @endphp

<div class="page-content">
    <div class="page-header-row">
        <h1 class="page-heading">Edit Patient - {{ $patient->full_name }}</h1>
        <a href="{{ route("{$role}.patients.show", $patient) }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Back to Patients
        </a>
    </div>

    <form method="POST" action="{{ route("{$role}.patients.update", $patient) }}" novalidate>
        @csrf
        @method('PUT')
        <!-- Form fields for patient registration -->
         @include('patients.partials._form')

        <div class="mt-4 d-flex gap-2">
            <button type="submit" class="btn btn-primary"><i class="bi bi-check2 me-1"></i>Save Changes</button>
            <a href="{{ route("{$role}.patients.show", $patient) }}" class="btn btn-outline-secondary">Cancel</a>
        </div>
    </form>
</div>    
@endsection