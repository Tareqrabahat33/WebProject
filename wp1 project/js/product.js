// Navigation toggle function
function toggleNav() {
    var navLinks = document.getElementById('navLinks');
    navLinks.classList.toggle('active');
}

document.addEventListener('DOMContentLoaded', function() {
    // Load products when page loads
    loadProducts('all');
    
    // Filter functionality
    const filterTags = document.querySelectorAll('.filter-tag');
    
    filterTags.forEach(tag => {
        tag.addEventListener('click', function() {
            // Remove active class from all tags
            filterTags.forEach(t => t.classList.remove('active'));
            
            // Add active class to clicked tag
            this.classList.add('active');
            
            // Get category from tag text
            let category = this.textContent.trim().toLowerCase();
            if (category === 'all yachts') category = 'all';
            
            // Load products for the selected category
            loadProducts(category);
        });
    });
    
    // Rent/Buy toggle functionality if they exist
    const rentOption = document.getElementById('rent-option');
    const buyOption = document.getElementById('buy-option');
    
    if (rentOption && buyOption) {
        // Default to rent option on page load
        document.body.classList.remove('buy-mode');
        
        rentOption.addEventListener('click', function() {
            rentOption.classList.add('active');
            buyOption.classList.remove('active');
            document.body.classList.remove('buy-mode');
        });
        
        buyOption.addEventListener('click', function() {
            buyOption.classList.add('active');
            rentOption.classList.remove('active');
            document.body.classList.add('buy-mode');
        });
    }
});

// Function to load products
function loadProducts(category) {
    fetch(`/wp1%20project/wp1%20project/php/products.php?action=list&category=${category}`)
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            displayProducts(data.products);
        } else {
            console.error('Error loading products:', data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

// Function to display products
function displayProducts(products) {
    const productGrid = document.querySelector('.product-grid');
    
    // Clear existing content
    productGrid.innerHTML = '';
    
    // Check if there are products
    if (products.length === 0) {
        productGrid.innerHTML = '<p class="no-products">No yachts found in this category.</p>';
        return;
    }
    
    // Add products to grid
    products.forEach(product => {
        const statusHtml = product.status ? `<span class="product-status">${product.status}</span>` : '';
        
        const productCard = document.createElement('div');
        productCard.className = 'product-card';
        productCard.innerHTML = `
            <div class="product-image">
                <img src="${product.image_path}" alt="${product.model} Yacht">
                ${statusHtml}
            </div>
            <div class="product-details">
                <h3 class="product-title">${product.model}</h3>
                <div class="product-specs">
                    <span>Length: ${product.length}</span>
                    <span>Year: ${product.year}</span>
                    <span>Cabins: ${product.cabins}</span>
                </div>
                <p>${product.description}</p>
                <div class="product-price">
                    <div class="rent-price">Rent: $${parseFloat(product.rent_price).toLocaleString()}/day</div>
                    <div class="buy-price">Buy: $${parseFloat(product.buy_price).toLocaleString()}</div>
                </div>
                <div class="product-actions">
                    <a href="#" class="btn-view" onclick="alert('Details coming soon!')">View Details</a>
                    <a href="contact_page.html?yacht=${product.model}" class="btn-inquire">Inquire</a>
                </div>
            </div>
        `;
        
        productGrid.appendChild(productCard);
    });
}
