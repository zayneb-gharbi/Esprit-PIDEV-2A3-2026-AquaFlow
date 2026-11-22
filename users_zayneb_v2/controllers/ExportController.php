<?php
class ExportController
{
    private Utilisateur $model;
    public function __construct(Utilisateur $model) { $this->model = $model; }

    public function utilisateurs(): void
    {
        require_admin();
        $role = $_GET['role'] ?? 'plombier';
        if (!in_array($role, ['plombier', 'citoyen'], true)) {
            http_response_code(400);
            exit('Type d’export invalide.');
        }
        $filters = ['q' => trim($_GET['q'] ?? ''), 'statut' => trim($_GET['statut'] ?? '')];
        $users = $this->model->getUtilisateursParRole($role, $filters);
        $label = $role === 'plombier' ? 'plombiers' : 'citoyens';
        $title = $role === 'plombier' ? 'Liste des plombiers' : 'Liste des citoyens';
        $headers = ['ID', 'Nom', 'Prénom', 'Email', 'Téléphone', 'Statut', 'Date d’inscription'];

        if (is_file(__DIR__ . '/../vendor/autoload.php')) {
            require_once __DIR__ . '/../vendor/autoload.php';
            if (class_exists(\PhpOffice\PhpSpreadsheet\Spreadsheet::class)) {
                $book = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
                $sheet = $book->getActiveSheet();
                $sheet->setTitle($role === 'plombier' ? 'Plombiers' : 'Citoyens');
                $sheet->fromArray($headers, null, 'A1');
                $row = 2;
                foreach ($users as $user) {
                    $sheet->fromArray([$user['id'], $user['nom'], $user['prenom'], $user['email'], $user['telephone'], $user['statut'], $user['date_creation']], null, 'A' . $row++);
                }
                $lastRow = max(1, $row - 1);
                $sheet->getStyle('A1:G1')->getFont()->setBold(true)->getColor()->setARGB('FFFFFFFF');
                $sheet->getStyle('A1:G1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FF073C72');
                $sheet->freezePane('A2');
                if ($lastRow > 1) $sheet->setAutoFilter('A1:G' . $lastRow);
                foreach (range('A', 'G') as $column) $sheet->getColumnDimension($column)->setAutoSize(true);
                while (ob_get_level()) ob_end_clean();
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment; filename="AquaFlow_liste_' . $label . '.xlsx"');
                header('Cache-Control: max-age=0');
                (new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($book))->save('php://output');
                exit;
            }
        }

        // Fallback CSV compatible avec Excel si PhpSpreadsheet n’est pas installé.
        while (ob_get_level()) ob_end_clean();
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="AquaFlow_liste_' . $label . '.csv"');
        $out = fopen('php://output', 'w');
        fwrite($out, "\xEF\xBB\xBF");
        fputcsv($out, $headers, ';');
        foreach ($users as $user) {
            fputcsv($out, [$user['id'], $user['nom'], $user['prenom'], $user['email'], $user['telephone'], $user['statut'], $user['date_creation']], ';');
        }
        fclose($out);
        exit;
    }
}
