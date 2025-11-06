<div>
    <flux:main container="">
        <div class="flex flex-row items-center w-full justify-between mb-6">
            <div>
                <flux:heading size="xl">Criar Loja</flux:heading>
                <flux:text class="mt-2 text-base">Cadastre uma nova loja</flux:text>
            </div>

            <flux:button variant="ghost" icon="arrow-left" :href="route('stores.index')" wire:navigate>
                Voltar
            </flux:button>
        </div>

        <x-section>
            <form wire:submit="save" class="space-y-6">
                <div class="grid lg:grid-cols-2 gap-6">
                    <flux:field>
                        <flux:input
                            label="Nome"
                            placeholder="Nome da loja"
                            wire:model.blur="form.name"
                            wire:change="form.generateSlug"
                            required
                        />
                    </flux:field>

                    <flux:field>
                        <flux:input
                            label="Slug"
                            placeholder="slug-da-loja"
                            wire:model="form.slug"
                            required
                        />
                    </flux:field>
                </div>

                <div class="grid lg:grid-cols-2 gap-6">
                    <flux:field>
                        <flux:input
                            label="Website"
                            placeholder="https://example.com"
                            type="url"
                            wire:model="form.website"
                            required
                        />
                    </flux:field>

                    <flux:field>
                        <flux:input
                            label="Telefone"
                            placeholder="(00) 00000-0000"
                            type="tel"
                            wire:model="form.phone"
                            required
                        />
                    </flux:field>
                </div>

                <flux:field>
                    <flux:textarea
                        label="Horário de Funcionamento (JSON)"
                        placeholder='{"monday": "09:00-18:00", "tuesday": "09:00-18:00"}'
                        wire:model="form.opening_hours_json"
                        rows="6"
                    />
                    <flux:text class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                        Opcional. Formato JSON válido.
                    </flux:text>
                </flux:field>

                <div class="flex items-center justify-end gap-4">
                    <flux:button variant="ghost" :href="route('stores.index')" wire:navigate>
                        Cancelar
                    </flux:button>
                    <flux:button variant="primary" type="submit" icon="check">
                        Criar Loja
                    </flux:button>
                </div>
            </form>
        </x-section>
    </flux:main>
</div>
