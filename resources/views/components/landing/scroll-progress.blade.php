{{-- Barra horizontal de progresso no topo --}}
<div
    class="scroll-progress-3d-top pointer-events-none"
    x-show="!showIntro"
    x-cloak
    aria-hidden="true"
>
    <div class="scroll-progress-3d-top__track">
        <div class="scroll-progress-3d-top__fill" :style="`width: ${scrollProgress}%`"></div>
    </div>
</div>
