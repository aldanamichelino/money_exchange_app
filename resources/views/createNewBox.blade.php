<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Creá una nueva caja
        </h2>
    </x-slot>

    @php $account = App\Models\Account::where(['user_id' => auth()->id()])->first(); @endphp
        <form action="{{ route('storeSavingBox') }}" method="post" enctype="multipart/form-data">
            {{ csrf_field() }}

            <div class="flex border-none justify-center">
                <div class="mt-10 w-1/3 p-20 bg-green-200 rounded-br-3xl">
                    <div class="flex flex-col justify-around border-none" style="height: 90%;">
                            <label for="currency">Elegí una moneda</label>
                            @php $currencies = App\Models\Currency::all(); @endphp
                            <select name="currency">
                                @foreach($currencies as $currency)
                                    <option value="{{$currency->id}}">{{$currency->currency_name}} {{$currency->currency_code}}</option>
                                @endforeach
                            </select>

                            <div class="flex border-none">
                                <label for="account">Cuenta: &nbsp</label>
                                <h1>{{$account->id}}</h1>
                            </div>
                            <input type="text" name="account" id="account" value="{{$account->id}}" class="hidden">
                    </div>

                    <div class="flex justify-end pt-2">
                        <button type="submit" class="submit-button px-4 bg-transparent p-3 rounded-lg text-black hover:bg-gray-100 hover:text-green-400 mr-2 focus:outline-none">Guardar</button>
                        <button type="button" class="px-4 bg-black p-3 rounded-lg text-white hover:bg-white hover:text-black focus:outline-none" onclick="window.location='{{Route('dashboard')}}'">Volver</button>
                    </div>
                </div>
            </div>
        </form>

</x-app-layout>
