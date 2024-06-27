const searchInput = document.getElementById('search-input');
const searchResults = document.getElementById('search-results');

searchInput.addEventListener('input', function(e) {
    const value = e.target.value;

    if (!value) {
        document.getElementById('search-results').classList.add('hidden');
        return;
    }

    fetch(`/search-books?query=${encodeURIComponent(value)}`)
        .then(response => response.json())
        .then(data => {
            let resultsContainer = document.getElementById('search-results');
            resultsContainer.innerHTML = '';
            if (data.length) {
                data.forEach(book => {
                    let searchDiv = document.createElement('div');
                    searchDiv.classList.add('p-2', 'hover:bg-gray-100', 'cursor-pointer');
                    searchDiv.textContent = book.title;
                    searchDiv.dataset.id = book.id;
                    searchDiv.addEventListener('click', () => {
                        window.location.href = `/books/${book.id}`;
                    });
                    resultsContainer.appendChild(searchDiv);
                });
                resultsContainer.classList.remove('hidden');
            } else {
                resultsContainer.classList.add('hidden');
            }
        });
});

document.addEventListener('click', function(event) {
    if (!searchInput.contains(event.target) && !searchResults.contains(event.target)) {
    searchResults.classList.add('hidden');
    }
});

document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
    searchResults.classList.add('hidden');
    }
});