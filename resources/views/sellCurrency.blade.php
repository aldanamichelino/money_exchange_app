<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Vender divisas
        </h2>
    </x-slot>

        <form action="{{ Route('sellCurrency')}}" method="post" enctype="multipart/form-data">
            {{ csrf_field() }}

            <div class="flex border-none justify-center mb-20">
                <div class="mt-10 w-2/4 p-20 bg-green-200 rounded-br-3xl">
                    <div class="flex flex-col justify-around border-none" style="height: 90%;">

                            <label for="currency">¿Qué moneda querés vender?</label>
                            <select id="originCurrency" name="currency" class="mb-4" id="selected_currency">
                                <option selected value="">Elegí una moneda</option>
                                @foreach($list_of_currencies as $currency_item)
                                    <option value="{{$currency_item->currency_id}}">{{$currency_item->currency_name}} {{$currency_item->currency_code}}</option>
                                @endforeach
                            </select>

                            <div class="flex border-none">
                                <label for="balance">Saldo: &nbsp;<h3 id="balance"></h3></label>
                            </div>
                                <input type="hidden" name="balance" value="" id="balance_input" class="mb-4">

                            <label for="targetAmount">Cantidad</label>
                            <input type="text" name="targetAmount" id="targetAmount" value="" class="mb-4" placeholder="Usá puntos o comillas solo para decimales.">

                            <label for="buying_currency">¿Por qué moneda lo vas a cambiar?</label>
                            <select id="targetCurrency" name="buying_currency" class="mb-4" id="selected_currency">
                                <option selected value="">Elegí una moneda</option>
                                @foreach($list_of_currencies as $currency_item)
                                    <option value="{{$currency_item->currency_id}}">{{$currency_item->currency_name}} {{$currency_item->currency_code}}</option>
                                @endforeach
                            </select>


                    </div>

                    <div class="flex justify-end pt-2">
                        <button type="submit" class="submit-button px-4 bg-transparent p-3 rounded-lg text-black hover:bg-gray-100 hover:text-green-400 mr-2 focus:outline-none">Vender</button>
                        <button type="button" class="px-4 bg-black p-3 rounded-lg text-white hover:bg-white hover:text-black focus:outline-none" onclick="window.location='{{Route('dashboard')}}'">Volver</button>
                    </div>
                </div>
            </div>
        </form>

    <script>

        var originCurrency = document.getElementById('targetCurrency');
        var targetCurrency = document.getElementById('originCurrency');
        var balance = document.getElementById('balance');
        var balance_input = document.getElementById('balance_input');
        var buying_currency = document.getElementById('buying_currency');
        var target_amount = document.getElementById('targetAmount');

        originCurrency.addEventListener('change', function(){
            if(targetCurrency != null && originCurrency.value == targetCurrency.value){
                Swal.fire('¡Atención!', 'La moneda origen y la moneda meta no pueden ser iguales', 'error');
            }
        });

        targetCurrency.addEventListener('change', function(){

            <?php

                $saving_boxes = App\Models\SavingBox::where(['account_id' => app('user_account')->id])->get();
                $currency_array = [];
                foreach($saving_boxes as $boxes){
                    $currency_array[$boxes->currency_id] = $boxes->balance;
                }

            ?>

           var boxes = <?php echo json_encode($currency_array); ?>

            Object.keys(boxes).forEach(function(key){
                if(key == targetCurrency.value){
                    balance.innerHTML = boxes[key];
                    balance_input.value = boxes[key];
                }
            });


            if(originCurrency != null && originCurrency.value == targetCurrency.value){
                Swal.fire('¡Atención!', 'La moneda origen y la moneda meta no pueden ser iguales', 'error');
            }
        });

        target_amount.addEventListener('focusout', function(){

            if(parseInt(target_amount.value) > parseInt(balance_input.value)){
                Swal.fire('¡Atención!', 'La cantidad a vender no puede ser mayor que el saldo.', 'error');
            }

        })

    </script>

</x-app-layout>

