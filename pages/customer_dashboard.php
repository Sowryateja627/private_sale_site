<?php
include '../includes/dbconnection.php';
include '../includes/fetch_sales.php';
// include "../includes/viewpage.php";
session_start();

$sales = fetchSales($conn);

$page_id = 1; // Replace with the appropriate identifier for this page

// Increment the visit count
try {
    $conn->begin_transaction();

    // Check if the page ID exists in the table
    $stmt = $conn->prepare("SELECT visit_count FROM page_visits WHERE page_id = ?");
    $stmt->bind_param('i', $page_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Page ID exists, update visit count
        $stmt = $conn->prepare("UPDATE page_visits SET visit_count = visit_count + 1 WHERE page_id = ?");
    } else {
        // Page ID does not exist, insert new record
        $stmt = $conn->prepare("INSERT INTO page_visits (page_id, visit_count) VALUES (?, 1)");
    }

    $stmt->bind_param('i', $page_id);
    $stmt->execute();
    $conn->commit();
    $stmt->close();
} catch (mysqli_sql_exception $e) {
    $conn->rollback();
    // Handle the error (e.g., log it)
    error_log('Database error: ' . $e->getMessage());
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Dashboard</title>
    <link rel="stylesheet" href="../css/customer1.css">
    <style>
        .dropdown-content {
            display: none;
            margin: 2% 0 0 53%;

        }
        .dropdown-content.show {
            display: block;
           margin: 2% 0 0 53%;

        }
    </style>
</head>
<body>
    <header >
        <div class="header-container">
            <h1>Private Sale Site</h1>
             </div>
             <div>
                <a href="login.html">Logout</a>
                <a href="edit_user.php?user_id=<?php echo htmlspecialchars($_SESSION['user_id']); ?>">Account settings</a>
                <button onclick="myFunction()" class="dropbtn">Notification</button>
                <div id="myDropdown" class="dropdown-content">
                    <p></p>
                      
                </div>
            </div>

</div>
  
    </header>
  
  

    <main>
        <section class="sales-section">
            <h2>Current Sales</h2>
            <div class="sales-container-wrapper">
                <div id="current-sales" class="sales-container"></div>
            </div>

            <h2>Upcoming Sales</h2>
            <div class="sales-container-wrapper">
                <div id="future-sales" class="sales-container"></div>
            </div>
        </section>
    </main>

    <script>




function myFunction() {
    fetchAlerts();
    document.getElementById("myDropdown").classList.toggle("show");
  }
  
  // Close the dropdown if the user clicks outside of it
  window.onclick = function(event) {
    if (!event.target.matches('.dropbtn')) {
      var dropdowns = document.getElementsByClassName("dropdown-content");
      var i;
      for (i = 0; i < dropdowns.length; i++) {
        var openDropdown = dropdowns[i];
        if (openDropdown.classList.contains('show')) {
          openDropdown.classList.remove('show');
        }
      }
    }
  }


  function fetchAlerts() {
        fetch('../includes/fetchalerts.php')
            .then(response => response.json())
            .then(data => {
                const dropdown = document.getElementById('myDropdown');
                console.log(data,dropdown);
                
                if (!data.alerts || data.alerts.length === 0) {
                    dropdown.innerHTML = '<p>No new alerts.</p>';
                } else {
                    dropdown.innerHTML = '';
                    data.alerts.forEach(alert => {
                        const alertElement = document.createElement('p');
                        alertElement.textContent = `The ${alert.name} ${alert.alert_message} `;
                        dropdown.appendChild(alertElement);
                    });
                   ;
                }
            })
            .catch(error => {
                console.error('Error fetching alerts:', error);
            });

    function formatDate(timestamp) {
        const date = new Date(timestamp);
        return `${date.toLocaleDateString()} ${date.toLocaleTimeString()}`;
    }
    }























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
            window.location.href = `items.html?sale_id=${saleId}&type=cus`;

            // Log the view and redirect to items page
            // fetch('../includes/log_view.php', {
            //     method: 'POST',
            //     headers: {
            //         'Content-Type': 'application/x-www-form-urlencoded',
            //     },
            //     body: new URLSearchParams({
            //         sale_id: saleId
            //     })
            // })
            // .then(response => {
            //     if (response.ok) {
            //         // Redirect to items page after successful logging
            //     } else {
            //         console.error('Failed to log view.');
            //     }
            // })
            // .catch(error => {
            //     console.error('Error:', error);
            // });
        }

        displaySales();
    </script>

    <footer>
        <div class="footer-container">
            <p>&copy; 2024 Private Sale Site. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
