<?php

class ClientController extends Controller {
    private Client $client;

    public function __construct() {
        $this->client = new Client();
    }

    public function index(): void {
        $filters = [
            'search' => $_GET['search'] ?? '',
        ];

        $clients = $this->client->getAllByCompany(current_company_id(), $filters);
        $debtTotal = $this->client->getDebtTotal(current_company_id());

        $this->view('clients/index', [
            'clients'   => $clients,
            'debtTotal' => $debtTotal,
            'filters'   => $filters,
            'title'     => 'Clientes',
        ]);
    }

    public function create(): void {
        $this->view('clients/create', ['title' => 'Nuevo Cliente']);
    }

    public function store(): void {
        $name  = trim($_POST['name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $email = trim($_POST['email'] ?? '');

        if ($name === '') {
            session_flash('error', 'El nombre del cliente es obligatorio.');
            $this->redirect('/clients/create');
            return;
        }

        $this->client->create([
            'company_id' => current_company_id(),
            'name'       => $name,
            'phone'      => $phone,
            'email'      => $email,
            'address'    => trim($_POST['address'] ?? ''),
            'notes'      => trim($_POST['notes'] ?? ''),
        ]);

        session_flash('success', 'Cliente creado correctamente.');
        $this->redirect('/clients');
    }

    public function edit(int $id): void {
        $client = $this->client->findWithCompanyCheck($id, current_company_id());
        if (!$client) {
            session_flash('error', 'Cliente no encontrado.');
            $this->redirect('/clients');
            return;
        }

        $this->view('clients/edit', [
            'client' => $client,
            'title'  => 'Editar Cliente',
        ]);
    }

    public function update(int $id): void {
        $client = $this->client->findWithCompanyCheck($id, current_company_id());
        if (!$client) {
            session_flash('error', 'Cliente no encontrado.');
            $this->redirect('/clients');
            return;
        }

        $name  = trim($_POST['name'] ?? '');
        if ($name === '') {
            session_flash('error', 'El nombre del cliente es obligatorio.');
            $this->redirect("/clients/edit/{$id}");
            return;
        }

        $this->client->update($id, [
            'name'    => $name,
            'phone'   => trim($_POST['phone'] ?? ''),
            'email'   => trim($_POST['email'] ?? ''),
            'address' => trim($_POST['address'] ?? ''),
            'notes'   => trim($_POST['notes'] ?? ''),
        ]);

        session_flash('success', 'Cliente actualizado correctamente.');
        $this->redirect('/clients');
    }

    public function destroy(int $id): void {
        $client = $this->client->findWithCompanyCheck($id, current_company_id());
        if ($client) {
            $this->client->delete($id);
            session_flash('success', 'Cliente eliminado.');
        }
        $this->redirect('/clients');
    }

    public function show(int $id): void {
        $client = $this->client->findWithCompanyCheck($id, current_company_id());
        if (!$client) {
            session_flash('error', 'Cliente no encontrado.');
            $this->redirect('/clients');
            return;
        }

        // Get client's sales (only if column exists — Punto 2)
        $sales = [];
        try {
            $saleModel = new Sale();
            $sales = $saleModel->getByClient($id);
        } catch (Exception $e) {
            // Column client_id may not exist yet
        }

        // Get debt payments
        $payments = $this->getPayments($id);

        $this->view('clients/show', [
            'client'   => $client,
            'sales'    => $sales,
            'payments' => $payments,
            'title'    => "Cliente: {$client['name']}",
        ]);
    }

    public function pay(int $id): void {
        $client = $this->client->findWithCompanyCheck($id, current_company_id());
        if (!$client) {
            session_flash('error', 'Cliente no encontrado.');
            $this->redirect('/clients');
            return;
        }

        $amount = (float) ($_POST['amount'] ?? 0);
        if ($amount <= 0) {
            session_flash('error', 'El monto debe ser mayor a 0.');
            $this->redirect("/clients/{$id}");
            return;
        }

        if ($amount > (float)$client['total_debt']) {
            session_flash('error', 'El abono no puede superar la deuda actual.');
            $this->redirect("/clients/{$id}");
            return;
        }

        $db = getDB();
        $db->prepare(
            "INSERT INTO debt_payments (client_id, amount, type, notes, payment_date)
             VALUES (?, ?, 'payment', ?, DATE('now'))"
        )->execute([$id, $amount, trim($_POST['notes'] ?? 'Abono registrado')]);

        // Update client debt
        $newDebt = (float)$client['total_debt'] - $amount;
        $this->client->updateDebt($id, $newDebt);

        session_flash('success', 'Abono registrado correctamente. Deuda actual: ' . format_currency($newDebt));
        $this->redirect("/clients/{$id}");
    }

    public function adjust(int $id): void {
        $client = $this->client->findWithCompanyCheck($id, current_company_id());
        if (!$client) {
            session_flash('error', 'Cliente no encontrado.');
            $this->redirect('/clients');
            return;
        }

        $amount = (float) ($_POST['amount'] ?? 0);
        $reason = trim($_POST['reason'] ?? '');

        if ($amount == 0) {
            session_flash('error', 'El monto del ajuste no puede ser 0.');
            $this->redirect("/clients/{$id}");
            return;
        }

        if ($reason === '') {
            session_flash('error', 'Debés ingresar una razón para el ajuste.');
            $this->redirect("/clients/{$id}");
            return;
        }

        $db = getDB();
        $db->prepare(
            "INSERT INTO debt_payments (client_id, amount, type, notes, payment_date)
             VALUES (?, ?, 'adjustment', ?, DATE('now'))"
        )->execute([$id, $amount, "Ajuste: {$reason}"]);

        // Update client debt
        $this->client->adjustDebt($id, $amount);

        $action = $amount > 0 ? 'agregada' : 'quitada';
        session_flash('success', "Deuda {$action} correctamente. Monto: " . format_currency(abs($amount)));
        $this->redirect("/clients/{$id}");
    }

    private function getPayments(int $clientId): array {
        $db = getDB();
        $stmt = $db->prepare(
            "SELECT * FROM debt_payments WHERE client_id = ? ORDER BY created_at DESC"
        );
        $stmt->execute([$clientId]);
        return $stmt->fetchAll();
    }
}
