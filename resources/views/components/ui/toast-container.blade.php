<div
    x-data="toastContainer"
    class="fixed top-20 right-4 z-[70] flex flex-col gap-2 w-[min(100vw-2rem,22rem)] pointer-events-none"
    aria-live="polite"
>
    <template x-for="item in items" :key="item.id">
        <div
            x-show="true"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-x-4"
            x-transition:enter-end="opacity-100 translate-x-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0 translate-x-4"
            class="pointer-events-auto rounded-xl border px-4 py-3 text-sm shadow-lg backdrop-blur-sm flex items-start gap-3"
            :class="{
                'bg-white border-brand/30 text-slate-700': item.type === 'success',
                'bg-white border-red-200 text-red-800': item.type === 'error',
                'bg-white border-amber-200 text-amber-900': item.type === 'warning',
                'bg-white border-slate-200 text-slate-700': item.type === 'info',
            }"
        >
            <span class="shrink-0 w-2 h-2 rounded-full mt-1.5" :class="{
                'bg-brand': item.type === 'success',
                'bg-red-500': item.type === 'error',
                'bg-amber-500': item.type === 'warning',
                'bg-slate-400': item.type === 'info',
            }"></span>
            <p class="flex-1 leading-snug" x-text="item.message"></p>
            <button type="button" @click="remove(item.id)" class="text-slate-400 hover:text-slate-600 shrink-0" aria-label="Fechar">×</button>
        </div>
    </template>
</div>
