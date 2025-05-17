// Admin panel navigation
function toggleNav() {
  var navLinks = document.getElementById('navLinks');
  navLinks.classList.toggle('active');
}

document.addEventListener('DOMContentLoaded', function() {
  const sidebarLinks = document.querySelectorAll('.admin-sidebar a');
  
  sidebarLinks.forEach(link => {
    link.addEventListener('click', function() {
      sidebarLinks.forEach(l => l.classList.remove('active'));
      this.classList.add('active');
    });
  });

  // Product Management System
  // Get the form element
  const yachtForm = document.querySelector('#yacht-management form');
  
  // Add event listener for form submission
  if (yachtForm) {
    yachtForm.addEventListener('submit', function(e) {
      e.preventDefault();
      
      // Create FormData object to handle file uploads
      const formData = new FormData(this);
      
      // Determine if this is a new yacht or an edit
      const yachtId = formData.get('id');
      const action = yachtId ? 'edit' : 'add';
      
      // Add action to form data
      formData.append('action', action);
      
      // Send AJAX request
      fetch('/wp1%20project/wp1%20project/php/products.php', {
          method: 'POST',
          body: formData
      })
      .then(response => response.json())
      .then(data => {
          if (data.status === 'success') {
              alert(data.message);
              yachtForm.reset();
              loadYachts(); // Refresh the yacht list
          } else {
              alert('Error: ' + data.message);
          }
      })
      .catch(error => {
          console.error('Error:', error);
          alert('An error occurred. Please try again.');
      });
    });
  }
  
  // Function to load existing yachts
  function loadYachts() {
    fetch('/wp1%20project/wp1%20project/php/products.php?action=list')
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            displayYachts(data.products);
        } else {
            console.error('Error loading yachts:', data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
  }
  
  // Function to display yachts in a table
  function displayYachts(products) {
    // Create or get yacht list container
    let yachtList = document.querySelector('#yacht-list');
    
    if (!yachtList) {
        yachtList = document.createElement('div');
        yachtList.id = 'yacht-list';
        document.querySelector('#yacht-management').appendChild(yachtList);
    }
    
    // Clear existing content
    yachtList.innerHTML = '';
    
    // Create table
    const table = document.createElement('table');
    table.className = 'admin-table';
    
    // Add table header
    table.innerHTML = `
        <thead>
            <tr>
                <th>Image</th>
                <th>Model</th>
                <th>Length</th>
                <th>Year</th>
                <th>Status</th>
                <th>Buy Price</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody></tbody>
    `;
    
    const tbody = table.querySelector('tbody');
    
    // Add products to table
    if (products.length === 0) {
        const row = document.createElement('tr');
        row.innerHTML = '<td colspan="7">No yachts found</td>';
        tbody.appendChild(row);
    } else {
        products.forEach(product => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td><img src="${product.image_path}" width="50" height="30" alt="${product.model}"></td>
                <td>${product.model}</td>
                <td>${product.length}</td>
                <td>${product.year}</td>
                <td>${product.status}</td>
                <td>$${parseFloat(product.buy_price).toLocaleString()}</td>
                <td>
                    <button class="edit-btn" data-id="${product.id}">Edit</button>
                    <button class="delete-btn" data-id="${product.id}">Delete</button>
                </td>
            `;
            tbody.appendChild(row);
        });
    }
    
    yachtList.appendChild(table);
    
    // Add event listeners for edit and delete buttons
    document.querySelectorAll('.edit-btn').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            editYacht(id);
        });
    });
    
    document.querySelectorAll('.delete-btn').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            if (confirm('Are you sure you want to delete this yacht?')) {
                deleteYacht(id);
            }
        });
    });
  }
  
  // Function to edit a yacht
  function editYacht(id) {
    fetch(`/wp1%20project/wp1%20project/php/products.php?action=get&id=${id}`)
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            const product = data.product;
            
            // Populate form with product data
            document.getElementById('yacht-model').value = product.model;
            document.getElementById('yacht-length').value = product.length;
            document.getElementById('yacht-year').value = product.year;
            document.getElementById('yacht-cabins').value = product.cabins;
            document.getElementById('yacht-rent-price').value = product.rent_price;
            document.getElementById('yacht-price').value = product.buy_price;
            document.getElementById('yacht-status').value = product.status;
            document.getElementById('yacht-category').value = product.category;
            document.getElementById('yacht-description').value = product.description;
            
            // Add hidden field for ID
            let idField = document.getElementById('yacht-id');
            if (!idField) {
                idField = document.createElement('input');
                idField.type = 'hidden';
                idField.id = 'yacht-id';
                idField.name = 'id';
                yachtForm.appendChild(idField);
            }
            idField.value = product.id;
            
            // Change button text
            const submitBtn = yachtForm.querySelector('button[type="submit"]');
            submitBtn.textContent = 'Update Yacht';
            
            // Add cancel button if it doesn't exist
            let cancelBtn = document.getElementById('cancel-edit');
            if (!cancelBtn) {
                cancelBtn = document.createElement('button');
                cancelBtn.type = 'button';
                cancelBtn.id = 'cancel-edit';
                cancelBtn.className = 'admin-btn';
                cancelBtn.textContent = 'Cancel';
                submitBtn.parentNode.appendChild(cancelBtn);
                
                cancelBtn.addEventListener('click', function() {
                    yachtForm.reset();
                    if (document.getElementById('yacht-id')) {
                        document.getElementById('yacht-id').remove();
                    }
                    submitBtn.textContent = 'Add Yacht';
                    this.remove();
                });
            }
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
    });
  }
  
  // Function to delete a yacht
  function deleteYacht(id) {
    fetch(`/wp1%20project/wp1%20project/php/products.php?action=delete&id=${id}`)
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            alert(data.message);
            loadYachts(); // Refresh the yacht list
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
    });
  }
  
  // Load yachts when page loads
  loadYachts();
});
