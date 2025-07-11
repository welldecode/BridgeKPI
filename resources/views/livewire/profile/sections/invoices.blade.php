<div>
    <div class="page-body">

        <div class="container-xl">
            <div class="card">
                <form wire:submit.prevent="saveUser">
                    <div class="row g-0">
                        <div class="col-12 col-md-3 border-end">
                            @includeWhen('1', 'livewire.profile.sections.menu')
                        </div>
                        <div class="col-12 col-md-9 d-flex flex-column">
                            <div class="card-body">
                                <h2 class="mb-4">Meus Pedidos</h2>

                                <h3 class="card-title">Lista de todos pedidos</h3>
                                <div class="table-responsive">
                                    <table class="table table-vcenter card-table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Numero do Pedido</th>
                                                <th>Preço</th>
                                                <th>Status</th>
                                                <th>Metodo</th>
                                                <th class="w-1"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($orders as $order)
                                                <tr> 
                                                    <td>{{ $order->order_number }}</td>
                                                    <td>@price($order->sub_total)</td>
                                                    <td>{{ __($order->payment_status) }}</td>
                                                    <td>{{ __(ucfirst($order->payment_method)) }}</td>
                                                    <td> 
                                                        <a href="{{route('user.invoiceview', $order->order_number)}}">Visualizar</a> 
                                                    </td> 
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                            </div>
                        </div>

                    </div>
                </form>
            </div>
        </div>

    </div>
</div>
