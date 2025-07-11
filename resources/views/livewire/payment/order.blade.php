<div>
    <div class="container container-tight py-20">
        <div class="text-center mb-4">
            <a href="#" class="navbar-brand navbar-brand-autodark"><img
                    src="https://i.imgur.com/hgXV0Wu.png" height="70" width="200" alt=""></a>
        </div>
        <div class="card card-md">

            <div id="statusScreenBrick_container"></div>
            <input type="hidden" id="payment_Id" value="{{ $order->payment_order }}" />

        </div>
    </div>
    <script src="/assets/libs/jquery/jquery.min.js"></script>
    <script>
        const mp = new MercadoPago('TEST-c8fdf9ec-ce5e-410f-ab7d-716fe8595c97', {
            locale: 'pt'
        });
        const bricksBuilder = mp.bricks();
        const renderStatusScreenBrick = async (bricksBuilder) => {
            const settings = {
                initialization: {
                    paymentId: $("#payment_Id").val(), // id do pagamento a ser mostrado
                },
                customization: {
                    visual: { 
                        showExternalReference: true,
                        style: {
                            theme: 'default', // 'default' | 'dark' | 'bootstrap' | 'flat'
                        },
                    },
                    backUrls: {
                        'error': 'https://bridges.devstep.com.br/',
                        'return': 'https://bridges.devstep.com.br/dashboard'
                    }
                },
                callbacks: {
                    onReady: () => {
                        // Callback called when Brick is ready
                    },
                    onError: (error) => {
                        // callback chamado para todos os casos de erro do Brick
                        console.error(error);
                    },
                },
            };
            window.statusScreenBrickController = await bricksBuilder.create('statusScreen',
                'statusScreenBrick_container', settings);
        };
        renderStatusScreenBrick(bricksBuilder);
    </script>
</div>
