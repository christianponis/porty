<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-bold text-slate-900">Autovalutazione - {{ $berth->title }}</h2>
            <a href="{{ route('owner.berths.show', $berth) }}" class="btn-secondary text-sm">Torna al posto</a>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        @if ($assessment && $assessment->status->value === 'submitted')
            {{-- Riepilogo assessment completato --}}
            <div class="card card-body mb-8">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-lg font-bold text-slate-900">Autovalutazione completata</h3>
                        <p class="text-sm text-slate-500">Inviata il {{ $assessment->submitted_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <div class="text-center">
                        <x-anchor-rating :count="$assessment->anchor_count" level="grey" size="lg" />
                        <p class="text-sm text-slate-500 mt-1">Punteggio: {{ $assessment->total_score }}/100</p>
                    </div>
                </div>
                <div class="border-t border-slate-100 pt-4">
                    <p class="text-sm text-slate-600">Puoi aggiornare la tua autovalutazione compilando nuovamente il questionario qui sotto.</p>
                </div>
            </div>
        @endif

        {{-- Form questionario --}}
        <form action="{{ route('owner.assessment.store', $berth) }}" method="POST" enctype="multipart/form-data">
            @csrf

            @foreach ($questions as $category => $categoryQuestions)
                <div class="card card-body mb-6">
                    <h3 class="text-lg font-bold text-slate-900 mb-1">
                        {{ \App\Enums\AssessmentCategory::from($category)->label() }}
                    </h3>
                    <p class="text-sm text-slate-500 mb-4">
                        Peso: {{ \App\Enums\AssessmentCategory::from($category)->weight() * 100 }}%
                    </p>

                    <div class="space-y-6">
                        @foreach ($categoryQuestions as $index => $question)
                            @php
                                $existingAnswer = $assessment?->answers->firstWhere('question_id', $question->id);
                            @endphp
                            <div class="p-4 bg-slate-50 rounded-lg">
                                <label class="block text-sm font-medium text-slate-700 mb-2">
                                    {{ $question->question_text }}
                                    @if ($question->requires_photo)
                                        <span class="text-amber-500 text-xs ml-1">(foto richiesta)</span>
                                    @endif
                                </label>

                                <input type="hidden" name="answers[{{ $loop->parent->index }}_{{ $index }}][question_id]" value="{{ $question->id }}">

                                @if ($question->question_type === 'boolean')
                                    <div class="flex gap-4" x-data="{ val: {{ $existingAnswer?->answer_value ?? 0 }} }">
                                        <label class="flex items-center gap-2 cursor-pointer">
                                            <input type="radio" name="answers[{{ $loop->parent->index }}_{{ $index }}][answer_value]" value="1" x-model.number="val" class="text-ocean-600 focus:ring-ocean-500">
                                            <span class="text-sm">Si</span>
                                        </label>
                                        <label class="flex items-center gap-2 cursor-pointer">
                                            <input type="radio" name="answers[{{ $loop->parent->index }}_{{ $index }}][answer_value]" value="0" x-model.number="val" class="text-ocean-600 focus:ring-ocean-500">
                                            <span class="text-sm">No</span>
                                        </label>
                                    </div>
                                @elseif ($question->question_type === 'scale_1_5')
                                    <div x-data="{ rating: {{ $existingAnswer?->answer_value ?? 3 }} }">
                                        <input type="hidden" name="answers[{{ $loop->parent->index }}_{{ $index }}][answer_value]" :value="rating">
                                        <div class="flex gap-2">
                                            @for ($i = 1; $i <= 5; $i++)
                                                <button type="button" @click="rating = {{ $i }}"
                                                    :class="rating >= {{ $i }} ? 'bg-ocean-500 text-white' : 'bg-slate-200 text-slate-600'"
                                                    class="w-10 h-10 rounded-lg font-bold text-sm transition-colors">
                                                    {{ $i }}
                                                </button>
                                            @endfor
                                        </div>
                                    </div>
                                @else
                                    <select name="answers[{{ $loop->parent->index }}_{{ $index }}][answer_value]" class="form-select mt-1 w-full max-w-xs">
                                        @for ($i = 1; $i <= 10; $i++)
                                            <option value="{{ $i }}" {{ ($existingAnswer?->answer_value ?? 5) == $i ? 'selected' : '' }}>{{ $i }}</option>
                                        @endfor
                                    </select>
                                @endif

                                @if ($question->requires_photo)
                                    <div class="mt-3">
                                        @if ($existingAnswer?->photo_path)
                                            <p class="text-xs text-seafoam-600 mb-1">Foto caricata</p>
                                        @endif
                                        <input type="file" name="photos[{{ $question->id }}]" accept="image/*"
                                            class="text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-ocean-50 file:text-ocean-600 hover:file:bg-ocean-100">
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach

            <div class="flex justify-end">
                <button type="submit" class="btn-primary">
                    {{ $assessment ? 'Aggiorna autovalutazione' : 'Invia autovalutazione' }}
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
