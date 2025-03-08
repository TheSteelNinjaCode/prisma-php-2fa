<?php

use Lib\Prisma\Classes\Prisma;
use Lib\PHPX\PHPXUI\{Dialog, Button, Label, Input, DialogTrigger, DialogContent, DialogHeader, DialogTitle, DialogDescription, Select, SelectTrigger, SelectValue, SelectContent, SelectGroup, SelectItem, DialogFooter};
use Lib\Validator;

$prisma = Prisma::getInstance();

$userRoles = $prisma->userRole->findMany([], true);

function createUpdateUser($data)
{
    $id = Validator::cuid($data->id);
    $name = Validator::string($data->name);
    $lastName = Validator::string($data->lastName);
    $email = Validator::email($data->email);
    $password = Validator::string($data->password);
    $roleId = Validator::int($data->roleId);

    if (!$name || !$lastName || !$email || !$roleId) {
        return [
            'error' => 'Por favor, rellena todos los campos.'
        ];
    }

    $prisma = Prisma::getInstance();

    $emailExist = $prisma->user->findUnique([
        'where' => [
            'email' => $email,
            'NOT' => [
                'id' => $id
            ]
        ],
    ]);

    if ($emailExist) {
        return [
            'error' => 'Ya existe un usuario con ese correo.'
        ];
    } else {
        if ($id) {

            $data = [
                'name' => $name,
                'email' => $email,
                'lastName' => $lastName,
                'userRole' => [
                    'connect' => [
                        'id' => $roleId
                    ]
                ]
            ];

            if ($password) {
                $password = password_hash($password, PASSWORD_DEFAULT);
                $data['password'] = $password;
                $data['tempPassword'] = null;
            }

            $prisma->user->update([
                'where' => [
                    'id' => $id
                ],
                'data' => $data
            ]);

            return [
                'success' => 'Usuario actualizado correctamente.'
            ];
        } else {

            $registrationExist = $prisma->registration->findFirst([
                'where' => [
                    'user' => null
                ]
            ]);

            if ($registrationExist) {
                $data = [
                    'name' => $name,
                    'lastName' => $lastName,
                    'email' => $email,
                    'userRole' => [
                        'connect' => [
                            'id' => $roleId
                        ]
                    ],
                    'registration' => [
                        'connect' => [
                            'id' => $registrationExist->id
                        ]
                    ],
                ];

                if ($password) {
                    $password = password_hash($password, PASSWORD_DEFAULT);
                    $data['password'] = $password;
                }

                $prisma->user->create([
                    'data' => $data
                ]);

                return [
                    'success' => 'Usuario creado correctamente.'
                ];
            } else {
                return [
                    'error' => 'No hay registros disponibles para asignar a este usuario.'
                ];
            }
        }
    }
}

?>

<Dialog callback="createUpdateUserModalCallback">
    <DialogTrigger asChild="true" id="createUpdateUserModal">
        <Button>Agregar Usuario</Button>
    </DialogTrigger>
    <DialogContent class="sm:max-w-[425px]">
        <DialogHeader>
            <DialogTitle id="createUpdateUserModalTitle">Nuevo Usuario</DialogTitle>
            <DialogDescription id="createUpdateUserModalDescription">
                Rellena los campos para agregar un nuevo usuario.
            </DialogDescription>
        </DialogHeader>
        <div id="createUpdateUser-message" class="mb-1"></div>
        <form id="createUpdateUserForm" onsubmit="createUpdateUser" pp-after-request="createUpdateUserResponse" pp-suspense="{'disabled': true}">
            <div class="grid gap-4 py-4">
                <input type="hidden" name="id" id="createUpdateUserId" />
                <div class="grid grid-cols-4 items-center gap-4">
                    <Label for="createUpdateUserName" class="text-right">
                        Nombre
                    </Label>
                    <Input name="name" id="createUpdateUserName" placeholder="Nombre" class="col-span-3" required="true" />
                </div>
                <div class="grid grid-cols-4 items-center gap-4">
                    <Label for="createUpdateUserLastName" class="text-right">
                        Apellido
                    </Label>
                    <Input name="lastName" id="createUpdateUserLastName" placeholder="Nombre" class="col-span-3" required="true" />
                </div>
                <div class="grid grid-cols-4 items-center gap-4">
                    <Label for="createUpdateUserEmail" class="text-right">
                        Email
                    </Label>
                    <Input name="email" id="createUpdateUserEmail" placeholder="Correo" class="col-span-3" required="true" />
                </div>
                <div class="grid grid-cols-4 items-center gap-4">
                    <Label for="createUpdateUserPassword" class="text-right">
                        Contraseña
                    </Label>
                    <PasswordInput name="password" id="createUpdateUserPassword" placeholder="Contraseña" class="col-span-3" />
                </div>
                <div class="grid grid-cols-4 items-center gap-4 h-auto">
                    <Label for="role" class="text-right">
                        Rol
                    </Label>
                    <Select id="role" class="col-span-3" name="roleId" required="true">
                        <SelectItem selected="true" disabled="true">Selecciona un rol</SelectItem>
                        <?php foreach ($userRoles as $role): ?>
                            <SelectItem value="<?= $role->id ?>"><?= $role->name ?></SelectItem>
                        <?php endforeach; ?>
                    </Select>
                </div>
            </div>
            <DialogFooter>
                <Button type="submit" pp-suspense="{'textContent': 'Guardando...'}">Guardar</Button>
            </DialogFooter>
        </form>
    </DialogContent>
</Dialog>

<script>
    function createUpdateUserModalCallback({
        title,
        description
    } = {}) {
        const officeId = document.getElementById('createUpdateUserId');
        document.getElementById('createUpdateUserForm').reset();
        officeId.value = '';
        document.getElementById('createUpdateUser-message').innerHTML = '';

        const titleContent = document.getElementById('createUpdateUserModalTitle');
        const descriptionContent = document.getElementById('createUpdateUserModalDescription');

        titleContent.textContent = title || 'Nuevo Usuario';
        descriptionContent.textContent = description || 'Rellena los campos para agregar un nuevo usuario.';
    }

    async function createUpdateUserResponse(data) {
        const messageDiv = document.getElementById('createUpdateUser-message');
        messageDiv.innerHTML = '';

        if (data.response && data.response.success) {
            messageDiv.className = 'text-green-600 font-medium';
            messageDiv.textContent = data.response.success;
            const officeId = document.getElementById('createUpdateUserId');
            if (!officeId.value) {
                document.getElementById('createUpdateUserForm').reset();
            }
            await pphp.sync('dashboard-users-table');
        } else if (data.response && data.response.error) {
            messageDiv.className = 'text-red-600 font-medium';
            messageDiv.textContent = data.response.error;
        } else {
            messageDiv.className = 'text-red-600 font-medium';
            messageDiv.textContent = 'Ha ocurrido un error inesperado.';
        }
    }
</script>