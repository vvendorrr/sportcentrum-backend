<x-layouts.app title="Les aanmaken">
    <section class="mx-auto max-w-3xl" aria-labelledby="create-lesson-heading">
        <div class="mb-7">
            <p class="text-sm font-bold uppercase tracking-[0.16em] text-brand-600">Lesbeheer</p>
            <h1 id="create-lesson-heading" class="mt-1 text-3xl font-black tracking-tight text-brand-950 sm:text-4xl">Nieuwe les aanmaken</h1>
            <p class="mt-2 text-slate-600">Vul de lesgegevens in om deze aan het rooster toe te voegen.</p>
        </div>

        <x-lesson-form
            :instructors="$instructors"
            :action="route('lessons.store')"
            :cancel-url="route('lessons.index')"
            submit-label="Les opslaan"
        />
    </section>
</x-layouts.app>
