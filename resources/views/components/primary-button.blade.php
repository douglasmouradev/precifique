<button {{ $attributes->merge(['type' => 'submit', 'class' => 'ui-btn-primary px-5 py-2.5 text-sm']) }}>
    {{ $slot }}
</button>
