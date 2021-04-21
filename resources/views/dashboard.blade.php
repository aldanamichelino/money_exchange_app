<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
            @php
                if(isset(app('user_account')->id)){
                    $user_boxes = App\Models\SavingBox::where('account_id', app('user_account')->id)->get();
                }

            @endphp

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg flex border:none justify-between items-center">
                <div class="p-6 bg-white">
                    <h1>¡Bienvenid@ a Mercado de Monedas,</h1>
                    <h1>{{auth()->user()->name}}!</h1>
                </div>


               <div style="width: 800px;">
                    @if(!app('user_account'))
                        <div class="p-6 bg-white border-b border-gray-200 flex justify-center border-none">
                            <button type="button" class="bg-green-400 hover:bg-green-800 text-white font-bold py-2 px-4 rounded-full" onclick="window.location='{{Route('createAccount')}}'">Para comenzar, creá tu caja en pesos argentinos</button>
                        </div>
                    @else
                        <div class="p-6 bg-white border-b border-gray-200 flex justify-around border-none">
                            @include('partials.deposit-pesos-modal')
                            <button type="button" class="modal-open bg-green-400 hover:bg-green-800 text-white font-bold py-2 px-4 rounded-full focus:outline-none">Depositar pesos argentinos</button>

                            <button type="button" class="bg-green-400 hover:bg-green-800 text-white font-bold py-2 px-4 rounded-full focus:outline-none" onclick="window.location='{{Route('createSavingBox')}}'">Abrir caja en otra moneda</button>

                            <button class="buy-btn bg-yellow-400 hover:bg-yellow-100 font-bold py-2 px-4 rounded-full focus:outline-none align-middle"><a href="{{Route('showBuyForm')}}">Comprar</a></button>
                            <button class="sell-btn bg-yellow-400 hover:bg-yellow-100 font-bold py-2 px-4 rounded-full focus:outline-none align-middle"><a href="{{Route('showSellForm')}}">Vender</a></button>
                        </div>
                    @endif
               </div>
            </div>



            @if(app('user_account'))
                @php
                    $saving_boxes = App\Models\SavingBox::where('account_id', app('user_account')->id)->get();
                    $saving_box_in_pesos = App\Models\SavingBox::where(['account_id' => app('user_account')->id, 'currency_id' => 1])->first();
                    $one_box = count($user_boxes->toArray()) < 2;

                    $sum = 0;
                    foreach ($saving_boxes as $box) {
                        $sum += $box->balance;
                    }

                    $sum = intval($sum);
                @endphp

                <div class="grid grid-cols-3 gap-4">
                    @foreach($saving_boxes as $saving_box)
                    <div class="overflow-hidden shadow-sm sm:rounded-lg mt-10 w-96">
                        <div class="bg-white p-6 border-2 border-green-400 rounded-3xl">
                            @php $currency = App\Models\Currency::where('id', $saving_box->currency_id)->first();
                                 $last_update = strtotime($saving_box->updated_at);
                                 $last_update = local_date($last_update);
                            @endphp

                            <h1>Cuenta en <span class="font-extrabold">{{$currency->currency_name}}</span></h1>
                            <h3>Saldo: <span class="text-green-400 font-extrabold">{{$currency->currency_symbol}} {{$saving_box->balance}}</span></h3>
                            <h3>Última actualización: {{$last_update}}</h3>

                        </div>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
    <script>
        //Js para manipular modal de sumar pesos
        var openmodal = document.querySelectorAll('.modal-open')
        for (var i = 0; i < openmodal.length; i++) {
        openmodal[i].addEventListener('click', function(event){
            event.preventDefault()
            toggleModal()
        })
        }

        const overlay = document.querySelector('.modal-overlay')
        overlay.addEventListener('click', toggleModal)

        var closemodal = document.querySelectorAll('.modal-close')
        for (var i = 0; i < closemodal.length; i++) {
        closemodal[i].addEventListener('click', toggleModal)
        }

        document.onkeydown = function(evt) {
        evt = evt || window.event
        var isEscape = false
        if ("key" in evt) {
            isEscape = (evt.key === "Escape" || evt.key === "Esc")
        } else {
            isEscape = (evt.keyCode === 27)
        }
        if (isEscape && document.body.classList.contains('modal-active')) {
            toggleModal()
        }
        };


        function toggleModal () {
        const body = document.querySelector('body')
        const modal = document.querySelector('.modal')
        modal.classList.toggle('opacity-0')
        modal.classList.toggle('pointer-events-none')
        body.classList.toggle('modal-active')
        }

        var buy_button = document.querySelector('.buy-btn');
        var sell_button = document.querySelector('.sell-btn');

        buy_button.addEventListener('click', function(){
            if((<?php echo $saving_box_in_pesos->balance ?> == 0) && <?php echo $one_box ?> == true){
                Swal.fire('¡Atención!', 'Para operar, depositá pesos en tu caja y abre una caja en otra moneda', 'error');
                event.preventDefault();
            }
        })

        sell_button.addEventListener('click', function(){
            if((<?php echo $saving_box_in_pesos->balance ?> == 0) && <?php echo $one_box ?> == true){
                Swal.fire('¡Atención!', 'Para operar, depositá pesos en tu caja y abre una caja en otra moneda', 'error');
                event.preventDefault();
            }
        })

    </script>

</x-app-layout>



