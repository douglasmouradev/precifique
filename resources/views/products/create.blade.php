@extends('layouts.tenant')
@section('title', 'Novo produto')
@section('breadcrumb') Produtos / Novo @endsection

@section('content')
<x-ui.page-header title="Novo produto" subtitle="Depois você será levado à precificação" />

<x-ui.card class="max-w-lg">
    <form method="POST" action="{{ route('tenant.products.store') }}" enctype="multipart/form-data" class="space-y-5">
        @csrf
        <div><label class="ui-label">Nome</label><input name="name" required class="ui-input"></div>
        <div><label class="ui-label">Descrição</label><textarea name="description" rows="3" class="ui-input"></textarea></div>
        <div>
            <label class="ui-label">Nicho</label>
            <select name="niche_type" class="ui-input">
                <option value="alimentos">Alimentos</option>
                <option value="servico">Serviços</option>
                <option value="artesanato">Artesanato</option>
            </select>
        </div>
        <div>
            <label class="ui-label">Foto @if(($tenant->interface_mode ?? '') === 'artesanato')<span class="text-red-500">*</span>@endif</label>
            <input type="file" name="photo" accept="image/*" class="ui-input file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-brand/10 file:text-brand-dark file:font-semibold">
        </div>
        <x-ui.button type="submit" class="w-full py-3">Continuar para precificação</x-ui.button>
    </form>
</x-ui.card>
@endsection
