<x-kcat::layout>
    <x-slot name="header"><h2 class="text-xl font-semibold text-slate-900">Edit KCAT Question</h2></x-slot>

    <div class="mx-auto max-w-5xl py-8">
        <form method="POST" action="{{ route('kcat.career-counselor.kcat.questions.update', $question) }}" class="space-y-5 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            @csrf
            @method('PUT')
            @include('kcat::career-counselor.kcat.questions.partials.form', ['question' => $question, 'test' => $test])
        </form>
    </div>
</x-kcat::layout>
