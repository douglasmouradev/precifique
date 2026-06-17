<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header title="Criar tenant" subtitle="Nova conta com e-mail de boas-vindas">
            <x-slot:actions>
                <x-ui.button variant="outline" :href="route('admin.tenants.index')">← Voltar</x-ui.button>
            </x-slot:actions>
        </x-ui.page-header>
    </x-slot>
    <div class="max-w-lg mx-auto">
        <x-ui.card class="p-6">
            <form method="POST" action="{{ route('admin.tenants.store') }}" class="space-y-4">
                @csrf
                <x-ui.input label="Nome" name="name" required />
                <x-ui.input label="E-mail" name="email" type="email" required />
                <x-ui.select label="Nicho" name="niche">
                    <option value="alimentos">Alimentos</option>
                    <option value="servico">Serviço</option>
                    <option value="artesanato">Artesanato</option>
                    <option value="outro">Outro</option>
                </x-ui.select>
                <x-ui.select label="Plano" name="plan">
                    <option value="basic">Basic</option>
                    <option value="premium">Premium</option>
                </x-ui.select>
                <x-ui.button variant="secondary" type="submit" class="w-full py-2.5">Criar e enviar e-mail</x-ui.button>
            </form>
        </x-ui.card>
    </div>
</x-app-layout>
