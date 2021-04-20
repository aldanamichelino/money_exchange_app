<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Vender divisas
        </h2>
    </x-slot>

        <form action="" method="post" enctype="multipart/form-data">
            {{ csrf_field() }}

            <div class="flex border-none justify-center mb-20">
                <div class="mt-10 w-2/4 p-20 bg-green-200 rounded-br-3xl">
                    <div class="flex flex-col justify-around border-none" style="height: 90%;">

                            <input type="hidden" name="origin_sb" id="origin_sb" value="{{$box->id}}">

                            <label for="originCurrency" class="font-extrabold text-center mb-4">Vender {{$originCurrency}}</label>
                            <input type="hidden" name="originCurrency" id="originCurrency" value="{{$originCurrency_code}}">

                            <div class="flex border-none mb-4">
                                <label for="balance">Saldo: &nbsp</label>
                                <h1 class="text-gray-600 font-bold">{{number_format($balance, 2, ',', '')}}</h1>
                            </div>
                            <input type="hidden" name="balance" id="balance" value="{{number_format($balance, 2, ',', '')}}">

                            <label for="targetAmount">¿Cuántos {{$originCurrency}} querés vender?</label>
                            <input type="text" name="targetAmount" id="targetAmount" value="" placeholder="Usá puntos o comillas solo para decimales.">

                            <label for="currency">¿A qué moneda querés vender?</label>
                            <select name="currency" class="mb-4" id="selected_currency">
                                <option selected value="">Elegí una moneda</option>
                                @foreach($list_of_currencies as $currency_item)
                                    @if($currency_item->currency_id != $currency_id)
                                        <option value="{{$currency_item->currency_code}}">{{$currency_item->currency_name}} {{$currency_item->currency_code}}</option>
                                    @endif
                                @endforeach
                            </select>

                            <!-- <div class="flex border-none mb-4">
                                <label for="rateExchange" class="text-sm">Última cotización en {{$originCurrency}}: &nbsp</label>
                                <h1 id="rateExchange" class="text-gray-600 font-bold"></h1>
                            </div>
                            <input type="hidden" name="rateExchange" id="rateExchange" value=""> -->
                    </div>

                    <div class="flex justify-end pt-2">
                        <button type="submit" class="submit-button px-4 bg-transparent p-3 rounded-lg text-black hover:bg-gray-100 hover:text-green-400 mr-2 focus:outline-none">Vender</button>
                        <button type="button" class="px-4 bg-black p-3 rounded-lg text-white hover:bg-white hover:text-black focus:outline-none" onclick="window.location='{{Route('dashboard')}}'">Volver</button>
                    </div>
                </div>
            </div>
        </form>

    <script>

    </script>

</x-app-layout>
