<?php
// ����������� � ���� ������ � ���������� ������
try {
    $con = new mysqli('localhost', 'root', '', 'onlinelibrary');
    if ($con->connect_error) {
        throw new mysqli_sql_exception("������ ����������� � ���� ������: " . $con->connect_error);
    }
} catch (mysqli_sql_exception $e) {
    $con = null; // ������������� $con � null, ���� ����������� �� �������
    error_log($e->getMessage()); // �������� ������ ��� �������
}
?>
