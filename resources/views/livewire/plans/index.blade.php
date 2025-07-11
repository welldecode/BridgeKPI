<div>
    <div class="page-wrapper ">
        <!-- Page header -->
        <div class="page-header d-print-none">
          <div class="container-xl">
            <div class="row g-2 align-items-center">
              <div class="col">
                <h2 class="page-title">
             Tabela de Planos
                </h2>
              </div>
            </div>
          </div>
        </div>
        <!-- Page body -->
        <div class="page-body">
          <div class="container-xl">
            <div class="card">
              <div class="table-responsive">
                <table class="table table-vcenter table-bordered table-nowrap card-table">
                  <thead>
                    <tr>
                      <td class="w-50">
                        <h2>Planos para equipes de todos os tipos</h2>
                        <div class="text-secondary text-wrap">
                        Escolha um plano acessível que venha com os melhores recursos para envolver sua empresa e aumentar as vendas.
                        </div>
                      </td>
                      @foreach ($plans as $plan_items)
                      <td class="text-center">
                        <div class="text-uppercase text-secondary font-weight-medium">{{ $plan_items->name}}</div>
                        <div class="display-6 fw-bold my-3"> @price($plan_items->monthly_prices)</div>
                        <a href="{{ route('register', $plan_items->id) }}" class="btn w-100">Escolher
                            Plano</a> 
                      </td>
                      
                      @endforeach
                    </tr>
                  </thead>
                  <tbody>
                    <tr class="bg-light">
                      <th colspan="4" class="subheader text-xl">Recursos</th>
                    </tr>
                    <tr>
                      <td>Todas as Funções</td>
                      <td class="text-center"><!-- Download SVG icon from http://tabler.io/icons/icon/check -->
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon text-green"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10" /></svg>
                      </td>
                      <td class="text-center"><!-- Download SVG icon from http://tabler.io/icons/icon/check -->
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon text-green"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10" /></svg>
                      </td>
                      <td class="text-center"><!-- Download SVG icon from http://tabler.io/icons/icon/check -->
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon text-green"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10" /></svg>
                      </td>
                    </tr>
                    <tr>
                      <td>Colaboradores</td>
                      <td class="text-left"><!-- Download SVG icon from http://tabler.io/icons/icon/x -->
                        1
                    </td>
                      <td class="text-left"><!-- Download SVG icon from http://tabler.io/icons/icon/check -->
                    2 
                    </td>
                      <td class="text-left"><!-- Download SVG icon from http://tabler.io/icons/icon/check -->
                    4
                    </td>
                    </tr>
                    <tr>
                      <td>Suporte</td>
                      <td class="text-center"><!-- Download SVG icon from http://tabler.io/icons/icon/x -->
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon text-red"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M18 6l-12 12" /><path d="M6 6l12 12" /></svg>
                      </td>
                      <td class="text-center"><!-- Download SVG icon from http://tabler.io/icons/icon/x -->
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon text-green"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10" /></svg>
                    </td>
                      <td class="text-center"><!-- Download SVG icon from http://tabler.io/icons/icon/check -->
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon text-green"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10" /></svg>
                      </td>
                    </tr> 
                  </tbody>
                  <tfoot>
                    <tr>
                      <td></td>
                      <td>
                        <a href="{{ route('register', 1) }}" class="btn w-100">Escolher
                            Plano</a>
                      
                      </td>
                      <td>
                        <a href="{{ route('register', 2) }}" class="btn btn-green  w-100">Escolher
                            Plano</a>
                      </td>
                      <td>
                        <a href="{{ route('register', 3) }}" class="btn w-100">Escolher
                            Plano</a>
                      </td>
                    </tr>
                  </tfoot>
                </table>
              </div>
            </div>
          </div>
        </div>
 
      <a href="#" wire:click="addToCart(1)">Cart</a>