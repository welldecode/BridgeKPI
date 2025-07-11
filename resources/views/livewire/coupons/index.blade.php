<div>
    @if (session()->has('message'))
        <div style="color: green;">{{ session('message') }}</div>
    @endif

    <form wire:submit.prevent="{{ $editId ? 'update' : 'save' }}">
        <input type="text" wire:model="code" placeholder="Código">
        <input type="number" wire:model="discount" placeholder="Desconto" step="0.01">
        <select wire:model="type">
            <option value="">Tipo</option>
            <option value="fixed">Fixo</option>
            <option value="percentage">Porcentagem</option>
        </select>
        <input type="datetime-local" wire:model="valid_from">
        <input type="datetime-local" wire:model="valid_to">
        <input type="number" wire:model="usage_limit" placeholder="Limite de Uso">
        <button type="submit">{{ $editId ? 'Atualizar' : 'Salvar' }}</button>
    </form>

    <hr>

    <ul>
        @foreach ($coupons as $coupon)
            <li>
                {{ $coupon->code }} - {{ $coupon->discount }} ({{ $coupon->type }})
                <button wire:click="edit({{ $coupon->id }})">Editar</button>
                <button wire:click="delete({{ $coupon->id }})">Excluir</button>
            </li>
        @endforeach
    </ul>
</div>
