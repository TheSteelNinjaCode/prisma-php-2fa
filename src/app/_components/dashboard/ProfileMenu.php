<?php

use Lib\PHPX\PHPXUI\{DropdownMenu, DropdownMenuTrigger, DropdownMenuContent, DropdownMenuLabel, DropdownMenuSeparator, DropdownMenuGroup, DropdownMenuItem, DropdownMenuShortcut, DropdownMenuSub, DropdownMenuSubTrigger, DropdownMenuPortal, DropdownMenuSubContent, Button, Avatar, AvatarImage, Dialog, DialogTrigger, DialogContent, DialogHeader, DialogTitle, DialogDescription, Label, Input, DialogFooter};
use Lib\Auth\Auth;
use app\_components\PasswordInput;
use Lib\Validator;
use Lib\Prisma\Classes\Prisma;

$user = Auth::getInstance()->getPayload();

function signout()
{
    Auth::getInstance()->signOut();
}

function saveProfile($data)
{
    $id = Validator::cuid($data->id);
    $name = Validator::string($data->name);
    $email = Validator::email($data->email);
    $password = Validator::string($data->password);
    $passwordModified = Validator::boolean($data->passwordModified);

    $prisma = Prisma::getInstance();

    $emailExists = $prisma->user->findUnique([
        'where' => [
            'email' => $email,
            'NOT' => [
                'id' => $id
            ]
        ]
    ]);

    if ($emailExists) {
        return [
            'error' => 'El correo electrónico ya está en uso.'
        ];
    }

    $data = [
        'name' => $name,
        'email' => $email
    ];

    if ($passwordModified) {
        $password = password_hash($password, PASSWORD_DEFAULT);
        $data['password'] = $password;
        $data['tempPassword'] = null;
    }
    $updatedUser = $prisma->user->update([
        'where' => [
            'id' => $id
        ],
        'data' => $data,
        'include' => [
            'userRole' => true
        ]
    ]);

    $auth = Auth::getInstance();
    $auth->signIn($updatedUser);

    return [
        'success' => 'Perfil actualizado correctamente.'
    ];
}

?>

<DropdownMenu class="flex items-center">
    <DropdownMenuTrigger asChild="true">
        <Button class="hover:bg-transparent border-none size-2" variant="ghost" size="icon">
            <Avatar class="size-2">
                <AvatarImage src="https://github.com/shadcn.png" alt="@shadcn" />
            </Avatar>
        </Button>
    </DropdownMenuTrigger>
    <DropdownMenuContent class="w-56">
        <DropdownMenuLabel><?= $user->name ?></DropdownMenuLabel>
        <DropdownMenuSeparator />
        <DropdownMenuGroup>
            <DropdownMenuItem onclick="editProfile.showModal()">
                Perfil
                <DropdownMenuShortcut>⇧⌘P</DropdownMenuShortcut>
            </DropdownMenuItem>
        </DropdownMenuGroup>
        <DropdownMenuSeparator />
        <DropdownMenuItem onclick="signout">
            Cerrar Sesión
            <DropdownMenuShortcut>⇧⌘Q</DropdownMenuShortcut>
        </DropdownMenuItem>
    </DropdownMenuContent>
</DropdownMenu>

<Dialog>
    <DialogTrigger id="editProfile" />
    <DialogContent class="sm:max-w-[425px]">
        <DialogHeader>
            <DialogTitle>Editar perfil</DialogTitle>
            <DialogDescription>
                Realiza cambios en tu perfil aquí. Haz clic en guardar cuando hayas terminado.
            </DialogDescription>
        </DialogHeader>
        <form onsubmit="saveProfile" pp-after-request="saveProfileResponse">
            <div class="grid gap-4 py-4">
                <input type="hidden" name="id" value="<?= $user->id ?>" />
                <div class="grid grid-cols-4 items-center gap-4">
                    <Label for="rol" class="text-right">
                        Rol
                    </Label>
                    <Input readonly="true" id="rol" value="<?= $user->userRole->name ?>" class="col-span-3" />
                </div>
                <div class="grid grid-cols-4 items-center gap-4">
                    <Label for="name" class="text-right">
                        Name
                    </Label>
                    <Input id="name" value="<?= $user->name ?>" name="name" class="col-span-3" />
                </div>
                <div class="grid grid-cols-4 items-center gap-4">
                    <Label for="email" class="text-right">
                        Email
                    </Label>
                    <Input type="email" id="email" value="<?= $user->email ?>" name="email" class="col-span-3" />
                </div>
                <div class="grid grid-cols-4 items-center gap-4">
                    <Label for="password" class="text-right">
                        Contraseña
                    </Label>
                    <div class="col-span-3 flex items-center gap-4">
                        <input id="profile-password-modify" type="hidden" name="passwordModified" value="false" />
                        <PasswordInput class="hidden" name="password" />
                        <Button variant="outline" onclick="togglePasswordInputVisibility(this)">
                            Cambiar contraseña
                        </Button>
                    </div>
                </div>
            </div>
            <DialogFooter>
                <Button type="submit">Guardar</Button>
            </DialogFooter>
        </form>
    </DialogContent>
</Dialog>

<script>
    function togglePasswordInputVisibility(element) {
        const passwordInput = element.previousElementSibling;
        const passwordModified = document.getElementById('profile-password-modify');

        if (element.textContent.trim() === 'Cambiar contraseña') {
            passwordInput.classList.remove('hidden');
            passwordModified.value = 'true';

            element.textContent = 'Cancelar';
        } else {
            passwordInput.classList.add('hidden');
            passwordModified.value = 'false';

            element.textContent = 'Cambiar contraseña';
        }
    }

    function saveProfileResponse(data) {
        if (data.response.error) {
            alert(data.response.error);
        } else {
            alert(data.response.success);
            window.location.reload();
        }
    }
</script>