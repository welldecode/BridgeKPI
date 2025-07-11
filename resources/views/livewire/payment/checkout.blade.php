<div>
    <div class="container mt-20">
        <div class="row row-cards  ">
            <div class="col-lg-8">
                <div class=" card  ">
                    <div class="card-body">
                        <div class="w-full mb-5">
                            <h1 class="font-bold text-xl text-zinc-800 text-primary ">Método de Pagamento</h1>
                            <p>{{ __('Escolha o seu metodo de pagamento') }}</p>
                        </div> 
                        <div id="paymentBrick_container"></div>
                    </div>
                </div> 
            </div>
            <div class="col-md-8 col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h1 class="card-title">
                            Resumo de compra
                        </h1>
                    </div>
                    <div class="card-body d-flex flex-col gap-2">

                        @foreach ($cart as $hash => $item)
                            @php $product = App\Models\Plan::find($item['product_id']); @endphp

                            <div class="card-title">{{ $product->name }}</div>

                            <div class="mb-2 d-flex">
                          
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round" class="icon me-2 text-secondary">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <path d="M3 19a9 9 0 0 1 9 0a9 9 0 0 1 9 0"></path>
                                    <path d="M3 6a9 9 0 0 1 9 0a9 9 0 0 1 9 0"></path>
                                    <path d="M3 6l0 13"></path>
                                    <path d="M12 6l0 13"></path>
                                    <path d="M21 6l0 13"></path>
                                </svg>
                                <div> Total de Colaboradores: <strong> </strong></div>
                            </div>
                            <div class="mb-2 d-flex">
                                <!-- Download SVG icon from http://tabler-icons.io/i/briefcase -->
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round" class="icon me-2 text-secondary">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <path
                                        d="M3 7m0 2a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v9a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2z">
                                    </path>
                                    <path d="M8 7v-2a2 2 0 0 1 2 -2h4a2 2 0 0 1 2 2v2"></path>
                                    <path d="M12 12l0 .01"></path>
                                    <path d="M3 13a20 20 0 0 0 18 0"></path>
                                </svg>
                                <div>Total de Empresas: <strong>1</strong></div>
                            </div>
                            <div class="mb-2 d-flex">
                                <!-- Download SVG icon from http://tabler-icons.io/i/home -->
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round" class="icon me-2 text-secondary">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <path d="M5 12l-2 0l9 -9l9 9l-2 0"></path>
                                    <path d="M5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-7"></path>
                                    <path d="M9 21v-6a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v6"></path>
                                </svg>
                                <div> Funções: <strong>Acesso Ilimitado</strong></div>
                            </div>

                            <div class="mb-2 d-flex">
                                <!-- Download SVG icon from http://tabler-icons.io/i/clock -->
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round" class="icon me-2 text-secondary">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0"></path>
                                    <path d="M12 7v5l3 3"></path>
                                </svg>
                                <div> Tempo: <strong>30 dias</strong></div>
                            </div>
                        @endforeach

                    </div>
                    <div class="card-footer">
                        <div class="d-flex justify-content-between w-100">
                            <div class="font-semibold text-lg">
                                Total:
                            </div>
                            <input type="hidden" id="amount" value="{{ $totalPrice }}" />
                            <h1 class="font-semibold text-lg"> @price($totalPrice)</h1>
                        </div>
                    </div>
                </div>
                <div>


                </div>
            </div>
        </div>
    </div>
</div>
<script src="/assets/libs/jquery/jquery.min.js"></script> 
<script>
    const mp = new MercadoPago('TEST-c8fdf9ec-ce5e-410f-ab7d-716fe8595c97', {
        locale: 'pt'
    });
    const bricksBuilder = mp.bricks();
    const renderPaymentBrick = async (bricksBuilder) => {
        const settings = {
            initialization: {
                /*
                  "amount" é a quantia total a pagar por todos os meios de pagamento com exceção da Conta Mercado Pago e Parcelas sem cartão de crédito, que têm seus valores de processamento determinados no backend através do "preferenceId"
                */
                amount: ($("#amount").val()),
            },
            customization: {
                visual: {
                    hideFormTitle: true,
                    style: {
                        theme: "bootstrap",
                    },
                },
                paymentMethods: { 

                    
       ticket: "all",
       bankTransfer: "all",
       creditCard: "all",
       debitCard: "all",
       mercadoPago: "all", 
                },
            },
            callbacks: {
                onReady: () => {
                    /*
                     Callback chamado quando o Brick está pronto.
                     Aqui, você pode ocultar seu site, por exemplo.
                    */
                },
                onSubmit: ({
                    selectedPaymentMethod,
                    formData
                }) => {


                    $.ajax({
                        url: '/payments/process_payment',
                        method: 'POST',
                        enctype: 'multipart/form-data',
                        data: JSON.stringify(formData),
                        processData: false,
                        contentType: false,
                        dataType: 'json',
                        headers: {
                            "Content-Type": "application/json",
                            'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
                        },
                        success: function(response) {
                            const redirect = response.redirect;
                            const type = response.payment_method;

                            window.location.href = "/pay/order/" + redirect + "/" + type;

                            console.log(response);
                        },
                        error: function(response) {
                            console.log(response);
                        }
                    })

                },
                onError: (error) => {
                    // callback chamado para todos os casos de erro do Brick
                    console.error(error);
                },
            },
        };
        window.paymentBrickController = await bricksBuilder.create(
            "payment",
            "paymentBrick_container",
            settings
        );
    };
    renderPaymentBrick(bricksBuilder);
</script>
</div>
