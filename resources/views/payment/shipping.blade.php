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
        <div class="book-title">Total Items: <span class="ml-2 text-green-700">{{ count($cartItems) }}</span></div>
        <div class="book-title">Total Amount: <span class="ml-2 text-green-700">${{ $total }}</span></div>
        
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
    <div class="mt-2">
        <div id="addressForm" class="mt-4">
            <label for="autocomplete" class="block">Address Search:</label>
            <input id="autocomplete" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" type="text" placeholder="Start typing your address...">
    
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="street" class="block">Street:</label>
                    <input type="text" id="street" name="street" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>
                <div>
                    <label for="city" class="block">City:</label>
                    <input type="text" id="city" name="city" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>
            </div>
        
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="region" class="block">Region:</label>
                    <input type="text" id="region" name="region" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>
                <div>
                    <label for="country" class="block">Country:</label>
                    <input type="text" id="country" name="country" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>
                <div>
                    <label for="zip" class="block">Zip:</label>
                    <input type="text" id="zip" name="zip" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>
            </div>

            <div id="map" class="mt-2" style="height: 400px;"></div>
        </div>
    </div>

    <form id="orderForm" action="{{ route('payment.checkout') }}" method="POST" class="mt-5">
        @csrf
        <button type="button" onclick="history.back();" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 mr-2 mb-2">Back</button>
        <button type="submit" id="confirmOrderButton" class="focus:outline-none text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 mr-2 mb-2" disabled>Confirm Order</button>
    </form>

    <script>
        function initMap() {
            const center = { lat: -34.397, lng: 150.644 };
            const map = new google.maps.Map(document.getElementById("map"), {
                zoom: 8,
                center: center,
            });
            const marker = new google.maps.Marker({
                position: center,
                map: map,
                draggable: true
            });

            const autocomplete = new google.maps.places.Autocomplete(document.getElementById("autocomplete"));
            autocomplete.bindTo("bounds", map);
            autocomplete.setFields(["address_components", "geometry", "icon", "name"]);

            autocomplete.addListener("place_changed", () => {
                const place = autocomplete.getPlace();
                if (!place.geometry) {
                    window.alert("No details available for input: '" + place.name + "'");
                    return;
                }

                map.setCenter(place.geometry.location);
                marker.setPosition(place.geometry.location);

                document.getElementById("latitude").value = place.geometry.location.lat();
                document.getElementById("longitude").value = place.geometry.location.lng();

                fillInAddress(place);
            });

            marker.addListener("dragend", () => {
                const latLng = marker.getPosition();
                map.panTo(latLng);

                document.getElementById("latitude").value = latLng.lat();
                document.getElementById("longitude").value = latLng.lng();
            });
        }

        function fillInAddress(place) {
            for (const component of place.address_components) {
                const addressType = component.types[0];
                if (document.getElementById(addressType)) {
                    document.getElementById(addressType).value = component.long_name;
                }
            }
            checkFields();
        }

        function checkFields() {
            const street = document.getElementById('street').value.trim();
            const city = document.getElementById('city').value.trim();
            const region = document.getElementById('region').value.trim();
            const country = document.getElementById('country').value.trim();
            const zip = document.getElementById('zip').value.trim();

            const button = document.getElementById('confirmOrderButton');
            if (street && city && region && country && zip) {
                button.disabled = false;
                button.classList.remove('bg-gray-500', 'cursor-not-allowed');
                button.classList.add('bg-green-700', 'hover:bg-green-800');
            } else {
                button.disabled = true;
                button.classList.add('bg-gray-500', 'cursor-not-allowed');
                button.classList.remove('bg-green-700', 'hover:bg-green-800');
            }
        }


        document.addEventListener('DOMContentLoaded', () => {
            checkFields();
        });

        function toggleAddressSelection(type, addressId = null) {
            const addressForm = document.getElementById('addressForm');
            const confirmOrderButton = document.getElementById('confirmOrderButton');

            if (type === 'new') {
                addressForm.style.display = 'block';
                checkFields();
            } else {
                addressForm.style.display = 'none';
                confirmOrderButton.disabled = false;
                confirmOrderButton.classList.remove('bg-gray-500', 'cursor-not-allowed');
                confirmOrderButton.classList.add('bg-green-700', 'hover:bg-green-800');
            }
        }
    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&libraries=places&callback=initMap" async defer></script>
@endsection
