<h2>העלאת סיפורים CSV</h2>
<form method="post" enctype="multipart/form-data">
    <input type="file" name="csv_file" accept=".csv" />
    <input type="submit" name="submit" value="העלה CSV" />
</form>

<?php
if (isset($_FILES['csv_file']['tmp_name']) && !empty($_FILES['csv_file']['tmp_name'])) {
    $csv_file_path = $_FILES['csv_file']['tmp_name'];

    if (($handle = fopen($csv_file_path, "r")) !== FALSE) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'stories';

        $sql = "INSERT INTO $table_name (name, url, new, phone) VALUES ";
        $value_placeholders = array();

        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            if (count($data) != 4) {
                continue;
            }

            $value_placeholders[] = $wpdb->prepare("(%s, %s, %d, %d)", $data[0], $data[1], $data[2] == 1, $data[3] == 1);

            if (count($value_placeholders) >= 1000) {
                $sql .= implode(", ", $value_placeholders);
                echo $sql;
                $wpdb->query($sql);
                $sql = "INSERT INTO $table_name (name, url, new, phone) VALUES ";
                $value_placeholders = array();
            }
        }

        if (!empty($value_placeholders)) {
            $sql .= implode(", ", $value_placeholders);
            echo $sql;
            $wpdb->query($sql);
        }

        fclose($handle);
    } else {
        echo "שגיאה בפתיחת קובץ CSV.";
    }
} else {
    echo "נא להעלות קובץ CSV.";
}
