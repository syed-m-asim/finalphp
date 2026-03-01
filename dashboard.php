<?php

include("connection.php");

$message = "";
$msgType = "";

// Agar form submit hua (AJAX request ke through action='add_product')
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'add_product') {

    // 1. Text Inputs Sanitize karein
    $pname = mysqli_real_escape_string($call, $_POST['pname']); 
    $description = mysqli_real_escape_string($call, $_POST['description']);
    $price = $_POST['price'];
    $quantity = $_POST['quantity'];

    // 2. Image Upload Logic
    $imagename = $_FILES['image']['name'];
    $imagetmpname = $_FILES['image']['tmp_name'];
    $folder = "uploads/"; 
    $filepath = $folder . $imagename;

    // Image ko server par move karein
    if (move_uploaded_file($imagetmpname, $filepath)) {
        
        $query = "INSERT INTO `product`(`pname`, `pdescription`, `pprice`, `pquantity`, `pimage`)
                  VALUES ('$pname','$description','$price','$quantity','$filepath')";

        if (mysqli_query($call, $query)) {
            $message = "Product successfully added!";
            $msgType = "success";
        } else {
            $message = "Database Error: " . mysqli_error($call);
            $msgType = "error";
        }
    } else {
        $message = "Image upload failed!";
        $msgType = "error";
    }

    // JSON Response bhejein JavaScript ko
    echo json_encode(['status' => $msgType, 'msg' => $message]);
    exit; // PHP ruk jayega
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ultras Admin - Summer Collection</title>
    <!-- Icons ke liye FontAwesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            /* Summer Theme Color Palette */
            --primary-color: #0ea5e9;
            /* Sky Blue */
            --primary-dark: #0284c7;
            --secondary-color: #f59e0b;
            /* Sunny Yellow */
            --bg-color: #f0f9ff;
            --sidebar-bg: #ffffff;
            --card-bg: #ffffff;
            --text-main: #334155;
            --text-light: #64748b;
            --danger: #ef4444;
            --success: #10b981;

            --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1);
            --radius: 12px;
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: var(--bg-color);
            color: var(--text-main);
            display: flex;
            height: 100vh;
            overflow: hidden;
        }

        /* --- Sidebar --- */
        .sidebar {
            width: 260px;
            background: var(--sidebar-bg);
            border-right: 1px solid #e2e8f0;
            display: flex;
            flex-direction: column;
            transition: var(--transition);
            z-index: 100;
        }

        .logo-area {
            padding: 24px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 24px;
            font-weight: 800;
            color: var(--primary-color);
            border-bottom: 1px solid #f1f5f9;
        }

        .logo-icon {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .nav-links {
            list-style: none;
            padding: 20px 10px;
            flex: 1;
        }

        .nav-item {
            margin-bottom: 8px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            text-decoration: none;
            color: var(--text-light);
            border-radius: var(--radius);
            font-weight: 500;
            transition: var(--transition);
        }

        .nav-link:hover,
        .nav-link.active {
            background-color: #e0f2fe;
            color: var(--primary-color);
        }

        .nav-link i {
            width: 20px;
            text-align: center;
        }

        /* --- Main Content --- */
        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        header {
            background: var(--card-bg);
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: var(--shadow-sm);
        }

        .header-title h2 {
            font-size: 1.25rem;
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
        }

        .avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--primary-color);
        }

        .content-scroll {
            flex: 1;
            overflow-y: auto;
            padding: 30px;
        }

        /* --- Widgets --- */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 24px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: var(--card-bg);
            padding: 24px;
            border-radius: var(--radius);
            box-shadow: var(--shadow-sm);
            display: flex;
            align-items: center;
            gap: 20px;
            border-bottom: 4px solid transparent;
            transition: var(--transition);
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-md);
        }

        .stat-card.blue {
            border-bottom-color: var(--primary-color);
        }

        .stat-card.yellow {
            border-bottom-color: var(--secondary-color);
        }

        .stat-card.green {
            border-bottom-color: var(--success);
        }

        .stat-card.red {
            border-bottom-color: var(--danger);
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }

        .bg-blue-light {
            background: #e0f2fe;
            color: var(--primary-color);
        }

        .bg-yellow-light {
            background: #fef3c7;
            color: var(--secondary-color);
        }

        .bg-green-light {
            background: #d1fae5;
            color: var(--success);
        }

        .bg-red-light {
            background: #fee2e2;
            color: var(--danger);
        }

        .stat-info h3 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 4px;
        }

        .stat-info p {
            color: var(--text-light);
            font-size: 14px;
        }

        /* --- Tabs --- */
        .section-container {
            display: none;
            animation: fadeIn 0.4s ease;
        }

        .section-container.active {
            display: block;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* --- Table & Cards --- */
        .card {
            background: var(--card-bg);
            border-radius: var(--radius);
            box-shadow: var(--shadow-sm);
            padding: 24px;
            margin-bottom: 24px;
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .btn {
            padding: 10px 20px;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: var(--transition);
            text-decoration: none;
            font-size: 14px;
        }

        .btn-primary {
            background-color: var(--primary-color);
            color: white;
        }

        .btn-primary:hover {
            background-color: var(--primary-dark);
        }

        .btn-danger {
            background-color: #fee2e2;
            color: var(--danger);
            padding: 6px 12px;
            font-size: 12px;
        }

        .btn-danger:hover {
            background-color: #fecaca;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 16px;
            text-align: left;
            border-bottom: 1px solid #f1f5f9;
        }

        th {
            color: var(--text-light);
            font-weight: 600;
            font-size: 14px;
            text-transform: uppercase;
        }

        td {
            font-size: 15px;
        }

        .product-img {
            width: 50px;
            height: 50px;
            border-radius: 8px;
            object-fit: cover;
        }

        .badge {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .badge-stock {
            background: #d1fae5;
            color: var(--success);
        }

        .badge-low {
            background: #fef3c7;
            color: var(--secondary-color);
        }

        /* --- Modal --- */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            opacity: 0;
            pointer-events: none;
            transition: var(--transition);
        }

        .modal-overlay.open {
            opacity: 1;
            pointer-events: all;
        }

        .modal {
            background: white;
            padding: 30px;
            border-radius: var(--radius);
            width: 500px;
            max-width: 90%;
            transform: scale(0.9);
            transition: var(--transition);
            max-height: 90vh;
            overflow-y: auto;
        }

        .modal-overlay.open .modal {
            transform: scale(1);
        }

        .form-group {
            margin-bottom: 16px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            font-size: 14px;
        }

        .form-control {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            outline: none;
            transition: var(--transition);
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px #e0f2fe;
        }

        /* --- Chart --- */
        .chart-container {
            display: flex;
            align-items: flex-end;
            justify-content: space-around;
            height: 250px;
            padding-top: 20px;
            border-bottom: 1px solid #e2e8f0;
        }

        .bar-group {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 10%;
            height: 100%;
            justify-content: flex-end;
        }

        .bar {
            width: 100%;
            background: linear-gradient(to top, var(--primary-color), #7dd3fc);
            border-radius: 4px 4px 0 0;
            transition: height 1s ease;
        }

        .bar:hover {
            background: linear-gradient(to top, var(--primary-dark), #38bdf8);
        }

        .bar-label {
            margin-top: 10px;
            font-size: 12px;
            color: var(--text-light);
        }

        /* --- Toast --- */
        .toast {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background: white;
            padding: 16px 24px;
            border-radius: 8px;
            box-shadow: var(--shadow-md);
            border-left: 5px solid var(--success);
            display: flex;
            align-items: center;
            gap: 12px;
            transform: translateX(150%);
            transition: transform 0.3s ease;
            z-index: 2000;
        }

        .toast.show {
            transform: translateX(0);
        }

        .toast.error {
            border-left-color: var(--danger);
        }

        .toast i {
            font-size: 20px;
        }

        .toast.success i {
            color: var(--success);
        }

        .toast.error i {
            color: var(--danger);
        }

        .menu-toggle {
            display: none;
            font-size: 24px;
            cursor: pointer;
            color: var(--text-main);
        }

        @media (max-width: 768px) {
            .sidebar {
                position: fixed;
                left: -260px;
                height: 100%;
            }

            .sidebar.active {
                left: 0;
            }

            .menu-toggle {
                display: block;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>

    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="logo-area">
            <i class="fa-solid fa-sun logo-icon"></i>
            <span>ULTRAS</span>
        </div>
        <ul class="nav-links">
            <li class="nav-item">
                <a href="#" class="nav-link active" onclick="switchTab('dashboard', this)"><i class="fa-solid fa-chart-line"></i> Dashboard</a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link" onclick="switchTab('collection', this)"><i class="fa-solid fa-shirt"></i> Summer Collection</a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link" onclick="switchTab('orders', this)"><i class="fa-solid fa-box-open"></i> Orders</a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link"><i class="fa-solid fa-users"></i> Customers</a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link"><i class="fa-solid fa-gear"></i> Settings</a>
            </li>
        </ul>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <header>
            <div class="header-title" style="display: flex; align-items: center; gap: 15px;">
                <i class="fa-solid fa-bars menu-toggle" onclick="toggleSidebar()"></i>
                <h2 id="page-title">Dashboard Overview</h2>
            </div>
            <div class="header-actions">
                <i class="fa-regular fa-bell" style="cursor: pointer; color: var(--text-light);"></i>
                <div class="user-profile">
                    <span style="font-size: 14px; font-weight: 600;">Admin User</span>
                    <img src="https://picsum.photos/seed/admin/100/100" alt="Admin" class="avatar">
                </div>
            </div>
        </header>

        <div class="content-scroll">
            <!-- DASHBOARD SECTION -->
            <section id="dashboard" class="section-container active">
                <div class="stats-grid">
                    <div class="stat-card blue">
                        <div class="stat-icon bg-blue-light"><i class="fa-solid fa-bag-shopping"></i></div>
                        <div class="stat-info">
                            <h3>1,240</h3>
                            <p>Total Sales</p>
                        </div>
                    </div>
                    <div class="stat-card yellow">
                        <div class="stat-icon bg-yellow-light"><i class="fa-solid fa-sun"></i></div>
                        <div class="stat-info">
                            <h3>85</h3>
                            <p>New Summer Items</p>
                        </div>
                    </div>
                    <div class="stat-card green">
                        <div class="stat-icon bg-green-light"><i class="fa-solid fa-dollar-sign"></i></div>
                        <div class="stat-info">
                            <h3>$45,200</h3>
                            <p>Revenue</p>
                        </div>
                    </div>
                    <div class="stat-card red">
                        <div class="stat-icon bg-red-light"><i class="fa-solid fa-heart"></i></div>
                        <div class="stat-info">
                            <h3>342</h3>
                            <p>Wishlist Items</p>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h3>Summer Sales Trend</h3>
                        <select class="form-control" style="width: auto; padding: 5px 10px;">
                            <option>This Week</option>
                            <option>Last Week</option>
                        </select>
                    </div>
                    <div class="chart-container">
                        <div class="bar-group">
                            <div class="bar" style="height: 40%;"></div><span class="bar-label">Mon</span>
                        </div>
                        <div class="bar-group">
                            <div class="bar" style="height: 65%;"></div><span class="bar-label">Tue</span>
                        </div>
                        <div class="bar-group">
                            <div class="bar" style="height: 85%;"></div><span class="bar-label">Wed</span>
                        </div>
                        <div class="bar-group">
                            <div class="bar" style="height: 50%;"></div><span class="bar-label">Thu</span>
                        </div>
                        <div class="bar-group">
                            <div class="bar" style="height: 95%;"></div><span class="bar-label">Fri</span>
                        </div>
                        <div class="bar-group">
                            <div class="bar" style="height: 70%;"></div><span class="bar-label">Sat</span>
                        </div>
                        <div class="bar-group">
                            <div class="bar" style="height: 60%;"></div><span class="bar-label">Sun</span>
                        </div>
                    </div>
                </div>
            </section>

            <!-- COLLECTION SECTION -->
            <section id="collection" class="section-container">
                <div class="card">
                    <div class="card-header">
                        <h3>Summer Collection Inventory</h3>
                        <button class="btn btn-primary" onclick="openModal()">
                            <i class="fa-solid fa-plus"></i> Add New Product
                        </button>
                    </div>

                    <div style="overflow-x: auto;">
                        <table id="productTable">
                            <thead>
                                <tr>
                                    <th>Image</th>
                                    <th>Product Name</th>
                                    <th>Description</th>
                                    <th>Price</th>
                                    <th>Quantity</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="tableBody">
                                <!-- Data JS se render hoga -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

            <!-- ORDERS SECTION -->
            <section id="orders" class="section-container">
                <div class="card">
                    <div class="card-header">
                        <h3>Recent Orders</h3>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Customer</th>
                                <th>Items</th>
                                <th>Total</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>#ORD-001</td>
                                <td>Sarah Khan</td>
                                <td>Floral Summer Dress</td>
                                <td>$45.00</td>
                                <td><span class="badge badge-stock">Shipped</span></td>
                            </tr>
                            <tr>
                                <td>#ORD-002</td>
                                <td>Ahmed Ali</td>
                                <td>Beach Shorts x2</td>
                                <td>$30.00</td>
                                <td><span class="badge badge-low">Pending</span></td>
                            </tr>
                            <tr>
                                <td>#ORD-003</td>
                                <td>Zara Ahmed</td>
                                <td>Sun Hat & Sunglasses</td>
                                <td>$25.50</td>
                                <td><span class="badge badge-stock">Delivered</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>

        </div>
    </main>

    <!-- Add Product Modal -->
    <div class="modal-overlay" id="productModal">
        <div class="modal">
            <div class="card-header">
                <h3>Add Summer Item</h3>
                <i class="fa-solid fa-xmark" onclick="closeModal()" style="cursor: pointer; font-size: 20px;"></i>
            </div>
            <!-- enctype="multipart/form-data" is required for file uploads -->
            <form id="addProductForm" action="" method="post" enctype="multipart/form-data">
                <input type="hidden" name="action" value="add_product">

                <div class="form-group">
                    <label>Product Name</label>
                    <input type="text" id="pname" name="pname" class="form-control" placeholder="e.g. Tropical Shirt" required>
                </div>

                <div class="form-group">
                    <label>Description</label>
                    <input type="text" id="description" name="description" class="form-control" placeholder="Product details..." required>
                </div>

                <div class="form-group">
                    <label>Price ($)</label>
                    <input type="number" id="price" name="price" class="form-control" placeholder="0.00" step="0.01" required>
                </div>

                <div class="form-group">
                    <label>Quantity</label>
                    <input type="number" id="quantity" name="quantity" class="form-control" placeholder="0" required>
                </div>

                <div class="form-group">
                    <label>Product Image</label>
                    <input type="file" name="image" id="image" class="form-control" required>
                    <small style="color: #64748b;">Upload an image (jpg, png)</small>
                </div>

                <div style="text-align: right; margin-top: 20px;">
                    <button type="button" class="btn" onclick="closeModal()" style="background: #e2e8f0; margin-right: 10px;">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Product</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Toast Notification -->
    <div class="toast" id="toast">
        <i class="fa-solid fa-circle-check"></i>
        <span id="toastMsg">Action Successful!</span>
    </div>

    <script>
        // --- 1. Initial Mock Data ---
        let products = [{
                id: 1,
                name: "Floral Breeze Dress",
                description: "Cotton summer dress",
                price: 55.00,
                quantity: 10,
                pimage: "https://picsum.photos/seed/dress/100/100"
            },
            {
                id: 2,
                name: "Palm Tree Shirt",
                description: "Relaxed fit shirt",
                price: 35.00,
                quantity: 5,
                pimage: "https://picsum.photos/seed/shirt/100/100"
            },
            {
                id: 3,
                name: "Ocean Wave Sandals",
                description: "Beach footwear",
                price: 40.00,
                quantity: 12,
                pimage: "https://picsum.photos/seed/shoes/100/100"
            }
        ];

        // --- 2. Render Table Function ---
        function renderTable() {
            const tbody = document.getElementById('tableBody');
            tbody.innerHTML = '';

            products.forEach((product) => {
                // Agar pimage local path hai (uploads/) to use directly, warna mock image
                const imgSrc = product.pimage.startsWith('http') ? product.pimage : product.pimage;

                const row = `
                    <tr>
                        <td><img src="${imgSrc}" class="product-img" alt="Product"></td>
                        <td>${product.name}</td>
                        <td>${product.description}</td>
                        <td>$${parseFloat(product.price).toFixed(2)}</td>
                        <td>${product.quantity}</td>
                        <td>
                            <button class="btn btn-danger" onclick="deleteProduct(${product.id})">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;
                tbody.innerHTML += row;
            });
        }

        // --- 3. Navigation Logic ---
        function switchTab(tabId, element) {
            document.querySelectorAll('.section-container').forEach(sec => sec.classList.remove('active'));
            document.getElementById(tabId).classList.add('active');

            const titles = {
                'dashboard': 'Dashboard Overview',
                'collection': 'Summer Collection Management',
                'orders': 'Customer Orders'
            };
            document.getElementById('page-title').innerText = titles[tabId] || 'Ultras Admin';

            document.querySelectorAll('.nav-link').forEach(link => link.classList.remove('active'));
            if (element) element.classList.add('active');

            if (window.innerWidth <= 768) {
                document.getElementById('sidebar').classList.remove('active');
            }
        }

        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('active');
        }

        // --- 4. Modal Logic ---
        function openModal() {
            document.getElementById('productModal').classList.add('open');
        }

        function closeModal() {
            document.getElementById('productModal').classList.remove('open');
            document.getElementById('addProductForm').reset();
        }

        // --- 5. AJAX Add Product Logic (Database Connection + File Upload) ---
        document.getElementById('addProductForm').addEventListener('submit', function(e) {
            e.preventDefault(); // Page refresh rokne ke liye

            const formData = new FormData(this);

            // Data bhej rahe hain USI PAGE par
            fetch('', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        // Database mein save ho gaya
                        // Local array ko update karein taaki table refresh ho bina reload
                        // Note: Server se wapas image path nahi mil raha isliye hum mock kar rahe hain ya browser read karega
                        const fileInput = document.getElementById('image');
                        const file = fileInput.files[0];

                        let imageUrl = "https://picsum.photos/200"; // Fallback

                        if (file) {
                            // Create a temporary URL for preview immediately
                            imageUrl = URL.createObjectURL(file);
                        }

                        const newProduct = {
                            id: Date.now(),
                            name: formData.get('pname'),
                            description: formData.get('description'),
                            price: formData.get('price'),
                            quantity: formData.get('quantity'),
                            pimage: imageUrl // Temporary preview
                        };

                        products.push(newProduct);
                        renderTable();
                        closeModal();
                        showToast(data.msg, 'success');

                        // Optional: Page refresh karein agar real server path chahiye
                        // setTimeout(() => location.reload(), 1500);
                    } else {
                        showToast(data.msg, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast("Server Error! Please check connection.", 'error');
                });
        });

        // --- 6. Delete Product Logic ---
        function deleteProduct(id) {
            if (confirm("Are you sure you want to remove this item?")) {
                products = products.filter(p => p.id !== id);
                renderTable();
                showToast("Item removed from list.", 'success');
            }
        }

        // --- 7. Toast Notification ---
        function showToast(message, type = 'success') {
            const toast = document.getElementById('toast');
            const toastMsg = document.getElementById('toastMsg');
            const icon = toast.querySelector('i');

            toastMsg.innerText = message;

            toast.className = 'toast';
            if (type === 'error') {
                toast.classList.add('error');
                icon.className = 'fa-solid fa-circle-exclamation';
            } else {
                toast.classList.add('show');
                icon.className = 'fa-solid fa-circle-check';
            }

            setTimeout(() => toast.classList.add('show'), 10);
            setTimeout(() => {
                toast.classList.remove('show');
            }, 3000);
        }

        // --- Initialize ---
        window.onload = function() {
            renderTable();
        };
    </script>
</body>

</html>