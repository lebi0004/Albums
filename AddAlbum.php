<?php
include 'functions.php';
include("./common/header.php");

// Initialize variables
$isEditing = false;
$title = "";
$description = "";
$accessibility = "";
$errors = [];
$albumId = "";
$images = [];
$type = "";
$grades = "";
$province = "";
$city = "";
$gender = "";
$studentPopulation = "";
$boardingPopulation = "";
$numberOfSchools = "";
$cost = "";
$classSize = "";
$language = "";
$programs = [];

// Check if editing an existing album
if (isset($_GET['albumId'])) {
    $isEditing = true;
    $albumId = $_GET['albumId'];
    $albums = getAlbums();
    $album = array_filter($albums, fn($album) => $album['Album_Id'] === $albumId);

    if (!empty($album)) {
        $album = current($album);
        $title = $album['Title'];
        $description = $album['Description'];
        $accessibility = $album['Accessibility_Code'];
        $images = $album['Images'] ?? [];
        $type = $album['Type'] ?? "";
        $grades = $album['Grades'] ?? "";
        $province = $album['Province'] ?? "";
        $city = $album['City'] ?? "";
        $gender = $album['Gender'] ?? "";
        $studentPopulation = $album['StudentPopulation'] ?? "";
        $boardingPopulation = $album['BoardingPopulation'] ?? "";
        $numberOfSchools = $album['NumberOfSchools'] ?? "";
        $cost = $album['Cost'] ?? "";
        $classSize = $album['ClassSize'] ?? "";
        $language = $album['Language'] ?? "";
        $programs = $album['Programs'] ?? [];
    }
}

// Handle image deletion
if (isset($_GET['deleteImage']) && $isEditing) {
    $imagePath = $_GET['deleteImage'];
    deleteAlbumImage($albumId, $imagePath);
    header("Location: AddAlbum.php?albumId=$albumId");
    exit;
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $accessibility = $_POST['accessibility'] ?? 'public';
    $uploadedImages = uploadImages($_FILES['images'] ?? []);

    // Gather extra fields
    $extraFields = [
        'type' => $_POST['type'] ?? '',
        'grades' => $_POST['grades'] ?? '',
        'province' => $_POST['province'] ?? '',
        'city' => $_POST['city'] ?? '',
        'gender' => $_POST['gender'] ?? '',
        'studentPopulation' => $_POST['studentPopulation'] ?? '',
        'boardingPopulation' => $_POST['boardingPopulation'] ?? '',
        'numberOfSchools' => $_POST['numberOfSchools'] ?? '',
        'cost' => $_POST['cost'] ?? '',
        'classSize' => $_POST['classSize'] ?? '',
        'language' => $_POST['language'] ?? '',
        'programs' => $_POST['programs'] ?? []
    ];

    if (empty($title)) {
        $errors[] = "School Name is required.";
    }

    if (empty($errors)) {
        $userId = 'User123'; // Replace with dynamic user ID
        if ($isEditing && isset($albumId)) {
            updateAlbum($albumId, $title, $description, $accessibility, $uploadedImages, $extraFields);
            echo "School updated successfully!";
        } else {
            addAlbum($title, $description, $accessibility, $userId, $uploadedImages, $extraFields);
            echo "School added successfully!";
        }

        // Redirect back to MyAlbums page after submission
        header("Location: MyAlbums.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $isEditing ? 'Edit School' : 'Add New School'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="shadow p-4 mb-5 bg-body-tertiary rounded">
            <h1 class="text-center mb-4"><?php echo $isEditing ? 'Edit School' : 'Add New School'; ?></h1>
            <?php if ($errors): ?>
                <div class="alert alert-danger">
                    <?php foreach ($errors as $error): ?>
                        <p><?php echo $error; ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <!-- Form Starts Here -->
            <form action="AddAlbum.php<?php echo $isEditing ? '?albumId=' . $albumId : ''; ?>" method="post" enctype="multipart/form-data">
                <div class="row mb-4">
                    <!-- School Images -->
                    <div class="col-md-12 mb-3">
                        <label class="form-label">School Images:</label>
                        <div class="row">
                            <?php foreach ($images as $image): ?>
                                <div class="col-md-2">
                                    <img src="<?php echo htmlspecialchars($image); ?>" class="img-thumbnail mb-2" alt="School Image">
                                    <a href="AddAlbum.php?albumId=<?php echo $albumId; ?>&deleteImage=<?php echo urlencode($image); ?>" class="btn btn-danger btn-sm">Delete</a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <input type="file" class="form-control" id="images" name="images[]" multiple>
                    </div>
                </div>

                <!-- School Name -->
                <div class="mb-3">
                    <label for="title" class="form-label">School Name:</label>
                    <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($title); ?>" required>
                </div>

                <div class="row">
                    <!-- Left Column -->
                    <div class="col-md-6">
                        <!-- Type of School -->
                        <div class="mb-3">
                            <label for="type" class="form-label">Type of School:</label>
                            <select class="form-select" id="type" name="type">
                                <option value="Boarding School" <?php echo $type === 'Boarding School' ? 'selected' : ''; ?>>Boarding School</option>
                                <option value="Private Day School" <?php echo $type === 'Private Day School' ? 'selected' : ''; ?>>Private Day School</option>
                                <option value="Public School" <?php echo $type === 'Public School' ? 'selected' : ''; ?>>Public School</option>
                            </select>
                        </div>

                        <!-- Grades -->
                        <div class="mb-3">
                            <label for="grades" class="form-label">Grades:</label>
                            <select class="form-select" id="grades" name="grades">
                                <option value="K-12" <?php echo $grades === 'K-12' ? 'selected' : ''; ?>>K-12</option>
                                <option value="6-12" <?php echo $grades === '6-12' ? 'selected' : ''; ?>>6-12</option>
                                <option value="9-12" <?php echo $grades === '9-12' ? 'selected' : ''; ?>>9-12</option>
                                <option value="9-11" <?php echo $grades === '9-11' ? 'selected' : ''; ?>>9-11</option>
                                <option value="K-11" <?php echo $grades === 'K-11' ? 'selected' : ''; ?>>K-11</option>
                            </select>
                        </div>

                        <!-- Province -->
                        <div class="mb-3">
                            <label for="province" class="form-label">Province:</label>
                            <select class="form-select" id="province" name="province">
                                <option value="Ontario" <?php echo $province === 'Ontario' ? 'selected' : ''; ?>>Ontario</option>
                                <option value="Quebec" <?php echo $province === 'Quebec' ? 'selected' : ''; ?>>Quebec</option>
                                <option value="British Columbia" <?php echo $province === 'British Columbia' ? 'selected' : ''; ?>>British Columbia</option>
                                <option value="Alberta" <?php echo $province === 'Alberta' ? 'selected' : ''; ?>>Alberta</option>
                            </select>
                        </div>

                        <!-- City -->
                        <div class="mb-3">
                            <label for="city" class="form-label">City:</label>
                            <input type="text" class="form-control" id="city" name="city" value="<?php echo htmlspecialchars($city); ?>" required>
                        </div>

                        <!-- Gender -->
                        <div class="mb-3">
                            <label for="gender" class="form-label">Gender:</label>
                            <select class="form-select" id="gender" name="gender">
                                <option value="Coed" <?php echo $gender === 'Coed' ? 'selected' : ''; ?>>Coed</option>
                                <option value="All-boy" <?php echo $gender === 'All-boy' ? 'selected' : ''; ?>>All-boy</option>
                                <option value="All-girl" <?php echo $gender === 'All-girl' ? 'selected' : ''; ?>>All-girl</option>
                            </select>
                        </div>

                        <!-- Entire Student Population -->
                        <div class="mb-3">
                            <label for="studentPopulation" class="form-label">Entire Student Population:</label>
                            <input type="number" class="form-control" id="studentPopulation" name="studentPopulation" value="<?php echo htmlspecialchars($studentPopulation); ?>">
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="col-md-6">
                        <!-- Boarding Student Population -->
                        <div class="mb-3">
                            <label for="boardingPopulation" class="form-label">Boarding Student Population:</label>
                            <input type="number" class="form-control" id="boardingPopulation" name="boardingPopulation" value="<?php echo htmlspecialchars($boardingPopulation); ?>">
                        </div>

                        <!-- Number of Schools -->
                        <div class="mb-3">
                            <label for="numberOfSchools" class="form-label">Number of Schools:</label>
                            <input type="number" class="form-control" id="numberOfSchools" name="numberOfSchools" value="<?php echo htmlspecialchars($numberOfSchools); ?>">
                        </div>

                        <!-- Cost Per Year -->
                        <div class="mb-3">
                            <label for="cost" class="form-label">Cost Per Year:</label>
                            <input type="text" class="form-control" id="cost" name="cost" value="<?php echo htmlspecialchars($cost); ?>">
                        </div>

                        <!-- Average Class Size -->
                        <div class="mb-3">
                            <label for="classSize" class="form-label">Average Class Size:</label>
                            <input type="text" class="form-control" id="classSize" name="classSize" value="<?php echo htmlspecialchars($classSize); ?>">
                        </div>

                        <!-- Language of Instruction -->
                        <div class="mb-3">
                            <label for="language" class="form-label">Language of Instruction:</label>
                            <select class="form-select" id="language" name="language">
                                <option value="English" <?php echo $language === 'English' ? 'selected' : ''; ?>>English</option>
                                <option value="French" <?php echo $language === 'French' ? 'selected' : ''; ?>>French</option>
                            </select>
                        </div>

                        <!-- Academic Programs -->
                        <div class="mb-3">
                            <label for="programs" class="form-label">Academic Programs Available:</label>
                            <div>
                                <input type="checkbox" id="regularCurriculum" name="programs[]" value="Regular Curriculum" <?php echo in_array('Regular Curriculum', $programs) ? 'checked' : ''; ?>>
                                <label for="regularCurriculum">Regular Curriculum</label>
                            </div>
                            <div>
                                <input type="checkbox" id="apCourses" name="programs[]" value="AP Courses" <?php echo in_array('AP Courses', $programs) ? 'checked' : ''; ?>>
                                <label for="apCourses">AP Courses</label>
                            </div>
                            <div>
                                <input type="checkbox" id="ib" name="programs[]" value="International Baccalaureate (IB)" <?php echo in_array('International Baccalaureate (IB)', $programs) ? 'checked' : ''; ?>>
                                <label for="ib">International Baccalaureate (IB)</label>
                            </div>
                            <div>
                                <input type="checkbox" id="ibPrep" name="programs[]" value="IB Prep" <?php echo in_array('IB Prep', $programs) ? 'checked' : ''; ?>>
                                <label for="ibPrep">IB Prep</label>
                            </div>
                            <div>
                                <input type="checkbox" id="montessori" name="programs[]" value="Montessori" <?php echo in_array('Montessori', $programs) ? 'checked' : ''; ?>>
                                <label for="montessori">Montessori</label>
                            </div>
                            <div>
                                <input type="checkbox" id="summerProgram" name="programs[]" value="Summer Program" <?php echo in_array('Summer Program', $programs) ? 'checked' : ''; ?>>
                                <label for="summerProgram">Summer Program</label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit and Clear Buttons -->
                <div class="d-flex justify-content-between">
                    <button type="submit" class="btn btn-primary"><?php echo $isEditing ? 'Update School' : 'Add School'; ?></button>
                    <button type="reset" class="btn btn-secondary">Clear</button>
                </div>
            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
