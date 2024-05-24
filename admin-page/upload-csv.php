<h2>העלאת סיפורים CSV</h2>
<form method="post" enctype="multipart/form-data">
    <input type="file" name="csv_file" accept=".csv" />
    <input type="submit" name="submit" value="העלה CSV" />
</form>

<?php
if (!isset($_FILES['csv_file']['tmp_name']) || empty($_FILES['csv_file']['tmp_name'])) {
    echo "נא להעלות קובץ CSV.";
    return;
}

$csv_file_path = $_FILES['csv_file']['tmp_name'];

$handle = fopen($csv_file_path, "r");

if ($handle === FALSE) {
    echo "שגיאה בפתיחת קובץ CSV.";
    return;
}

global $wpdb;
$table = $wpdb->prefix . 'stories';

$query = "INSERT INTO $table (name, url, is_new, is_phone) VALUES ";
$values = [];

while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
    if (count($data) != 4) {
        echo "שגיאה: מספר העמודות בקובץ CSV לא תקין.";
        fclose($handle);
        return;
    }

    $values[] = $wpdb->prepare("(%s, %s, %d, %d)", $data[0], $data[1], $data[2] == 1, $data[3] == 1);
}

if (!empty($values)) {
    array_reverse($values);
    $query .= implode(", ", $values);
    $result = $wpdb->query($query);
    if ($result === FALSE) {
        echo "שגיאה בביצוע השאילתה למסד הנתונים.";
    }
}

fclose($handle);
?>
