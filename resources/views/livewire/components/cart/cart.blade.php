<div>

    @if (empty($cart))
        <div class="flex justify-center items-center mt-64">
            <div class="flex items-center flex-col  ">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-16 h-16 text-primary" viewBox="0 0 24 24"
                    fill="currentColor">
                    <path
                        d="M21 4H2v2h2.3l3.28 9a3 3 0 0 0 2.82 2H19v-2h-8.6a1 1 0 0 1-.94-.66L9 13h9.28a2 2 0 0 0 1.92-1.45L22 5.27A1 1 0 0 0 21.27 4 .84.84 0 0 0 21 4zm-2.75 7h-10L6.43 6h13.24z">
                    </path>
                    <circle cx="10.5" cy="19.5" r="1.5"></circle>
                    <circle cx="16.5" cy="19.5" r="1.5"></circle>
                </svg>
                <h2 class="mt-5 font-medium text-3xl text-zinc-600">
                    Seu carrinho está vazio!!</h2>
                <p class="text-base text-zinc-400">Adicione produtos para visualizar seu carrinho!</p>
                <a href="{{ route('admin.index') }}" class="mt-5 btn btn-primary ">Voltar para o Inicio</a>
            </div>
        </div>
    @else
        <div class="container mt-20">
            <div class="row row-cards  ">
                <div class="col-lg-8">
                    <div class=" card   bg-primary  text-primary-fg">
                        <div class="card-body">
                            <h1 class="card-title">
                                Carrinho de compras</h1>
                        </div>
                    </div>

                    <div class=" card  mt-5">
                        <div class="card-body">


                            <div class="table-responsive">

                                <table class="table table-vcenter card-table">
                                    <thead>
                                        <tr>
                                            <th>Nome</th>
                                            <th>Preço</th>
                                            <th>Colaboradores</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            @foreach ($cart as $hash => $item)
                                                @php $product = App\Models\Plan::find($item['product_id']); @endphp
                                                <td>{{ $product->name }}</td>
                                                <td class="text-secondary">
                                                    @price($product->monthly_prices)
                                                </td> 
                                                <td >
                                                    <div   class="flex flex-col gap-1"> 
                                                   
                                                        <div class="flex items-center">
                                                            <button wire:click="removeQuantity"  class="flex h-5 items-center justify-center rounded-l-md border border-neutral-300 bg-neutral-50 px-1 py-1 text-neutral-600 hover:opacity-75 focus-visible:z-10 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-black active:opacity-100 active:outline-offset-0" aria-label="subtract">
                                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true" stroke="currentColor" fill="none" stroke-width="2" class="size-4">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 12h-15"/>
                                                                </svg>
                                                            </button>
                                                            <input wire:model="quantity" id="quantity" name="quantity" type="text" class="border-x-none h-5 w-10 rounded-none border-y border-neutral-300 bg-neutral-50/50 text-center text-neutral-900 focus-visible:z-10 focus-visible:outline focus-visible:outline-2 focus-visible:outline-black" readonly />
                                                            <button  wire:click="addQuantity" class="flex h-5 items-center justify-center rounded-r-md border border-neutral-300 bg-neutral-50 px-1 py-1 text-neutral-600 hover:opacity-75 focus-visible:z-10 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-black active:opacity-100 active:outline-offset-0" aria-label="add">
                                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true" stroke="currentColor" fill="none" stroke-width="2" class="size-4">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                                                                </svg>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </td>
                                            @endforeach
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                        </div>
                    </div>

                </div>

                <div class="col-md-8 col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h1 class="card-title">
                                Resumo do Pedido
                            </h1>
                        </div>
                        <div class="card-body">
                            <table class="w-100">
                                <tbody class="flex flex-col gap-2">
                                    <tr class="flex items-center justify-between mb-1 pb-3 border-b border-solid ">
                                        <td>
                                            <div class="card-title m-0 p-0">
                                                SubTotal: </div>
                                        </td>
                                        <td>

                                            <div class="card-title  m-0 p-0"> @price($totalPrice)</div>
                                        </td>

                                    </tr>
                                    <tr @class([
                                        'flex items-center justify-between ',
                                        'mb-1 pb-3 border-b border-solid  ' => empty($coupon),
                                    ])>
                                        <td>
                                            <div class="card-title m-0 p-0">
                                                Plano:</div>
                                        </td>
                                        <td>

                                            <div class="card-title  m-0 p-0"> @price($getPrice)</div>
                                        </td>

                                    </tr>
                                    <tr @class([
                                        'flex items-center justify-between ',
                                        'mb-1 pb-3 border-b border-solid  ' => empty($coupon),
                                    ])>
                                        <td>
                                            <div class="card-title m-0 p-0">
                                                Colaboradores:</div>
                                        </td>
                                        <td>

                                            <div class="card-title  m-0 p-0">{{ $item['quantity']['number'] }}</div>
                                        </td>

                                    </tr>
                                    @if (!empty($coupon))
                                        <tr class="flex items-center justify-between ">
                                            <td>
                                                <div class="card-title m-0 p-0">
                                                    Desconto: </div>
                                            </td>
                                            <td>

                                                <div class="card-title m-0 p-0"> @price($coupon['value'])</div>

                                            </td>

                                        </tr>
                                        <tr class="border-b border-solid   mb-1 pb-3">
                                            <td class="flex justify-between  items-center w-full">

                                                <button wire:click="removeCoupon"
                                                    class="text-white px-2 py-1 rounded font-medium text-xs bg-[#fa4654]">Remover
                                                    Cupom </button>
                                            </td>
                                        </tr>
                                    @endif

                                </tbody>
                            </table>
                            <div class="flex items-center justify-between w-full mt-4 font-bold mb-2">
                                <div class="font-semibold text-zinc-600 text-lg">

                                </div>
                                @if (empty($coupon))
                                    <div class="flex justify-between w-full gap-1">
                                        <h1 class="font-semibold text-lg">Total:</h1>
                                        <h1 class="font-semibold text-lg"> @price($totalPrice)</h1>
                                    </div>
                                @else
                                    <div class="flex justify-between w-full gap-1">
                                        <h1 class="font-semibold text-lg">Total:</h1>
                                        <h1 class="font-semibold text-lg"> @price($totalPriceCoupon)</h1>
                                    </div>
                                @endif

                            </div>
                            <div class="flex items-center gap-5  ">
                                <a href="{{ route('pay.checkout') }}" class=" z-10 btn btn-primary w-100">
                                    Finalizar Pedido</a>
                            </div>
                        </div>
                    </div>

                    @livewire('coupons.apply-coupon')
                    <div>


                    </div>
                </div>
            </div>
        </div>
</div>
@endif
</div>
