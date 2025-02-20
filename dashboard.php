<?php
session_start();
include 'database/db_connect.php';

// Configurare paginare
$items_per_page = 100;
$page = isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0 ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $items_per_page;

try {
    // Construim filtrarea
    $filters = [];
    $params = [];
    
    if (!empty($_GET['type'])) {
    $filters[] = "type = :type";
    $params[':type'] = $_GET['type'];
    }


    if (!empty($_GET['country'])) {
        $filters[] = "country = :country";
        $params[':country'] = $_GET['country'];
    }

    if (!empty($_GET['city'])) {
        $filters[] = "city = :city";
        $params[':city'] = $_GET['city'];
    }

    if (!empty($_GET['asn'])) {
        $filters[] = "asn = :asn";
        $params[':asn'] = $_GET['asn'];
    }

    if (!empty($_GET['organization'])) {
        $filters[] = "organization LIKE :organization";
        $params[':organization'] = '%' . $_GET['organization'] . '%';
    }

    $filter_sql = !empty($filters) ? 'WHERE ' . implode(' AND ', $filters) : '';

    // Interogare pentru numărul total
    $count_query = "SELECT COUNT(*) as total FROM proxies $filter_sql";
    $count_stmt = $pdo->prepare($count_query);
    foreach ($params as $key => $value) {
        $count_stmt->bindValue($key, $value);
    }
    $count_stmt->execute();
    $total_items = $count_stmt->fetch(PDO::FETCH_ASSOC)['total'];
    $total_pages = ceil($total_items / $items_per_page);

    // Interogare pentru date
    $query = "SELECT id, type, country, city, ip, port, asn, organization, date_added
              FROM proxies
              $filter_sql
              ORDER BY date_added DESC
              LIMIT :limit OFFSET :offset";
    $stmt = $pdo->prepare($query);

    // Legăm parametrii filtrați
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->bindValue(':limit', $items_per_page, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();

    $proxies = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Dropdown-uri pentru filtrare
    $types = $pdo->query("SELECT DISTINCT type FROM proxies")->fetchAll(PDO::FETCH_COLUMN);
    $countries = $pdo->query("SELECT DISTINCT country FROM proxies")->fetchAll(PDO::FETCH_COLUMN);
    $cities = $pdo->query("SELECT DISTINCT city FROM proxies")->fetchAll(PDO::FETCH_COLUMN);
    $asns = $pdo->query("SELECT DISTINCT asn FROM proxies")->fetchAll(PDO::FETCH_COLUMN);
    $organizations = $pdo->query("SELECT DISTINCT organization FROM proxies")->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

?>

<?php
function timeAgo($dateTime) {
    $time = strtotime($dateTime);
    $timeDiff = time() - $time;

    if ($timeDiff < 60) {
        return $timeDiff . " seconds ago";
    } elseif ($timeDiff < 3600) {
        return floor($timeDiff / 60) . " minutes ago";
    } elseif ($timeDiff < 86400) {
        return floor($timeDiff / 3600) . " hours ago";
    } else {
        return floor($timeDiff / 86400) . " days ago";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="assets/styles.css">
    <style>
        table {
            width: 95%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table th, table td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }
        table th {
            background-color: #007bff;
            color: white;
        }
        .filter-form {
            margin-bottom: 20px;
        }
        .filter-form select, .filter-form input {
            padding: 8px;
            margin-right: 10px;
        }
        .select-ip {
            width: 20px;
            height: 20px;
            cursor: pointer;
        }
        .pagination {
            margin-top: 20px;
            text-align: center;
        }
        .pagination a, .pagination span {
            display: inline-block;
            padding: 8px 12px;
            margin: 0 3px;
            text-decoration: none;
            color: white;
            background-color: #007bff;
            border-radius: 4px;
            transition: background-color 0.3s;
        }
        .pagination a:hover {
            background-color: #0056b3;
        }
        .pagination a.active {
            background-color: #0056b3;
            font-weight: bold;
            pointer-events: none;
        }
        .pagination span {
            background-color: transparent;
            color: #6c757d;
        }
        .filter-form input[list] {
    width: 200px;
    padding: 8px;
    margin-right: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
}
.filter-form input[list]:focus {
    border-color: #007bff;
    outline: none;
}
.clear-button {
    background-color: #dbb11a;
    color: white;
    padding: 8px 12px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    margin-left: 10px;
    transition: background-color 0.3s;
}
.filter-button {
    background-color: #33c917;
    color: white;
    padding: 8px 12px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    margin-left: 10px;
    transition: background-color 0.3s;
}

.clear-button:hover {
    background-color: #c82333;
}
.pagination {
    margin-top: 20px;
    text-align: center;
}

.pagination a, .pagination span {
    display: inline-block;
    padding: 8px 12px;
    margin: 0 3px;
    text-decoration: none;
    color: white;
    background-color: #007bff;
    border-radius: 4px;
    transition: background-color 0.3s;
}

.pagination a:hover {
    background-color: #0056b3;
}

.pagination a.active {
    background-color: #0056b3;
    font-weight: bold;
    pointer-events: none;
}

.pagination span {
    background-color: transparent;
    color: #6c757d;
}

tr.selected {
    background-color: #d4edda; /* Verde deschis */
}



    </style>
</head>
<body>
        <!-- Navbar inclus -->
    <?php include 'includes/nav_login.php'; ?>

    <div class="container">
        <h1>Proxy Dashboard</h1>
<form method="GET" class="filter-form">
    <!-- Dropdown pentru Type -->
    <input list="type-list" name="type" placeholder="Select or enter type" value="<?php echo isset($_GET['type']) ? htmlspecialchars($_GET['type']) : ''; ?>">
    <datalist id="type-list">
        <?php foreach ($types as $type): ?>
            <option value="<?php echo htmlspecialchars($type); ?>">
        <?php endforeach; ?>
    </datalist>

    <!-- Dropdown și input pentru Country -->
    <input list="country-list" name="country" placeholder="Select or enter country" value="<?php echo isset($_GET['country']) ? htmlspecialchars($_GET['country']) : ''; ?>">
    <datalist id="country-list">
        <?php foreach ($countries as $country): ?>
            <option value="<?php echo htmlspecialchars($country); ?>">
        <?php endforeach; ?>
    </datalist>

    <!-- Dropdown și input pentru City -->
    <input list="city-list" name="city" placeholder="Select or enter city" value="<?php echo isset($_GET['city']) ? htmlspecialchars($_GET['city']) : ''; ?>">
    <datalist id="city-list">
        <?php foreach ($cities as $city): ?>
            <option value="<?php echo htmlspecialchars($city); ?>">
        <?php endforeach; ?>
    </datalist>

    <!-- Dropdown și input pentru ASN -->
    <input list="asn-list" name="asn" placeholder="Select or enter ASN" value="<?php echo isset($_GET['asn']) ? htmlspecialchars($_GET['asn']) : ''; ?>">
    <datalist id="asn-list">
        <?php foreach ($asns as $asn): ?>
            <option value="<?php echo htmlspecialchars($asn); ?>">
        <?php endforeach; ?>
    </datalist>

    <!-- Dropdown și input pentru Organization -->
    <input list="organization-list" name="organization" placeholder="Select or enter organization" value="<?php echo isset($_GET['organization']) ? htmlspecialchars($_GET['organization']) : ''; ?>">
    <datalist id="organization-list">
        <?php foreach ($organizations as $organization): ?>
            <option value="<?php echo htmlspecialchars($organization); ?>">
        <?php endforeach; ?>
    </datalist>

    <!-- Butoane de acțiune -->
    <button type="submit" class="filter-button">Filter</button>
    <button type="button" class="clear-button" onclick="window.location.href='dashboard.php';">Clear</button>
</form>

</form>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Type</th>
            <th>Country</th>
            <th>City</th>
            <th>IP:Port</th>
            <th>ASN</th>
            <th>Organization</th>
            <th>Added Ago</th>
            <th>Action</th>
        </tr>
    </thead>
<tbody>
    <?php foreach ($proxies as $proxy): ?>
        <tr id="row-<?php echo htmlspecialchars($proxy['id']); ?>">
            <td><?php echo htmlspecialchars($proxy['id']); ?></td>
            <td><?php echo htmlspecialchars($proxy['type']); ?></td>
            <td><?php echo htmlspecialchars($proxy['country']); ?></td>
            <td><?php echo htmlspecialchars($proxy['city']); ?></td>
            <td><?php echo substr($proxy['ip'], 0, 4) . '***:' . $proxy['port']; ?></td>
            <td><?php echo htmlspecialchars($proxy['asn']); ?></td>
            <td><?php echo strlen($proxy['organization']) > 25 
                     ? htmlspecialchars(substr($proxy['organization'], 0, 25)) . '...' 
                     : htmlspecialchars($proxy['organization']); ?></td>
            <td><?php echo timeAgo($proxy['date_added']); ?></td>
            <td>
                <input type="checkbox" class="select-item" data-id="<?php echo htmlspecialchars($proxy['id']); ?>" />
            </td>
        </tr>
    <?php endforeach; ?>
</tbody>



</table>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Găsește toate checkbox-urile
    const checkboxes = document.querySelectorAll('.select-item');

    // Adaugă eveniment pentru fiecare checkbox
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function () {
            const rowId = `row-${this.getAttribute('data-id')}`;
            const row = document.getElementById(rowId);
            const proxyId = this.getAttribute('data-id');
            const action = this.checked ? 'add' : 'remove'; // Determină acțiunea (adăugare/scoatere)

            // Schimbă culoarea rândului
            if (this.checked) {
                row.classList.add('selected'); // Adaugă clasa pentru schimbarea culorii
            } else {
                row.classList.remove('selected'); // Elimină clasa
            }

            // Trimite cererea AJAX pentru a actualiza coșul
            updateCart(proxyId, action);
        });
    });

    // Funcție pentru actualizarea coșului prin AJAX
    function updateCart(id, action) {
        fetch('update_cart.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ id: id, action: action }),
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Failed to update cart');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    updateCartCount(); // Actualizează numărul articolelor din coș
                } else {
                    console.error('Error updating cart:', data.message);
                }
            })
            .catch(error => {
                console.error('Error updating cart:', error);
            });
    }

    // Funcție pentru actualizarea numărului de articole din coș
    function updateCartCount() {
        fetch('cart_count.php')
            .then(response => response.json())
            .then(data => {
                const cartCountElement = document.getElementById('cart-count');
                cartCountElement.textContent = data.count || 0; // Actualizează textul
            })
            .catch(error => {
                console.error('Error fetching cart count:', error);
            });
    }

    // Actualizează numărul de articole din coș la încărcarea paginii
    updateCartCount();
});

</script>
    


    <div class="pagination">
        <?php if ($page > 1): ?>
            <a href="?page=1">First</a>
            <a href="?page=<?php echo $page - 1; ?>">Previous</a>
        <?php endif; ?>

        <?php
        $start = max(1, $page - 2);
        $end = min($total_pages, $page + 2);
        if ($start > 1) echo '<span>...</span>';
        for ($i = $start; $i <= $end; $i++): ?>
            <a href="?page=<?php echo $i; ?>" class="<?php echo $i === $page ? 'active' : ''; ?>"><?php echo $i; ?></a>
        <?php endfor;
        if ($end < $total_pages) echo '<span>...</span>';
        ?>

        <?php if ($page < $total_pages): ?>
            <a href="?page=<?php echo $page + 1; ?>">Next</a>
            <a href="?page=<?php echo $total_pages; ?>">Last</a>
        <?php endif; ?>
    </div>
    <div>
    <!-- Footer inclus -->
    <?php include 'includes/footer.php'; ?>
    </div>
</body>
</html>
