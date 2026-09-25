@extends('layouts.app')

@section('title', 'Market & Farmer Approval Engine | MarketLink Admin')

@section('content')
<div class="container py-4">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h2 class="mb-1">Market & Farmer Approval Engine</h2>
            <p class="text-muted small mb-0">Audit regional producer credentials, enforce organic certifications, and manage stall access permissions</p>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-sm btn-outline-forest">
            <i class="fa-solid fa-arrow-left me-1"></i> Directorate Overview
        </a>
    </div>

    <!-- Farmers Table -->
    <div class="ml-card p-4">
        <div class="table-responsive">
            <table class="table align-middle small mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 25%;">Producer Stall</th>
                        <th>User Account</th>
                        <th>Designated Market</th>
                        <th>Certifications</th>
                        <th>Carbon Rating</th>
                        <th>Produce Count</th>
                        <th>Approval Status</th>
                        <th class="text-end">Engine Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($farmers as $f)
                        <tr>
                            <td>
                                <strong class="text-dark d-block">{{ $f->stall_name }}</strong>
                                <span class="badge bg-light text-dark border">{{ $f->stall_number }}</span>
                                <div class="text-muted" style="font-size: 0.72rem;">{{ $f->farm_origin }}</div>
                            </td>
                            <td>
                                <div>{{ $f->user ? $f->user->name : 'N/A' }}</div>
                                <span class="text-muted" style="font-size: 0.72rem;">{{ $f->user ? $f->user->email : '' }}</span>
                            </td>
                            <td>
                                {{ $f->market ? $f->market->name : 'Unassigned' }}
                            </td>
                            <td>
                                <span class="badge bg-light text-success border" style="font-size: 0.72rem;">
                                    <i class="fa-solid fa-certificate me-1"></i> {{ $f->certifications ?? 'Self-Attested' }}
                                </span>
                            </td>
                            <td>
                                <span class="badge-co2">{{ $f->carbon_score_rating }}/5.0</span>
                            </td>
                            <td class="fw-semibold">
                                {{ $f->products->count() }} items
                            </td>
                            <td>
                                @if($f->is_approved)
                                    <span class="badge bg-success text-white">Approved & Active</span>
                                @else
                                    <span class="badge bg-danger text-white">Suspended / Pending</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <form action="{{ route('admin.farmer.toggle_approval', $f->id) }}" method="POST">
                                    @csrf
                                    @if($f->is_approved)
                                        <button type="submit" class="btn btn-sm btn-outline-danger py-1 px-2" title="Suspend vendor access">
                                            <i class="fa-solid fa-ban me-1"></i> Suspend
                                        </button>
                                    @else
                                        <button type="submit" class="btn btn-sm btn-forest py-1 px-2" title="Grant verified market stall access">
                                            <i class="fa-solid fa-check me-1"></i> Approve
                                        </button>
                                    @endif
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $farmers->links() }}
        </div>
    </div>

</div>
@endsection
