<?php
include 'functions.php';
include("./common/header.php");

$userId = 'User123'; // Replace with dynamic user ID
$userAlbums = getUserAlbums($userId);

if (isset($_GET['delete'])) {
    $albumId = $_GET['delete'];
    deleteAlbum($albumId);
    header("Location: MyAlbums.php");
    exit;
}

// Retrieve filter values
$searchQuery = $_GET['search'] ?? '';
$filterAccessibility = $_GET['accessibility'] ?? '';

// Filter albums based on search and accessibility
$filteredAlbums = array_filter($userAlbums, function ($album) use ($searchQuery, $filterAccessibility) {
    $matchesSearch = empty($searchQuery) || stripos($album['Title'], $searchQuery) !== false || stripos($album['Description'], $searchQuery) !== false;
    $matchesAccessibility = empty($filterAccessibility) || $album['Accessibility_Code'] === $filterAccessibility;
    return $matchesSearch && $matchesAccessibility;
});
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Albums</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center mb-4">My Albums</h1>

        <!-- Search and Filter Form -->
        <form method="get" action="MyAlbums.php" class="mb-4">
            <div class="row">
                <!-- Search Field -->
                <div class="col-md-8 mb-2">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="Search for albums..." value="<?php echo htmlspecialchars($searchQuery); ?>">
                    </div>
                </div>
                <!-- Accessibility Filter -->
                <div class="col-md-4 mb-2">
                    <select name="accessibility" class="form-select">
                        <option value="">All Accessibility</option>
                        <option value="public" <?php echo $filterAccessibility === 'public' ? 'selected' : ''; ?>>Public</option>
                        <option value="private" <?php echo $filterAccessibility === 'private' ? 'selected' : ''; ?>>Private</option>
                    </select>
                </div>
            </div>
            <div class="text-end">
                <button type="submit" class="btn btn-primary">Apply Filters</button>
                <a href="MyAlbums.php" class="btn btn-secondary">Clear Filters</a>
            </div>
        </form>

        <div class="text-end mb-3">
            <a href="AddAlbum.php" class="btn btn-success">Add New Album</a>
        </div>

        <?php if ($filteredAlbums): ?>
            <div class="row">
                <?php foreach ($filteredAlbums as $album): ?>
                    <div class="col-md-12 mb-4">
                        <div class="card">
                            <div class="row g-0">
                                <!-- Album Images -->
                                <div class="col-md-4">
                                    <?php if (!empty($album['Images'])): ?>
                                        <div id="carousel-<?php echo $album['Album_Id']; ?>" class="carousel slide" data-bs-ride="carousel">
                                            <div class="carousel-inner">
                                                <?php foreach ($album['Images'] as $index => $image): ?>
                                                    <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                                                        <img src="<?php echo htmlspecialchars($image); ?>" class="d-block w-100 img-thumbnail" alt="Album Image">
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                            <!-- Carousel controls -->
                                            <?php if (count($album['Images']) > 1): ?>
                                                <button class="carousel-control-prev" type="button" data-bs-target="#carousel-<?php echo $album['Album_Id']; ?>" data-bs-slide="prev">
                                                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                                    <span class="visually-hidden">Previous</span>
                                                </button>
                                                <button class="carousel-control-next" type="button" data-bs-target="#carousel-<?php echo $album['Album_Id']; ?>" data-bs-slide="next">
                                                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                                    <span class="visually-hidden">Next</span>
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    <?php else: ?>
                                        <img src="placeholder.jpg" class="img-thumbnail" alt="No Image Available">
                                    <?php endif; ?>
                                </div>
                                <!-- Album Details -->
                                <div class="col-md-8">
                                    <div class="card-body">
                                        <h5 class="card-title"><?php echo htmlspecialchars($album['Title']); ?></h5>
                                        <!--<p class="card-text"><strong>Description:</strong> <?php echo htmlspecialchars($album['Description']); ?></p>-->
                                        <p class="card-text"><strong>Type of School:</strong> <?php echo htmlspecialchars($album['Type'] ?? 'N/A'); ?></p>
                                        <p class="card-text"><strong>Grades:</strong> <?php echo htmlspecialchars($album['Grades'] ?? 'N/A'); ?></p>
                                        <p class="card-text"><strong>Province:</strong> <?php echo htmlspecialchars($album['Province'] ?? 'N/A'); ?></p>
                                        <p class="card-text"><strong>City:</strong> <?php echo htmlspecialchars($album['City'] ?? 'N/A'); ?></p>
                                        <p class="card-text"><strong>Gender:</strong> <?php echo htmlspecialchars($album['Gender'] ?? 'N/A'); ?></p>
                                        <p class="card-text"><strong>Entire Student Population:</strong> <?php echo htmlspecialchars($album['StudentPopulation'] ?? 'N/A'); ?></p>
                                        <p class="card-text"><strong>Boarding Student Population:</strong> <?php echo htmlspecialchars($album['BoardingPopulation'] ?? 'N/A'); ?></p>
                                        <p class="card-text"><strong>Number of Schools:</strong> <?php echo htmlspecialchars($album['NumberOfSchools'] ?? 'N/A'); ?></p>
                                        <p class="card-text"><strong>Cost Per Year:</strong> <?php echo htmlspecialchars($album['Cost'] ?? 'N/A'); ?></p>
                                        <p class="card-text"><strong>Average Class Size:</strong> <?php echo htmlspecialchars($album['ClassSize'] ?? 'N/A'); ?></p>
                                        <p class="card-text"><strong>Language of Instruction:</strong> <?php echo htmlspecialchars($album['Language'] ?? 'N/A'); ?></p>
                                        <p class="card-text"><strong>Academic Programs:</strong> <?php echo !empty($album['Programs']) ? implode(', ', $album['Programs']) : 'N/A'; ?></p>
                                        <a href="AddAlbum.php?albumId=<?php echo $album['Album_Id']; ?>" class="btn btn-primary">Edit</a>
                                        <a href="MyAlbums.php?delete=<?php echo $album['Album_Id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this album?');">Delete</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-info">
                <p><?php echo $searchQuery || $filterAccessibility ? "No albums match your search or filter criteria." : "You have no albums yet."; ?></p>
            </div>
        <?php endif; ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
