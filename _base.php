<?php

// ============================================================================
// PHP Setups
// ============================================================================

date_default_timezone_set('Asia/Kuala_Lumpur');
session_start();
// TODO

// ============================================================================
// General Page Functions
// ============================================================================

// Is GET request?
function is_get()
{
    return $_SERVER['REQUEST_METHOD'] == 'GET';
}

// Is POST request?
function is_post()
{
    return $_SERVER['REQUEST_METHOD'] == 'POST';
}

// Obtain GET parameter
function get($key, $value = null)
{
    $value = $_GET[$key] ?? $value;
    return is_array($value) ? array_map('trim', $value) : trim($value);
}

// Obtain POST parameter
function post($key, $value = null)
{
    $value = $_POST[$key] ?? $value;
    return is_array($value) ? array_map('trim', $value) : trim($value);
}

// Obtain REQUEST (GET and POST) parameter
function req($key, $value = null)
{
    $value = $_REQUEST[$key] ?? $value;
    return is_array($value) ? array_map('trim', $value) : trim($value);
}

// Redirect to URL
function redirect($url = null)
{
    $url ??= $_SERVER['REQUEST_URI'];
    header("Location: $url");
    exit();
}


// Set or get temporary session variable
function temp($key, $value = null)
{
    if ($value !== null) {
        $_SESSION["temp_$key"] = $value;
    } else {
        $value = $_SESSION["temp_$key"] ?? null;
        unset($_SESSION["temp_$key"]);
        return $value;
    }
}


// ============================================================================
// HTML Helpers
// ============================================================================

// Encode HTML special characters
function encode($value)
{
    return htmlentities($value);
}


// Generate <input type='text'>
function html_text_type($type, $key, $class_style, $attr = '')
{
    $value = encode($GLOBALS[$key] ?? "");
    echo "<input type='$type' class='$class_style' id='$key' name='$key' value='$value' $attr/>";
}

// Generate <input type='text'>
function html_textarea($key, $rows, $cols, $attr = '')
{
    $value = encode($GLOBALS[$key] ?? "");
    echo "<textarea id='$key' name='$key' rows='$rows' cols='$cols' value='$value' $attr></textarea>";
}



// Generate <input type='radio'> list
function html_radios($key, $items, $br = false)
{
    $value = encode($GLOBALS[$key] ?? '');
    echo '<div>';
    foreach ($items as $id => $text) {
        $state = $id == $value ? 'checked' : '';
        echo "<label><input type='radio' id='{$key}_$id' name='$key' value='$id' $state>$text</label>";
        if ($br) {
            echo '<br>';
        }
    }
    echo '</div>';
}

// Generate <select>
function html_select($key, $items, $default = '- Select One -', $attr = '')
{
    $value = encode($GLOBALS[$key] ?? '');
    echo "<select id='$key' name='$key' $attr>";
    if ($default !== null) {
        echo "<option value=''>$default</option>";
    }
    foreach ($items as $id => $text) {
        $state = $id == $value ? 'selected' : '';
        echo "<option value='$id' $state>$text</option>";
    }
    echo '</select>';
}

// ============================================================================
// Error Handlings
// ============================================================================


// Global error array
$_err = [];


// Generate <span class='err'>
function err($key, $id_style)
{
    global $_err;

    if ($_err[$key] ?? false) {
        echo "
        <div class='invalid' id='{$id_style}'  style='display:block;'>
            <i class='fa fa-exclamation-circle'></i>
            {$_err[$key]}
        </div>
        ";
    } else {
        echo "
        <div class='invalid' id='{$id_style}'>
        </div>
        ";
    }
}

// ============================================================================
// Database Setups and Functions
// ============================================================================

// Global PDO object

$_db = new PDO('mysql:dbname=smart', 'root', '', [
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
]);

// Function to fetch the next ID value with a prefix
function getNextIdWithPrefix($table, $idField, $prefix, $length = 2)
{
    global $_db;
    // Prepare the query to get the maximum ID with the given prefix
    $stm = $_db->prepare("SELECT MAX($idField) FROM $table WHERE $idField LIKE ?");
    $stm->execute([$prefix . '%']);
    $maxId = $stm->fetchColumn();
    
    // Extract the numeric part and increment it
    if ($maxId) {
        // Remove the prefix and zero-pad the number to the specified length
        $numericPart = substr($maxId, strlen($prefix));
        $nextNumeric = str_pad((int)$numericPart + 1, $length, '0', STR_PAD_LEFT);
    } else {
        // Start with the initial numeric part if no records found
        $nextNumeric = str_pad(1, $length, '0', STR_PAD_LEFT);
    }
    
    return $prefix . $nextNumeric;
}
// Is unique?
function is_unique($value, $table, $field)
{
    global $_db;
    $stm = $_db->prepare("SELECT COUNT(*) FROM $table WHERE $field = ?");
    $stm->execute([$value]);
    return $stm->fetchColumn() == 0;
}

// Is exists?
function is_exists($value, $table, $field)
{
    global $_db;
    $stm = $_db->prepare("SELECT COUNT(*) FROM $table WHERE $field = ?");
    $stm->execute([$value]);
    return $stm->fetchColumn() > 0;
}



// ============================================================================
// Global Constants and Variables
// ============================================================================

$_genders = [
    'F' => 'Female',
    'M' => 'Male',
];

$_programs = [
    'RDS' => 'Data Science',
    'REI' => 'Enterprise Information Systems',
    'RIS' => 'Information Security',
    'RSD' => 'Software Systems Development',
    'RST' => 'Interactive Software Technology',
    'RSW' => 'Software Engineering',
];
