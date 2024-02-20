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

function toggleAddressSelection(type, addressId = null) {
    const addressForm = document.getElementById('addressForm');
    const selectedAddressId = document.getElementById('selectedAddressId');
    const continueButton = document.getElementById('continueButton');

    if (type === 'new') {
        addressForm.style.display = 'block';
        selectedAddressId.value = '';
    } else {
        addressForm.style.display = 'none';
        selectedAddressId.value = addressId;
        continueButton.disabled = false;
        continueButton.classList.remove('bg-gray-500', 'cursor-not-allowed');
        continueButton.classList.add('bg-green-700', 'hover:bg-green-800');
    }
}

document.getElementById('orderForm').addEventListener('submit', function(e) {
    const street = document.getElementById('street').value;
    const city = document.getElementById('city').value;
    const region = document.getElementById('region').value;
    const country = document.getElementById('country').value;
    const zip = document.getElementById('zip').value;
    const contactNumber = document.getElementById('contact_number').value;
    let isValid = true;

    var streetError = document.getElementById('street-error');
    streetError.innerHTML = '';
    if (!street) {
        streetError.innerHTML = 'Street is required.\n';
        isValid = false;
    }else if(street.length > 255){
        streetError.innerHTML = 'Street cannot exceed 255 characters.\n';
        isValid = false;
    }

    var cityError = document.getElementById('city-error');
    cityError.innerHTML = '';
    if (!city) {
        cityError.innerHTML = 'City is required.\n';
        isValid = false;
    }else if (city.length > 255){
        cityError.innerHTML = 'City cannot exceed 255 characters.\n';
        isValid = false;
    }

    var regionError = document.getElementById('region-error');
    regionError.innerHTML = '';
    if (!region) {
        regionError.innerHTML = 'Region is required.\n';
        isValid = false;
    }else if(region.length > 255){
        regionError.innerHTML = 'Region cannot exceed 255 characters.\n';
        isValid = false;
    }

    var countryError = document.getElementById('country-error');
    countryError.innerHTML = '';
    if (!country) {
        countryError.innerHTML = 'Country is required.\n';
        isValid = false;
    }else if(country.length > 255){
        countryError.innerHTML = 'Country cannot exceed 255 characters.\n';
        isValid = false;
    }

    var zipError = document.getElementById('zip-error');
    zipError.innerHTML = '';
    if (!zip) {
        zipError.innerHTML = 'ZIP code is required.\n';
        isValid = false;
    }else if(!/^\d{1,6}$/.test(zip)){
        zipError.innerHTML = 'ZIP code must be an integer and cannot exceed 6 digits.\n';
        isValid = false;
    }

    var contactError = document.getElementById('contact-error');
    contactError.innerHTML = '';
    if (!contactNumber) {
        contactError.innerHTML = 'Contact number is required.\n';
        isValid = false;
    }else if(!/^[\d\s+\-()]{1,15}$/.test(contactNumber)) {
        contactError.innerHTML = 'Contact number must be an integer and should be of proper format including + and dashes.\n';
        isValid = false;
    }

    if (!isValid) {
        e.preventDefault();
    }
});