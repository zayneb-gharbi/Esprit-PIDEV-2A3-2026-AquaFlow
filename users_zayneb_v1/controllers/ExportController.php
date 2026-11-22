<?php
class ExportController
{
    private Utilisateur $model;
    public function __construct(Utilisateur $model) { $this->model = $model; }

    public function plombiers(): void
    {
        require_admin();
        $plombiers = $this->model->getPlombiers();

        // Export XLSX si PhpSpreadsheet est installé via Composer.
        if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
            require_once __DIR__ . '/../vendor/autoload.php';
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Plombiers');
            $headers = ['ID', 'Nom', 'Prénom', 'Email', 'Téléphone', 'Statut', 'Date d’inscription'];
            $sheet->fromArray($headers, null, 'A1');
            $row = 2;
            foreach ($plombiers as $p) {
                $sheet->fromArray([$p['id'], $p['nom'], $p['prenom'], $p['email'], $p['telephone'], $p['statut'], $p['date_creation']], null, 'A' . $row++);
            }
            foreach (range('A', 'G') as $column) { $sheet->getColumnDimension($column)->setAutoSize(true); }
            $sheet->getStyle('A1:G1')->getFont()->setBold(true);
            while (ob_get_level()) { ob_end_clean(); }
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="liste_plombiers.xlsx"');
            header('Cache-Control: max-age=0');
            (new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet))->save('php://output');
            exit;
        }

        // Repli CSV compatible Excel si la dépendance XLSX n’est pas encore installée.
        while (ob_get_level()) { ob_end_clean(); }
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="liste_plombiers.csv"');
        $out = fopen('php://output', 'w');
        fwrite($out, "\xEF\xBB\xBF");
        fputcsv($out, ['ID', 'Nom', 'Prénom', 'Email', 'Téléphone', 'Statut', 'Date d’inscription'], ';');
        foreach ($plombiers as $p) {
            fputcsv($out, [$p['id'], $p['nom'], $p['prenom'], $p['email'], $p['telephone'], $p['statut'], $p['date_creation']], ';');
        }
        fclose($out);
        exit;
    }
}
