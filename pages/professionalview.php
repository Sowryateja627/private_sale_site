<?php
include '../includes/dbconnection.php';
include '../includes/fetch_sales.php';
session_start();
$sales = fetchSales($conn);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>professional View</title>
    <link rel="stylesheet" href="../css/profview.css">
</head>
<body>
    <header>
    <div class="header-container">

        <h1>Welcome</h1>
        <a href="logout.php">Logout</a>
        </div>
        
    </header>

    <main>
        <section class="sales-section">
            <h2>Current Sales</h2>
            <div id="current-sales" class="sales-container"></div>

            <h2>Upcoming Sales</h2>
            <div id="future-sales" class="sales-container"></div>
        </section>
    </main>

    <!-- Modal for displaying items -->
    <div id="item-modal" class="modal">
        <div class="modal-content">
            <span class="close-btn">&times;</span>
            <h2>Items in Sale</h2>
            <div id="item-list"></div>
        </div>
    </div>

    <script>
        // Pass PHP data to JavaScript
        const sales = <?php echo json_encode($sales); ?>;

        function formatDate(dateString) {
            const options = { year: 'numeric', month: '2-digit', day: '2-digit' };
            return new Date(dateString).toLocaleDateString(undefined, options);
        }

        function displaySales() {
            const currentSalesContainer = document.getElementById('current-sales');
            const futureSalesContainer = document.getElementById('future-sales');
            const now = new Date();

            sales.forEach(sale => {
                const saleCard = document.createElement('div');
                saleCard.className = 'sale-card';
                
                saleCard.innerHTML = `
                    <h3>${sale.title}</h3>
                    <p>${sale.description}</p>
                    <p><strong>Start Date:</strong> ${formatDate(sale.start_date)}</p>
                    <p><strong>End Date:</strong> ${formatDate(sale.end_date)}</p>
                    <button onclick="viewItems(${sale.sale_id})">Check Items</button>
                    <button onclick="editSale(${sale.sale_id})">Edit</button>
                    <button onclick="deleteSale(${sale.sale_id})">Delete</button>
                `;
                
                const startDate = new Date(sale.start_date);
                if (startDate > now) {
                    futureSalesContainer.appendChild(saleCard);
                } else {
                    currentSalesContainer.appendChild(saleCard);
                }
            });
        }

        function viewItems(saleId) {
        
            window.location.href = `items.html?sale_id=${saleId}&type=pro`;

        }

        function editSale(saleId) {
            window.location.href = `edit_sales.php?sale_id=${saleId}`;
        }

     function deleteSale(saleId) {
    if (confirm('Are you sure you want to delete this sale?')) {
        fetch(`../includes/delete_sale.php`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: new URLSearchParams({
                'sale_id': saleId,
                'action': 'delete'
            })
        })
        .then(response => response.json())
        .then(result => {
            console.log(result);
            
            if (result.success) {
                alert('Sale deleted successfully.');
                location.reload(); // Reload the page to reflect the changes
            } else {
                alert('Failed to delete sale.');
            }
        })
        .catch(error => console.error('Error deleting sale:', error));
    }
}


        displaySales();
    </script>
</body>
</html>
