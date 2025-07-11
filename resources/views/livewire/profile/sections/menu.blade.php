<div>
    <div class="card-body">
        <h4 class="subheader">Configurações de negócios</h4>

        <div class="list-group list-group-transparent">
            <a href="{{ route('user.user') }}"
                class="list-group-item list-group-item-action d-flex align-items-center  ">Perfil</a> 
            <a href="{{ route('user.business') }}"
                class="list-group-item list-group-item-action d-flex align-items-center  ">Minha Empresa</a> 
                <h4 class="subheader mt-4 ml-5">Minha Assinatura</h4>
            <a href="{{ route('user.signature') }}"
                class="list-group-item list-group-item-action d-flex align-items-center">Assinatura</a>
            <a href="{{ route('user.invoices') }}"
                class="list-group-item list-group-item-action d-flex align-items-center">Pedidos
            </a>
        </div>
    </div>
</div>
