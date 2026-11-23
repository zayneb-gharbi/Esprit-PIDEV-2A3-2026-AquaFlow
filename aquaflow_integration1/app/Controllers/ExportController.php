<?php
namespace App\Controllers;

use App\Models\Model;
use PDO;

require_once __DIR__ . '/../Models/Model.php';

class ExportController
{
    public function index(): void
    {
        requireLogin();
        if (($_SESSION['role'] ?? '') !== 'A') {
            http_response_code(403);
            exit('Accès interdit.');
        }

        $role = $_GET['role'] ?? 'P';
        if (!in_array($role, ['P', 'C'], true)) {
            http_response_code(400);
            exit('Type d’export invalide.');
        }

        $db = Model::getConnexion();
        $stmt = $db->prepare(
            'SELECT id_user, nom, prenom, email, telephone, statut, date_creation
             FROM utilisateur WHERE role = :role ORDER BY nom, prenom'
        );
        $stmt->execute(['role' => $role]);
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $label = $role === 'P' ? 'plombiers' : 'citoyens';
        $headers = ['ID', 'Nom', 'Prénom', 'Email', 'Téléphone', 'Statut', 'Date d’inscription'];
        $autoload = dirname(__DIR__, 2) . '/vendor/autoload.php';

        if (is_file($autoload)) {
            require_once $autoload;
            if (class_exists(\PhpOffice\PhpSpreadsheet\Spreadsheet::class)) {
                $book = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
                $sheet = $book->getActiveSheet();
                $sheet->setTitle($role === 'P' ? 'Plombiers' : 'Citoyens');
                $sheet->fromArray($headers, null, 'A1');
                $row = 2;
                foreach ($users as $u) {
                    $date = $u['DATE_CREATION'] ?? $u['date_creation'] ?? '';
                    $sheet->fromArray([
                        $u['ID_USER'] ?? $u['id_user'],
                        $u['NOM'] ?? $u['nom'],
                        $u['PRENOM'] ?? $u['prenom'],
                        $u['EMAIL'] ?? $u['email'],
                        $u['TELEPHONE'] ?? $u['telephone'],
                        $u['STATUT'] ?? $u['statut'],
                        $date
                    ], null, 'A' . $row++);
                }
                $last = max(1, $row - 1);
                $sheet->getStyle('A1:G1')->getFont()->setBold(true)->getColor()->setARGB('FFFFFFFF');
                $sheet->getStyle('A1:G1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FF073C72');
                $sheet->freezePane('A2');
                if ($last > 1) $sheet->setAutoFilter('A1:G' . $last);
                foreach (range('A', 'G') as $col) $sheet->getColumnDimension($col)->setAutoSize(true);
                while (ob_get_level()) ob_end_clean();
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment; filename="AquaFlow_liste_' . $label . '.xlsx"');
                header('Cache-Control: max-age=0');
                (new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($book))->save('php://output');
                exit;
            }
        }

        // Fallback CSV compatible avec Excel si la bibliothèque XLSX n'est pas disponible.
        while (ob_get_level()) ob_end_clean();
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="AquaFlow_liste_' . $label . '.csv"');
        $out = fopen('php://output', 'w');
        fwrite($out, "\xEF\xBB\xBF");
        fputcsv($out, $headers, ';');
        foreach ($users as $u) {
            fputcsv($out, [
                $u['ID_USER'] ?? $u['id_user'], $u['NOM'] ?? $u['nom'],
                $u['PRENOM'] ?? $u['prenom'], $u['EMAIL'] ?? $u['email'],
                $u['TELEPHONE'] ?? $u['telephone'], $u['STATUT'] ?? $u['statut'],
                $u['DATE_CREATION'] ?? $u['date_creation'] ?? ''
            ], ';');
        }
        fclose($out);
        exit;
    }
}
