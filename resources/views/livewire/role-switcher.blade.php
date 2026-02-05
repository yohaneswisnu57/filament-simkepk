<div class="flex items-center gap-2 mr-4">
    {{-- Label (Opsional, gunakan class bawaan Filament agar rapi) --}}
    <span class="text-sm font-medium text-gray-500 dark:text-gray-400 hidden sm:block">
        Role:
    </span>

    {{-- Gunakan Wrapper dan Select bawaan Filament --}}
    <div class="w-40"> {{-- Atur lebar sesuai kebutuhan --}}
        <x-filament::input.wrapper>
            <x-filament::input.select wire:model.live="currentRole">
                @foreach($options as $value => $label)
                    <option value="{{ $value }}">
                        {{ $label }}
                    </option>
                @endforeach
            </x-filament::input.select>
        </x-filament::input.wrapper>
    </div>
</div>
