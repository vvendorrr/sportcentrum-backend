<x-layouts.app title="Les bewerken">
    <section class="mx-auto max-w-3xl" aria-labelledby="edit-lesson-heading">
        <div class="mb-7">
            <p class="text-sm font-bold uppercase tracking-[0.16em] text-brand-600">Lesbeheer</p>
            <h1 id="edit-lesson-heading" class="mt-1 text-3xl font-black tracking-tight text-brand-950 sm:text-4xl">Les bewerken</h1>
            <p class="mt-2 text-slate-600">Pas de gegevens van {{ $lesson->sport }} aan.</p>
        </div>

        <x-lesson-form
            :lesson="$lesson"
            :instructors="$instructors"
            :action="route('lessons.update', $lesson)"
            http-method="PATCH"
            :cancel-url="route('lessons.show', $lesson)"
            submit-label="Wijzigingen opslaan"
        />
    </section>
</x-layouts.app>
