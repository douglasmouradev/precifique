@extends('layouts.landing')
@section('title', 'Política de Privacidade')
@section('content')
<article class="max-w-3xl mx-auto py-24 px-4 prose">
    <h1>Política de Privacidade</h1>
    <p>Versão {{ config('lgpd.policy_version') }}. O Precifique coleta dados de cadastro (nome, e-mail), dados de uso e consentimentos LGPD.</p>
    <p>Você pode exportar ou solicitar exclusão dos dados pelo portal do titular.</p>
    <p><a href="{{ route('home') }}">← Voltar</a></p>
</article>
@endsection
