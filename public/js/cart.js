document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.update-quantity-btn').forEach(button => {
        button.addEventListener('click', function() {
            const itemId = this.getAttribute('data-id');
            const newQuantity = this.getAttribute('data-quantity');

            clearErrorMessages();
            
            fetch("{{ route('cart.update') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ id: itemId, quantity: newQuantity })
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    window.location.reload();
                } else {
                    displayErrorMessage(data.message);
                }
            });
        });
    });
});

function clearErrorMessages() {
    document.querySelectorAll('.error-message').forEach(message => {
        message.remove();
    });
}

function displayErrorMessage(message) {
    const errorMessage = document.createElement('div');
    errorMessage.classList.add('p-4', 'bg-red-500', 'text-white', 'mb-4', 'rounded-md', 'error-message');
    errorMessage.textContent = message;
    
    const cartContainer = document.querySelector('.container');
    cartContainer.insertBefore(errorMessage, cartContainer.firstChild);
}