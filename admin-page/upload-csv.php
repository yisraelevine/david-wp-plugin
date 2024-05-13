<form method="post" enctype="multipart/form-data">
    <input type="file" name="csv_file" accept=".csv" />
    <input type="submit" name="submit" value="Upload CSV" />
</form>

<?php
if (isset($_FILES['csv_file']['tmp_name'])) {
    $csv_file_path = $_FILES['csv_file']['tmp_name'];

    global $wpdb;
    $table_name = $wpdb->prefix . 'stories';

    if (($handle = fopen($csv_file_path, "r")) !== FALSE) {
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            $wpdb->insert(
                $table_name,
                array(
                    'name' => $data[0],
                    'url' => $data[1],
                    'new' => $data[2] === 1,
                    'phone' => $data[3] === 1
                )
            );
        }
        fclose($handle);
    }
}