<?php
define("ALBUMS_FILE", "albums.json");
define("ALBUMS_IMAGES_DIR", "uploads/");

// Ensure the images directory exists
if (!is_dir(ALBUMS_IMAGES_DIR)) {
    mkdir(ALBUMS_IMAGES_DIR, 0777, true);
}

function getAlbums() {
    if (!file_exists(ALBUMS_FILE)) {
        file_put_contents(ALBUMS_FILE, json_encode([]));
    }
    $data = file_get_contents(ALBUMS_FILE);
    return json_decode($data, true);
}

function saveAlbums($albums) {
    file_put_contents(ALBUMS_FILE, json_encode($albums, JSON_PRETTY_PRINT));
}

/*function addAlbum($title, $description, $accessibility, $userId, $images = []) {
    $albums = getAlbums();
    $newAlbum = [
        "Album_Id" => uniqid(),
        "Title" => $title,
        "Description" => $description,
        "Accessibility_Code" => $accessibility,
        "Owner_Id" => $userId,
        "Images" => $images
    ];
    $albums[] = $newAlbum;
    saveAlbums($albums);
}*/


function addAlbum($title, $description, $accessibility, $userId, $uploadedImages = [], $extraFields = []) {
    $albums = getAlbums();
    $newAlbum = [
        "Album_Id" => uniqid(),
        "Title" => $title,
        "Description" => $description,
        "Accessibility_Code" => $accessibility,
        "Owner_Id" => $userId,
        "Images" => $uploadedImages,
        // Add extra fields
        "Type" => $extraFields['type'] ?? '',
        "Grades" => $extraFields['grades'] ?? '',
        "Province" => $extraFields['province'] ?? '',
        "City" => $extraFields['city'] ?? '',
        "Gender" => $extraFields['gender'] ?? '',
        "StudentPopulation" => $extraFields['studentPopulation'] ?? '',
        "BoardingPopulation" => $extraFields['boardingPopulation'] ?? '',
        "NumberOfSchools" => $extraFields['numberOfSchools'] ?? '',
        "Cost" => $extraFields['cost'] ?? '',
        "ClassSize" => $extraFields['classSize'] ?? '',
        "Language" => $extraFields['language'] ?? '',
        "Programs" => $extraFields['programs'] ?? []
    ];
    $albums[] = $newAlbum;
    saveAlbums($albums);
}

function deleteAlbum($albumId) {
    $albums = getAlbums();
    $albums = array_filter($albums, fn($album) => $album['Album_Id'] !== $albumId);
    saveAlbums($albums);
}

function getUserAlbums($userId) {
    $albums = getAlbums();
    return array_filter($albums, fn($album) => $album['Owner_Id'] === $userId);
}

/*function updateAlbum($albumId, $title, $description, $accessibility, $images = []) {
    $albums = getAlbums();
    foreach ($albums as &$album) {
        if ($album['Album_Id'] === $albumId) {
            $album['Title'] = $title;
            $album['Description'] = $description;
            $album['Accessibility_Code'] = $accessibility;
            if (!empty($images)) {
                $album['Images'] = array_merge($album['Images'] ?? [], $images);
            }
            break;
        }
    }
    saveAlbums($albums);
}*/


function updateAlbum($albumId, $title, $description, $accessibility, $newImages = [], $extraFields = []) {
    $albums = getAlbums(); // Fetch all albums
    foreach ($albums as &$album) {
        if ($album['Album_Id'] === $albumId) {
            // Update basic fields
            $album['Title'] = $title;
            $album['Description'] = $description;
            $album['Accessibility_Code'] = $accessibility;

            // Merge new images with existing images
            if (!empty($newImages)) {
                $album['Images'] = array_merge($album['Images'] ?? [], $newImages);
            }

            // Update extra fields
            $album['Type'] = $extraFields['type'] ?? '';
            $album['Grades'] = $extraFields['grades'] ?? '';
            $album['Province'] = $extraFields['province'] ?? '';
            $album['City'] = $extraFields['city'] ?? '';
            $album['Gender'] = $extraFields['gender'] ?? '';
            $album['StudentPopulation'] = $extraFields['studentPopulation'] ?? '';
            $album['BoardingPopulation'] = $extraFields['boardingPopulation'] ?? '';
            $album['NumberOfSchools'] = $extraFields['numberOfSchools'] ?? '';
            $album['Cost'] = $extraFields['cost'] ?? '';
            $album['ClassSize'] = $extraFields['classSize'] ?? '';
            $album['Language'] = $extraFields['language'] ?? '';
            $album['Programs'] = $extraFields['programs'] ?? [];
            break;
        }
    }
    saveAlbums($albums); // Save the updated albums list back to the JSON file
}


function uploadImages($files) {
    $uploadedImages = [];
    foreach ($files['name'] as $key => $name) {
        $tmpName = $files['tmp_name'][$key];
        $targetFile = ALBUMS_IMAGES_DIR . uniqid() . '_' . basename($name);
        if (move_uploaded_file($tmpName, $targetFile)) {
            $uploadedImages[] = $targetFile;
        }
    }
    return $uploadedImages;
}


function deleteAlbumImage($albumId, $imagePath) {
    $albums = getAlbums();
    foreach ($albums as &$album) {
        if ($album['Album_Id'] === $albumId) {
            $album['Images'] = array_filter($album['Images'], fn($img) => $img !== $imagePath);
            if (file_exists($imagePath)) {
                unlink($imagePath); // Delete the file from the filesystem
            }
            break;
        }
    }
    saveAlbums($albums);
}



define("USERS_FILE", "users.json");

// Retrieve all users from the JSON file
function getAllUsers() {
    if (!file_exists(USERS_FILE)) {
        file_put_contents(USERS_FILE, json_encode([])); // Initialize empty JSON file
    }
    $data = file_get_contents(USERS_FILE);
    return json_decode($data, true);
}

// Save users to the JSON file
function saveUsers($users) {
    file_put_contents(USERS_FILE, json_encode($users, JSON_PRETTY_PRINT));
}

// Get a user by their ID
function getUserById($id) {
    $users = getAllUsers();
    foreach ($users as $user) {
        if ($user['id'] === $id) {
            return $user;
        }
    }
    return null;
}

// Add a new user to the JSON file
function addNewUser($id, $name, $phone, $password) {
    $users = getAllUsers();
    $users[] = [
        "id" => $id,
        "name" => $name,
        "phone" => $phone,
        "password" => $password // Store the hashed password
    ];
    saveUsers($users);
}


// Get user by ID and verify password
function getUserByIdAndPassword($id, $password) {
    $users = getAllUsers();
    foreach ($users as $user) {
        if ($user['id'] === $id && password_verify($password, $user['password'])) {
            return $user; // Return user if credentials match
        }
    }
    return null; // Return null if no match
}

?>


