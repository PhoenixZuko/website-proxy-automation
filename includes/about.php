<?php
// Obține pagina curentă din URL, implicit pagina 1
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

// Conținut pentru fiecare pagină
$pages = [
    1 => "
        <strong>Current State:</strong><br>
        The project has achieved significant progress and is now functional. The system is set up to execute commands on an external SSH server, 
        which processes proxies and automatically transfers a JSON file to the CPanel server. This automation is powered by <strong>cron jobs</strong>, 
        ensuring smooth and timely updates.<br><br>
        Currently, the SQL database integration is being tested to allow the system to load proxies automatically. 
        On the website, the system displays <strong>40 random proxies</strong> for free to visitors, offering them a quick and convenient experience.
    ",
    2 => "
        <strong>Future Enhancements:</strong><br>
        In the future, user authentication will be implemented. This simple login functionality will enable users to search for specific proxies. 
        Logged-in users will have the option to select <strong>up to 30 free proxies</strong> every 24 hours.<br><br>
        Beyond this, the project will introduce a premium section. Integrated with <strong>Bitcoin payment</strong>, the premium plan will grant users 
        unlimited access to the entire proxy database for a period of <strong>30 days</strong>. This will provide flexibility and scalability for 
        both free and paid users.
    ",
    3 => "
        <strong>Benefits of Automation:</strong><br>
        The automation of proxy updates and SQL integration not only ensures that the system operates smoothly but also minimizes manual intervention. 
        Users can rely on accurate, up-to-date proxy data displayed in real-time on the website.<br><br>
        Additionally, the future addition of advanced filtering options will enhance usability, allowing users to refine their search based on 
        criteria such as country, city, and type of proxy.
    ",
    4 => "
        <strong>Where We Are Now:</strong><br>
        At this stage, the system successfully executes commands on the SSH server to process proxies and update the JSON file in CPanel. 
        This process is managed via cron jobs and ensures that the data remains up-to-date.<br><br>
        Currently, SQL integration testing is ongoing to automate proxy loading into the database. 
        The site displays <strong>40 random proxies</strong> for free, enabling users to explore available proxies easily and quickly.<br><br>
        <strong>Future Plans:</strong>
        <ul>
            <li>Adding a <strong>login system</strong> for users.</li>
            <li>Providing logged-in users the ability to select <strong>up to 30 proxies</strong> every 24 hours.</li>
            <li>Integrating a premium section with <strong>Bitcoin payments</strong>, granting unlimited access to all proxies for 30 days.</li>
        </ul>
    ",
    5 => "
        <strong>Custom Proxy Search:</strong><br>
        Once the user authentication system is in place, logged-in users will be able to search for specific proxies tailored to their needs. 
        This feature will include filters such as:<br>
        <ul>
            <li><strong>Location:</strong> Country and city.</li>
            <li><strong>Type:</strong> SOCKS5, HTTP, HTTPS.</li>
            <li><strong>Speed and Latency:</strong> Proxy performance metrics.</li>
        </ul><br>
        These filters will make the system highly flexible, allowing users to refine their search and choose the best proxies for their requirements.
    ",
    6 => "
        <strong>Premium Access with Bitcoin Payments:</strong><br>
        The premium section will offer unlimited access to all proxies in the database for a period of <strong>30 days</strong>. 
        This feature will be integrated with <strong>Bitcoin payments</strong>, ensuring secure and anonymous transactions.<br><br>
        Benefits of premium access include:
        <ul>
            <li>Unlimited searches and downloads of proxy data.</li>
            <li>Priority access to the newest and fastest proxies.</li>
            <li>Advanced filtering options not available in the free version.</li>
        </ul><br>
        This plan ensures that both free and paid users have options tailored to their needs, making the system scalable and future-proof.
    ",
];


// Determină numărul total de pagini
$total_pages = count($pages);

// Asigură-te că pagina curentă este validă
if ($page < 1) $page = 1;
if ($page > $total_pages) $page = $total_pages;

// Obține conținutul paginii curente
$content = $pages[$page];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About the Project</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #565e66;
            color: #333;
        }
        .container {
            max-width: 800px;
            margin: auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            margin-top: 50px;
        }
        h1 {
            text-align: center;
            color: #007bff;
            margin-bottom: 20px;
        }
        p {
            line-height: 1.8;
            font-size: 18px;
            margin-bottom: 20px;
        }
        .pagination {
            text-align: center;
            margin-top: 20px;
        }
        .pagination a {
            display: inline-block;
            padding: 10px 15px;
            margin: 0 5px;
            text-decoration: none;
            background-color: #007bff;
            color: #fff;
            border-radius: 50px;
            font-size: 14px;
            transition: background-color 0.3s, transform 0.3s;
        }
        .pagination a:hover {
            background-color: #0056b3;
            transform: scale(1.1);
        }
        .pagination a[style] {
            background-color: #0056b3;
        }
        .slider {
            display: flex;
            justify-content: center;
            margin-top: 20px;
            gap: 15px;
        }
        .slider img {
            width: 150px;
            height: 150px;
            margin: 0 10px;
            border-radius: 12px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .slider img:hover {
            transform: scale(1.1);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
        }
    </style>
</head>
<body>
    <div class="container">
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <a href="https://vorte.eu/vorte.eu/proxies/" style="text-decoration: none; color: #007bff; font-size: 18px;">
        &larr; Back
    </a>
    <h1 style="margin: 0; text-align: center; flex: 1;">About the Project</h1>
    <a href="https://github.com/PhoenixZuko" target="_blank" style="text-decoration: none; color: #007bff; font-size: 16px;">
        available on my GitHub
    </a>
</div>
<p><?php echo $content; ?></p>

</p>
<p><?php echo $content; ?></p>

        <!-- Paginare -->
        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="?page=<?php echo $page - 1; ?>">&laquo; Previous</a>
            <?php endif; ?>
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="?page=<?php echo $i; ?>" <?php if ($i === $page) echo 'style="background-color: #0056b3;"'; ?>>
                    <?php echo $i; ?>
                </a>
            <?php endfor; ?>
            <?php if ($page < $total_pages): ?>
                <a href="?page=<?php echo $page + 1; ?>">Next &raquo;</a>
            <?php endif; ?>
        </div>

        <!-- Slider -->
        <div class="slider">
            <img src="https://via.placeholder.com/150?text=Feature+1" alt="Feature 1">
            <img src="https://via.placeholder.com/150?text=Feature+2" alt="Feature 2">
            <img src="https://via.placeholder.com/150?text=Feature+3" alt="Feature 3">
        </div>
    </div>
</body>
</html>
