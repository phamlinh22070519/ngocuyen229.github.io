<?php
//Should fix the security concern here.
if(isset($_COOKIE['username'])){
    $username = $_COOKIE['username'];
    echo "Welcome " . $username . "<br/>";
?>
<form action="removeCookie.php" method="post">
    <input type="submit" value="Remove cookie"/>
</form>
<?php
}else{
?>
<form action="setCookie.php" method="post">
    User name: <input type="text" name="username"/>
    <input type="submit"/>
</form>
<?php
}
?>

<?php
if (isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW'])) {
    echo 'Welcome User: ' . $_SERVER['PHP_AUTH_USER'] .
         ' Password: ' . $_SERVER['PHP_AUTH_PW'];
} else {
    //Note also that header must be sent before any html
    //So you shouldn't have any HTML before the <?php tag for this file
    header('WWW-Authenticate: Basic realm="Restricted Section"');
    header("HTTP/1.0 401 Unauthorized");
    die("Please enter your username and password");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donor Management</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <header>
        <h1>Donor Management</h1>

        <button onclick="window.location.href='add_donor.php'">+ Add Donor</button>

        <a href="logout.php" class="logout-btn">Logout</a>
    </header>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Code</th>
                <th>Name</th>
                <th>Blood Type</th>
                <th>Phone Number</th>
                <th>Status</th>
                <th></th>
            </tr>
        </thead>

        <tbody>
            <?php if (empty($donors)): ?>
                <tr><td colspan="7">No donor data available.</td></tr>
            <?php else: ?>
                <?php foreach ($donors as $index => $donor): ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><?= htmlspecialchars($donor['code']) ?></td>
                        <td><?= htmlspecialchars($donor['name']) ?></td>
                        <td><?= htmlspecialchars($donor['blood_type']) ?></td>
                        <td><?= htmlspecialchars($donor['phone_number']) ?></td>
                        <td><?= htmlspecialchars($donor['status']) ?></td>
                        <td>
                            <a href="edit_donor.php?id=<?= $donor['id'] ?>">Edit</a> |
                            <a href="delete_donor.php?id=<?= $donor['id'] ?>" onclick="return confirm('Are you sure to delete this donor?')">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
