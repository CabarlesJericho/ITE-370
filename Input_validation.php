<?php
require_once 'Input_validation.php';

$validator = new InputValidation($_POST);

// Custom rule 
$validator->addRule('strong_password', function($value) {
    return strlen($value) >= 12;
}, 'Password must be 12+ characters.');

// Define validation rules
$rules = [
    'name' => 'required|alpha|min:2|max:50',
    'email' => 'required|email|unique:users,email',
    'age' => 'required|integer|min:18|max:100',
    'password' => 'required|password|min:8',
    'confirm_password' => 'required|same:password',
    'website' => 'required|url',
    'bio' => 'required|max:500',
    'gender' => 'required|in:male,female,other',
    'avatar' => 'image|max_file:2048',
    'terms' => 'required',
];

// Custom messages
$messages = [
    'name.required' => 'Please enter your full name.',
    'email.email' => 'Please enter a valid email address.',
];

if ($_POST && $validator->validate($rules, $messages)) {
    $cleanData = $validator->validated();
    echo "<h2 style='color:green'>✅ Validation Passed!</h2>";
    echo "<pre>" . print_r($cleanData, true) . "</pre>";
} elseif ($_POST) {
    echo "<h2 style='color:red'>❌ Validation Failed!</h2>";
    echo "<ul>";
    foreach ($validator->errors() as $field => $errors) {
        foreach ($errors as $error) {
            echo "<li>$field: $error</li>";
        }
    }
    echo "</ul>";
}
?>

<!DOCTYPE html>
<html>
<body>
<h2>Form with Validation</h2>
<form method="POST">
    <label>Name: <input name="name" value="<?= $_POST['name'] ?? '' ?>"></label><br><br>
    <label>Email: <input name="email" type="email" value="<?= $_POST['email'] ?? '' ?>"></label><br><br>
    <label>Age: <input name="age" type="number" value="<?= $_POST['age'] ?? '' ?>"></label><br><br>
    <label>Password: <input name="password" type="password"></label><br><br>
    <label>Confirm Password: <input name="confirm_password" type="password"></label><br><br>
    <label>Website: <input name="website" value="<?= $_POST['website'] ?? '' ?>"></label><br><br>
    <label>Bio: <textarea name="bio"><?= $_POST['bio'] ?? '' ?></textarea></label><br><br>
    <label>Gender:
        <select name="gender">
            <option value="male">Male</option>
            <option value="female">Female</option>
            <option value="other">Other</option>
        </select>
    </label><br><br>
    <label>Avatar: <input type="file" name="avatar"></label><br><br>
    <label><input type="checkbox" name="terms" value="1"> Accept Terms</label><br><br>
    <button type="submit">Submit</button>
</form>
</body>
</html>
