@if ($paginator->hasPages())
    <nav role="navigation" aria-label="صفحه‌بندی" style="display: flex; justify-content: center; align-items: center; gap: 8px; margin-top: 30px;">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <span aria-disabled="true" aria-label="قبلی" style="display: inline-flex; align-items: center; justify-content: center; min-width: 40px; height: 40px; padding: 0 12px; border: 2px solid #e5e7eb; border-radius: 10px; color: #9ca3af; background: #f3f4f6; cursor: not-allowed; opacity: 0.5;">
                ‹
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="قبلی" style="display: inline-flex; align-items: center; justify-content: center; min-width: 40px; height: 40px; padding: 0 12px; border: 2px solid #e5e7eb; border-radius: 10px; text-decoration: none; color: #374151; font-weight: 500; background: white; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);">
                ‹
            </a>
        @endif

        {{-- Pagination Elements --}}
        @foreach ($elements as $element)
            {{-- "Three Dots" Separator --}}
            @if (is_string($element))
                <span aria-disabled="true" style="display: inline-flex; align-items: center; justify-content: center; min-width: 40px; height: 40px; padding: 0 12px; border: 2px solid #e5e7eb; border-radius: 10px; color: #9ca3af; background: white;">
                    {{ $element }}
                </span>
            @endif

            {{-- Array Of Links --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span aria-current="page" style="display: inline-flex; align-items: center; justify-content: center; min-width: 40px; height: 40px; padding: 0 12px; border: 2px solid #f96c08; border-radius: 10px; color: white; font-weight: 500; background: linear-gradient(135deg, #f96c08 0%, #e37415 100%); box-shadow: 0 4px 12px rgba(249, 108, 8, 0.4);">
                            {{ $page }}
                        </span>
                    @else
                        <a href="{{ $url }}" aria-label="برو به صفحه {{ $page }}" style="display: inline-flex; align-items: center; justify-content: center; min-width: 40px; height: 40px; padding: 0 12px; border: 2px solid #e5e7eb; border-radius: 10px; text-decoration: none; color: #374151; font-weight: 500; background: white; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);">
                            {{ $page }}
                        </a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="بعدی" style="display: inline-flex; align-items: center; justify-content: center; min-width: 40px; height: 40px; padding: 0 12px; border: 2px solid #e5e7eb; border-radius: 10px; text-decoration: none; color: #374151; font-weight: 500; background: white; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);">
                ›
            </a>
        @else
            <span aria-disabled="true" aria-label="بعدی" style="display: inline-flex; align-items: center; justify-content: center; min-width: 40px; height: 40px; padding: 0 12px; border: 2px solid #e5e7eb; border-radius: 10px; color: #9ca3af; background: #f3f4f6; cursor: not-allowed; opacity: 0.5;">
                ›
            </span>
        @endif
    </nav>

    {{-- Page Info --}}
    <div style="text-align: center; margin-top: 15px; color: #6b7280; font-size: 14px;">
        نمایش {{ $paginator->firstItem() }} تا {{ $paginator->lastItem() }} از {{ $paginator->total() }} مورد
    </div>
@endif
