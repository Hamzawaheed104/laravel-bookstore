@extends('layouts.app')

@section('content')
    <h1 class="mb-10 text-2xl">Order Confirmation</h1>
    <table class="table-auto w-full">
        <thead>
            <tr>
                <th class="px-4 py-2">Book Name</th>
                <th class="px-4 py-2">Quantity</th>
                <th class="px-4 py-2">Item Total Price</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($cartItems as $cartItem)
            <tr>
                <td class="border px-4 py-2">{{ $cartItem->book->title }}</td>
                <td class="border px-4 py-2">{{ $cartItem->quantity }}</td>
                <td class="border px-4 py-2">${{ $cartItem->item_total_price }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="p-4 mt-10 bg-white shadow rounded-lg">
        <div>Total Items: <span class="ml-2 text-green-700">{{ count($cartItems) }}</span></div>
        <div>Total Amount: <span class="ml-2 text-green-700">${{ $total }}</span></div>
    </div>

    <h1 class="mt-8 text-2xl">Shipping Address</h1>
    <div class="p-4 mt-2 bg-white shadow rounded-lg">
        <div class="flex flex-col gap-4 mt-4">
            @foreach ($addresses as $index => $address)
            <label class="block p-4 border border-gray-200 rounded-lg hover:bg-gray-50">
                <input type="radio" name="shippingAddress" value="existing-{{ $address->id }}" class="text-blue-600 focus:ring-blue-500 focus:ring-offset-2 ring-offset-white w-4 h-4" onchange="toggleAddressSelection('existing', {{ $address->id }})">
                <span class="ml-2 text-sm text-gray-700">
                    {{ $address->street }}, {{ $address->city }}, {{ $address->region }}, {{ $address->country }}, {{ $address->zip }}
                </span>
            </label>
            @endforeach
            <label class="block p-4 border border-gray-200 rounded-lg hover:bg-gray-50">
                <input type="radio" name="shippingAddress" value="new" class="text-blue-600 focus:ring-blue-500 focus:ring-offset-2 ring-offset-white w-4 h-4" onchange="toggleAddressSelection('new')" checked>
                <span class="ml-2 text-sm text-gray-700">Add New Address</span>
            </label>
        </div>
    </div>

    <form id="orderForm" action="{{ route('payment.shipping') }}" method="POST">
        @csrf
        <div class="mt-2" id="addressForm">
            <input type="hidden" id="selectedAddressId" name="selectedAddressId">
            <input type="hidden" id="latitude" name="latitude">
            <input type="hidden" id="longitude" name="longitude">
            
            <label for="autocomplete" class="block">Address Search:</label>
            <input id="autocomplete" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" type="text" placeholder="Start typing your address...">
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                <div>
                    <label for="street" class="block">Street:</label>
                    <input type="text" id="street" name="street" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="{{ old('street') }}">
                    <p id="street-error" class="text-red-500 text-xs">
                        @if ($errors->has('street'))
                            <span>{{ $errors->first('street') }}</span>
                        @endif
                    </p>
                </div>
                <div>
                    <label for="city" class="block">City:</label>
                    <input type="text" id="city" name="city" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="{{ old('city') }}">
                    <p id="city-error" class="text-red-500 text-xs">
                        @if ($errors->has('city'))
                            <span>{{ $errors->first('city') }}</span>
                        @endif
                    </p>
                </div>
                <div>
                    <label for="region" class="block">Region:</label>
                    <input type="text" id="region" name="region" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="{{ old('region') }}">
                    <p id="region-error" class="text-red-500 text-xs">
                        @if ($errors->has('region'))
                            <span>{{ $errors->first('region') }}</span>
                        @endif
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                <div>
                    <label for="country" class="block">Country:</label>
                    <input type="text" id="country" name="country" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"  value="{{ old('country') }}" />
                    <p id="country-error" class="text-red-500 text-xs">
                        @if ($errors->has('country'))
                            <span>{{ $errors->first('country') }}</span>
                        @endif
                    </p>
                </div>
                <div>
                    <label for="zip" class="block">Zip:</label>
                    <input type="text" id="zip" name="zip" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="{{ old('zip') }}" pattern="\d{1,6}" title="ZIP code must be 1 to 6 digits long">
                    <p id="zip-error" class="text-red-500 text-xs">
                        @if ($errors->has('zip'))
                            <span>{{ $errors->first('zip') }}</span>
                        @endif
                    </p>
                </div>
                <div>
                    <label for="contact_number" class="block">Contact Number:</label>
                    <input type="text" id="contact_number" name="contact_number" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="{{ old('contact_number') }}" pattern="\d{1,15}" title="Contact number must be 1 to 15 digits long">
                    <p id="contact-error" class="text-red-500 text-xs">
                        @if ($errors->has('contact_number'))
                            <span>{{ $errors->first('contact_number') }}</span>
                        @endif
                    </p>
                </div>
            </div>
            
            <div id="map" class="mt-4" style="height: 400px;"></div>
        </div>
        <div class="mt-4">
            <button type="button" onclick="history.back();" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 mr-2 mb-2">Back</button>
            <button type="submit" id="continueButton" class="focus:outline-none text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 mr-2 mb-2">Continue</button>
        </div>
    </form>
@endsection

@section('footerScripts')
    <script src="{{ asset('js/shipping.js') }}"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&libraries=places&callback=initMap" async defer></script>
@endsection