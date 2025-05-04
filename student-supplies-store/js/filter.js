
document.addEventListener('DOMContentLoaded', () => {
    console.log("Filter/Sort trigger script loaded.");

    const sortSelect = document.getElementById('sort-select');

    // --- Event Listener for Sort Select ---
    if (sortSelect) {
        sortSelect.addEventListener('change', (event) => {
            const newSortValue = event.target.value;
            console.log(`Sort changed to: ${newSortValue}`);

            // Get current URL parameters
            const currentUrl = new URL(window.location.href);
            const params = currentUrl.searchParams;

            // Set the new sort parameter
            params.set('sort', newSortValue);

            // Reset page to 1 when changing sort order
            params.delete('page'); // Remove page param to go to page 1

            // Navigate to the new URL
            // Construct the path part + the modified query string
            window.location.href = currentUrl.pathname + '?' + params.toString();
        });
    } else {
        console.warn("Sort select (#sort-select) not found.");
    }
});