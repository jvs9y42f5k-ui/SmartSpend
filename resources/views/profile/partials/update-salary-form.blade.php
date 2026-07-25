<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Monthly Salary') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('Set your salary amount so it can be filled in automatically when you add a "Salary" transaction.') }}
        </p>
    </header>

    <form method="post" action="{{ route('profile.salary.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="salary" :value="__('Salary Amount ($)')" />
            <x-text-input id="salary" name="salary" type="number" step="0.01" min="0" class="mt-1 block w-full" :value="old('salary', $user->salary)" placeholder="0.00" />
            <x-input-error class="mt-2" :messages="$errors->get('salary')" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'salary-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>