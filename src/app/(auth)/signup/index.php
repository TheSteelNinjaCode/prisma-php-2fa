<?php

use Lib\PHPX\PHPXUI\{Card, CardContent, Input, Label, Button, Checkbox};
use Lib\Validator;
use Lib\Prisma\Classes\Prisma;
use Sonata\GoogleAuthenticator\GoogleAuthenticator;

function register($data)
{
    $name = Validator::string($data->name);
    $email = Validator::email($data->email);
    $password = Validator::string($data->password);
    $confirmPassword = Validator::string($data->confirmPassword);

    if (!$email) {
        return ['error' => 'Correo electrónico inválido'];
    }

    $prisma = Prisma::getInstance();

    $userEmailExist = $prisma->user->findUnique([
        'where' => [
            'email' => $email
        ]
    ]);

    if ($userEmailExist) {
        return ['error' => 'El correo electrónico ya está en uso'];
    }

    $authenticator = new GoogleAuthenticator();

    $user = $prisma->user->create(
        [
            'data' => [
                'name' => $name,
                'email' => $email,
                'google2faSecret' => $authenticator->generateSecret(),
                'password' => password_hash($password, PASSWORD_DEFAULT),
                'userRole' => [
                    'connect' => [
                        'name' => 'User'
                    ]
                ]
            ]
        ]
    );

    return [
        'success' => 'Cuenta creada exitosamente',
        'user' => $user
    ];
}

?>

<div class="min-h-screen flex items-center justify-center bg-background text-foreground px-4">
    <Card class="w-full max-w-md shadow-lg border border-border rounded-lg bg-card text-card-foreground p-6">
        <CardHeader class="text-center">
            <CardTitle class="text-lg font-bold sm:text-2xl">Crear Cuenta</CardTitle>
            <CardDescription class="text-sm sm:text-base">Únete y gestiona tu contabilidad con facilidad</CardDescription>
            <div id="error-message" class="text-sm sm:text-base text-red-500 hidden"></div>
        </CardHeader>
        <CardContent class="flex flex-col space-y-6">
            <form id="register" class="flex flex-col space-y-4" onsubmit="register" pp-after-request="validationResponse" pp-suspense="{'disabled': true}">
                <div class="space-y-2">
                    <Label for="name">Nombre</Label>
                    <Input name="name" type="text" id="name" placeholder="Nombre" class="w-full" required="true" />
                </div>

                <div class="space-y-2">
                    <Label for="email">Correo Electrónico</Label>
                    <Input name="email" type="email" id="email" placeholder="tucorreo@example.com" class="w-full" required="true" />
                </div>

                <div class="space-y-2">
                    <Label for="password">Contraseña</Label>
                    <Input name="password" type="password" id="password" placeholder="••••••••" class="w-full" required="true" />
                </div>

                <div class="space-y-2">
                    <Label for="confirm-password">Confirmar Contraseña</Label>
                    <Input name="confirmPassword" type="password" id="confirm-password" placeholder="••••••••" class="w-full" required="true" />
                </div>

                <Button type="submit" class="w-full bg-primary text-primary-foreground hover:bg-primary/90 mt-2">
                    Registrarse
                </Button>
            </form>

            <p class="text-center text-sm text-muted-foreground">
                ¿Ya tienes una cuenta? <a href="/signin" class="text-primary hover:underline">Iniciar Sesión</a>
            </p>
        </CardContent>
    </Card>
</div>

<script>
    function validationResponse(data) {
        console.log("🚀 ~ validationResponse ~ data:", data)
        const messageSection = document.getElementById('error-message');
        const error = data.response.error;
        const success = data.response.success;

        if (error) {
            messageSection.innerHTML = `<p>${error}</p>`;
            messageSection.classList.remove('hidden');
            messageSection.classList.remove('text-green-500');
            messageSection.classList.add('text-red-500');
        } else if (success) {
            messageSection.innerHTML = `<p>${success}</p>`;
            messageSection.classList.remove('hidden');
            messageSection.classList.remove('text-red-500');
            messageSection.classList.add('text-green-500');

            document.getElementById('register').reset();
        } else {
            messageSection.classList.add('hidden');
            messageSection.innerHTML = '';
        }
    }
</script>