{{--
    Activity Log Widget Partial
    Usage: @include('partials.activity-log', ['activityLogs' => $activityLogs])
--}}
<div class="card-panel activity-log-panel">
    <div class="card-panel-header">
        <div class="card-panel-title">
            <i class="bi bi-shield-check me-2"></i>Recent Activity
        </div>
        <span class="badge bg-secondary">{{ $activityLogs->count() }} entries</span>
    </div>

    <div class="activity-log-list">
        @forelse($activityLogs as $log)
            <div class="activity-log-item {{ $log->severityColorClass() }}">

                {{-- Severity stripe is set via CSS class on the item --}}
                <div class="activity-log-icon-wrap">
                    <i class="bi {{ $log->iconClass() }}"></i>
                </div>

                <div class="activity-log-content">
                    <div class="activity-log-desc">
                        {{-- Bold actor name --}}
                        @if($log->user_name)
                            <span class="activity-log-actor">{{ $log->user_name }}</span>
                        @endif

                        {{-- Action label --}}
                        <span class="activity-log-action-label {{ $log->severityColorClass() }}-text">
                            {{ \App\Helpers\ActivityLogHelper::actionLabel($log->action) }}
                        </span>

                        {{-- Subject (patient name, appointment #, etc.) --}}
                        @if($log->subject_label)
                            <span class="activity-log-subject">{{ $log->subject_label }}</span>
                        @endif
                    </div>

                    <div class="activity-log-meta">
                        @if($log->ip_address && in_array($log->action, ['auth.login_failed', 'auth.login']))
                            <span class="activity-log-ip">
                                <i class="bi bi-router me-1"></i>{{ $log->ip_address }}
                            </span>
                            <span class="activity-log-sep">·</span>
                        @endif
                        @if($log->user_role)
                            <span class="activity-log-role">{{ ucfirst($log->user_role) }}</span>
                            <span class="activity-log-sep">·</span>
                        @endif
                        <span class="activity-log-time" title="{{ $log->created_at->format('M j, Y H:i:s') }}">
                            {{ $log->created_at->diffForHumans() }}
                        </span>
                    </div>
                </div>

                {{-- Severity badge on the right --}}
                @if($log->severity !== 'info')
                    <div class="activity-log-badge">
                        <span class="badge {{ $log->severity === 'danger' ? 'bg-danger' : 'bg-warning text-dark' }}">
                            {{ strtoupper($log->severity) }}
                        </span>
                    </div>
                @endif

            </div>
        @empty
            <div class="activity-log-empty">
                <i class="bi bi-shield-check fs-3 mb-2 d-block text-muted"></i>
                <span class="text-muted">No activity recorded yet.</span>
            </div>
        @endforelse
    </div>
</div>