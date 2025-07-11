<div> 
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        Pedido
                    </h2>
                </div>
                    <!-- Page title actions -->
                    <div class="col-auto ms-auto d-print-none">
                        <button type="button" class="btn btn-primary" onclick="javascript:window.print();">
        <!-- Download SVG icon from http://tabler.io/icons/icon/printer -->
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-1"><path d="M17 17h2a2 2 0 0 0 2 -2v-4a2 2 0 0 0 -2 -2h-14a2 2 0 0 0 -2 2v4a2 2 0 0 0 2 2h2"></path><path d="M17 9v-4a2 2 0 0 0 -2 -2h-6a2 2 0 0 0 -2 2v4"></path><path d="M7 13m0 2a2 2 0 0 1 2 -2h6a2 2 0 0 1 2 2v4a2 2 0 0 1 -2 2h-6a2 2 0 0 1 -2 -2z"></path></svg>
     Baixar Pedido
    </button>
                    </div>
            </div>
        </div>
    </div>
    <div class="page-body">
        <div class="container-xl">
            <div class="card card-lg">
                <div class="card-body">
                    <div class="row">
                       
                        <div class="col-12 my-5">
                            <h1>Pedido  {{$id}}</h1>
                        </div>
                    </div>
                    <table class="table table-transparent table-responsive">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 1%"></th>
                                <th>Produto</th>
                                <th class="text-center" style="width: 1%">Quantidade</th>
                                <th class="text-end" style="width: 1%">Colaboradores</th>
                                <th class="text-end" style="width: 1%">Preço</th>
                            </tr>
                        </thead>
                        <tbody>
                        
                            <tr>
                                <td class="text-center">{{$plan->id}}</td>
                                <td>
                                    <p class="strong mb-1">{{$plan->name}}</p>
                                    <div class="text-secondary">{{$plan->description}}</div>
                                </td>
                                <td class="text-center">
                                    1
                                </td>
                                <td class="text-end">1</td>
                                <td class="text-end">@price($orders->total_amount)</td>
                            </tr>
                            <tr>
                                <td colspan="4" class="strong text-end">Subtotal</td>
                                <td class="text-end">@price($orders->sub_total)</td>
                            </tr> 
                        </tbody>
                    </table>
                    <p class="text-secondary text-center mt-5">Muito obrigado por fazer negócios conosco. Estamos ansiosos para trabalhar com você novamente!</p>
                </div>
            </div>
        </div>
    </div>
</div>
