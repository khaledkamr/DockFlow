@props(['paginator'])

@if ($paginator->hasPages())
    <style>
        /* Custom Pagination Styles */
        .custom-pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
            direction: ltr;
        }

        .custom-pagination .page-item {
            list-style: none;
        }

        .custom-pagination .page-link {
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 40px;
            height: 40px;
            padding: 8px 12px;
            color: #0d6efd;
            background-color: #fff;
            border: 1px solid #0d6efd;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            cursor: pointer;
            font-size: 14px;
        }

        .custom-pagination .page-link:hover:not(.disabled) {
            background-color: #0d6efd;
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(13, 110, 253, 0.3);
        }

        .custom-pagination .page-item.active .page-link {
            background-color: #0d6efd;
            border-color: #0d6efd;
            color: #fff;
            font-weight: 600;
            box-shadow: 0 2px 6px rgba(13, 110, 253, 0.4);
        }

        .custom-pagination .page-item.disabled .page-link {
            color: #6c757d;
            background-color: #e9ecef;
            border-color: #dee2e6;
            pointer-events: none;
            cursor: not-allowed;
            opacity: 0.6;
        }

        .custom-pagination .page-dots {
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 40px;
            height: 40px;
            color: #6c757d;
            font-weight: 500;
        }

        /* Responsive adjustments */
        @media (max-width: 576px) {
            .custom-pagination .page-link {
                min-width: 35px;
                height: 35px;
                padding: 6px 10px;
                font-size: 13px;
            }

            .custom-pagination {
                gap: 4px;
                padding: 15px 0;
            }
        }

        /* Navigation buttons special styling */
        .custom-pagination .nav-button {
            font-weight: 600;
            padding: 8px 16px;
        }

        .custom-pagination .nav-button:hover:not(.disabled) {
            background-color: #0d6efd;
            color: #fff;
        }

        /* Page info text */
        .pagination-info {
            text-align: center;
            color: #6c757d;
            font-size: 14px;
            direction: rtl;
        }
    </style>

    <div class="pagination-wrapper">
        <nav aria-label="Pagination Navigation">
            <ul class="custom-pagination">
                {{-- Previous Page Link --}}
                @if ($paginator->onFirstPage())
                    <li class="page-item disabled">
                        <span class="page-link nav-button" aria-disabled="true">
                            <i class="fas fa-chevron-left"></i>
                        </span>
                    </li>
                @else
                    <li class="page-item">
                        <a class="page-link nav-button" href="{{ $paginator->previousPageUrl() }}" rel="prev"
                            aria-label="السابق">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                    </li>
                @endif

                {{-- Pagination Elements --}}
                @foreach ($paginator->links()->elements as $element)
                    {{-- "Three Dots" Separator --}}
                    @if (is_string($element))
                        <li class="page-item disabled">
                            <span class="page-dots">{{ $element }}</span>
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
                        <a class="page-link nav-button" href="{{ $paginator->nextPageUrl() }}" rel="next"
                            aria-label="التالي">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    </li>
                @else
                    <li class="page-item disabled">
                        <span class="page-link nav-button" aria-disabled="true">
                            <i class="fas fa-chevron-right"></i>
                        </span>
                    </li>
                @endif
            </ul>
        </nav>

        {{-- Pagination Information --}}
        <div class="pagination-info">
            عرض <strong>{{ $paginator->firstItem() }}</strong> إلى <strong>{{ $paginator->lastItem() }}</strong> من أصل
            <strong>{{ $paginator->total() }}</strong> نتيجة
        </div>
    </div>
@endif
