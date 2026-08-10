@if ($paginator->hasPages())
    <style>
        .pagination-wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 0.5rem;
            flex-wrap: wrap;
            padding: 1.5rem 0;
        }

        .pagination {
            display: flex;
            gap: 0.25rem;
            list-style: none;
            margin: 0;
            padding: 0;
            flex-wrap: wrap;
            justify-content: center;
        }

        .pagination .page-item {
            display: inline-block;
            margin: 0;
        }

        .pagination .page-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 2.5rem;
            height: 2.5rem;
            padding: 0;
            border: 1px solid var(--card-border);
            border-radius: 8px;
            background: var(--card-bg);
            color: var(--text-secondary);
            font-weight: 500;
            font-size: 0.875rem;
            text-decoration: none;
            transition: all var(--transition);
            cursor: pointer;
        }

        .pagination .page-link:hover {
            background: var(--color-primary-light);
            border-color: var(--color-primary);
            color: var(--color-primary);
            transform: translateY(-2px);
        }

        .pagination .page-item.active .page-link {
            background: var(--color-primary);
            border-color: var(--color-primary);
            color: white;
            box-shadow: 0 2px 8px rgba(37, 99, 235, 0.2);
        }

        .pagination .page-item.disabled .page-link {
            background: #f8fafc;
            border-color: #e2e8f0;
            color: var(--text-muted);
            cursor: not-allowed;
            opacity: 0.5;
        }

        .pagination .page-item.disabled .page-link:hover {
            background: #f8fafc;
            border-color: #e2e8f0;
            color: var(--text-muted);
            transform: none;
        }

        /* Responsive design */
        @media (max-width: 576px) {
            .pagination-wrapper {
                padding: 1rem 0;
                gap: 0.25rem;
            }

            .pagination .page-link {
                min-width: 2.25rem;
                height: 2.25rem;
                font-size: 0.8125rem;
            }

            .pagination .page-item:not(.page-item.active):not(.page-item.disabled) .page-link {
                display: none;
            }

            .pagination .page-item.active .page-link,
            .pagination .page-item.disabled .page-link {
                display: inline-flex;
            }

            .pagination-info {
                font-size: 0.8125rem;
            }
        }

        .pagination-info {
            color: var(--text-secondary);
            font-size: 0.875rem;
        }
    </style>

    <nav aria-label="Page navigation" class="d-flex justify-content-center">
        <ul class="pagination">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li class="page-item disabled" aria-disabled="true">
                    <span class="page-link" aria-hidden="true">
                        <i class="bi bi-chevron-left"></i>
                    </span>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Previous">
                        <i class="bi bi-chevron-left"></i>
                    </a>
                </li>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <li class="page-item disabled" aria-disabled="true">
                        <span class="page-link">{{ $element }}</span>
                    </li>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="page-item active" aria-current="page">
                                <span class="page-link">{{ $page }}</span>
                            </li>
                        @else
                            <li class="page-item">
                                <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                            </li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Next">
                        <i class="bi bi-chevron-right"></i>
                    </a>
                </li>
            @else
                <li class="page-item disabled" aria-disabled="true">
                    <span class="page-link" aria-hidden="true">
                        <i class="bi bi-chevron-right"></i>
                    </span>
                </li>
            @endif
        </ul>
    </nav>
@endif