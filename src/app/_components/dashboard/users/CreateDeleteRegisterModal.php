<?php

use Lib\PHPX\PHPXUI\{Dialog, Button, Input, Table, TableHeader, TableRow, TableHead, TableBody, TableCell, DialogTrigger, DialogContent, DialogHeader, DialogTitle, DialogDescription, Badge};
use Lib\Prisma\Classes\Prisma;
use Lib\PHPX\PPIcons\{Pencil, Trash, Plus, LoaderCircle};
use Lib\Validator;

$prisma = Prisma::getInstance();
$registrations = $prisma->registration->findMany([
    'include' => [
        'user' => true
    ],
    'orderBy' => ['createdAt' => 'desc']
]);

function createRegister()
{
    $prisma = Prisma::getInstance();
    $registration = $prisma->registration->create([
        'data' => []
    ]);

    if ($registration) {
        return [
            'success' => 'Registro creado correctamente.'
        ];
    } else {
        return [
            'error' => 'No se ha podido crear el registro.'
        ];
    }
}

function deleteRegister($data)
{
    $id = Validator::cuid($data->id);

    if (!$id) {
        return [
            'error' => 'Por favor, proporciona un ID válido.'
        ];
    }

    $prisma = Prisma::getInstance();
    $registration = $prisma->registration->delete([
        'where' => [
            'id' => $id
        ]
    ]);

    if ($registration) {
        return [
            'success' => 'Registro eliminado correctamente.'
        ];
    } else {
        return [
            'error' => 'No se ha podido eliminar el registro.'
        ];
    }
}

?>

<Dialog callback="createRegisterModalCallback">
    <DialogTrigger asChild="true">
        <Button variant="outline">Registros</Button>
    </DialogTrigger>
    <DialogContent class="sm:max-w-[425px]">
        <DialogHeader>
            <DialogTitle>Registros</DialogTitle>
            <DialogDescription>
                Agrega o elimina registros.
            </DialogDescription>
        </DialogHeader>
        <div class="flex flex-col gap-4">
            <div id="createRegister-message"></div>
            <Button onclick="createRegister" pp-after-request="createRegisterResponse" size="icon" variant="outline" pp-suspense="{'targets': [{'id': '#create-icon', 'classList.add': 'hidden'}, {'id': '#create-loader', 'classList.remove': 'hidden'}]}">
                <Plus id="create-icon" />
                <LoaderCircle id="create-loader" class="hidden size-4 animate-spin" />
            </Button>
        </div>

        <div class="relative w-full overflow-auto h-64">
            <Table>
                <TableHeader class="sticky top-0 z-10">
                    <TableRow>
                        <TableHead>ID</TableHead>
                        <TableHead>Usuario</TableHead>
                        <TableHead></TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody pp-sync="register-modal-table-body">
                    <?php foreach ($registrations as $registration): ?>
                        <TableRow>
                            <TableCell class="font-medium"><?= $registration->id ?></TableCell>
                            <TableCell class="font-medium"><?= $registration->user->name ?? '' ?></TableCell>
                            <TableCell class="text-right">
                                <Button <?= isset($registration->user->name) ? 'disabled="true"' : '' ?> variant="outline" size="icon" onclick="deleteRegister({'id': '<?= $registration->id ?>'})" pp-after-request="deleteRegisterResponse" pp-suspense="{'targets': [{'id': '#delete-icon-<?= $registration->id ?>', 'classList.add': 'hidden'}, {'id': '#delete-loader-<?= $registration->id ?>', 'classList.remove': 'hidden'}], 'disabled': true}">
                                    <Trash id="delete-icon-<?= $registration->id ?>" class="size-4" />
                                    <LoaderCircle id="delete-loader-<?= $registration->id ?>" class="hidden size-4 animate-spin" />
                                </Button>
                            </TableCell>
                        </TableRow>
                    <?php endforeach; ?>
                </TableBody>
            </Table>
        </div>
    </DialogContent>
</Dialog>

<script>
    function createRegisterModalCallback() {
        document.getElementById('createRegister-message').innerHTML = '';
    }

    async function createRegisterResponse(data) {
        const messageDiv = document.getElementById('createRegister-message');
        messageDiv.innerHTML = '';

        if (data.response.success) {
            messageDiv.className = 'text-green-600 font-medium';
            messageDiv.textContent = data.response.success;;

            await pphp.sync('register-modal-table-body');
        } else if (data.response.error) {
            messageDiv.className = 'text-red-600 font-medium';
            messageDiv.textContent = data.response.error;
        } else {
            messageDiv.className = 'text-red-600 font-medium';
            messageDiv.textContent = 'An unexpected error occurred.';
        }
    }

    async function deleteRegisterResponse(data) {
        const messageDiv = document.getElementById('createRegister-message');
        messageDiv.innerHTML = '';

        if (data.response.success) {
            messageDiv.className = 'text-green-600 font-medium';
            messageDiv.textContent = data.response.success;

            await pphp.sync('register-modal-table-body');
        } else if (data.response.error) {
            messageDiv.className = 'text-red-600 font-medium';
            messageDiv.textContent = data.response.error;
        } else {
            messageDiv.className = 'text-red-600 font-medium';
            messageDiv.textContent = 'An unexpected error occurred.';
        }
    }
</script>