<?php
/*   __________________________________________________
    |  Obfuscated by YAK Pro - Php Obfuscator  3.0.0   |
    |              on 2026-06-27 04:10:42              |
    |    GitHub: https://github.com/pk-fr/yakpro-po    |
    |__________________________________________________|
*/
paginator->hasPages())
<nav style="display:flex; gap:4px;">
    @if ($paginator->onFirstPage())
        <span class="page-btn disabled">&laquo;</span>
    @else
        <a href="{{ $paginator->previousPageUrl() }}" class="page-btn">&laquo;</a>
    @endif

    @foreach ($elements as $element)
        @if (is_string($element))
            <span class="page-btn disabled">{{ $element }}</span>
        @endif
        @if (is_array($element))
            @foreach ($element as $page => $url)
                @if ($page == $paginator->currentPage())
                    <span class="page-btn active">{{ $page }}</span>
                @else
                    <a href="{{ $url }}" class="page-btn">{{ $page }}</a>
                @endif
            @endforeach
        @endif
    @endforeach

    @if ($paginator->hasMorePages())
        <a href="{{ $paginator->nextPageUrl() }}" class="page-btn">&raquo;</a>
    @else
        <span class="page-btn disabled">&raquo;</span>
    @endif
</nav>
<style>
    .page-btn{display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:8px;font-size:13px;font-weight:500;color:var(--text-secondary);text-decoration:none;border:1px solid var(--border);background:var(--bg-card);transition:all .15s}
    .page-btn:hover{border-color:var(--accent);color:var(--accent)}
    .page-btn.active{background:var(--accent);color:white;border-color:var(--accent)}
    .page-btn.disabled{opacity:.4;pointer-events:none}
</style>
@endif
