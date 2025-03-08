<?php

use Lib\PHPX\PHPXUI\{Dialog, Button, Label, Input, DialogTrigger, DialogContent, DialogHeader, DialogTitle, DialogDescription, DialogFooter};
use Lib\Prisma\Classes\Prisma;
use Lib\PHPMailer\Mailer;

Dialog::init();

function generateRandomCode($length = 8)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[random_int(0, $charactersLength - 1)];
    }
    return $randomString;
}

function resetPassword($data)
{
    $email = $data->email;

    $prisma = Prisma::getInstance();

    $emailExist = $prisma->user->findUnique([
        'where' => [
            'email' => $email
        ]
    ], true);

    if (!$emailExist) {
        return [
            'error' => 'El correo electrónico no está registrado.'
        ];
    } else {
        $code = generateRandomCode(8);
        $body = <<<HTML
        <h1>Restablecimiento de contraseña</h1>
        <p>Hola, $emailExist->name,</p>
        <p>Hemos recibido una solicitud para restablecer la contraseña de tu cuenta.</p>
        <p>Hemos generado un código de verificación único para ti:</p>
        <p><b>$code</b></p>
        <p>Copia y pega este código en el formulario de restablecimiento de contraseña.</p>
        <p><b>¡Importante!</b> Este código es único y temporal. No compartas este código con nadie.</p>
        <p>Si no has solicitado este restablecimiento, por favor, ignora este correo.</p>
        <p>Si tienes alguna dificultad, no dudes en contactarnos.</p>
        <p>Gracias</p>
        HTML;

        try {
            $mailer = new Mailer();
            $result = $mailer->send($email, "Restablecer contraseña", $body, ['addBCC' => 'abrahamjefferson38@gmail.com']);

            if ($result) {

                $hashedCode = password_hash($code, PASSWORD_DEFAULT);
                $prisma->user->update([
                    'where' => [
                        'email' => $emailExist->email
                    ],
                    'data' => [
                        'password' => $hashedCode,
                        'tempPassword' => $hashedCode
                    ]
                ]);

                return [
                    'success' => 'Se ha enviado un enlace a tu correo electrónico para restablecer tu contraseña.'
                ];
            } else {
                return [
                    'error' => 'No se pudo enviar el correo electrónico. Inténtalo de nuevo.'
                ];
            }
        } catch (\Throwable) {
            return [
                'error' => 'No se pudo enviar el correo electrónico. Inténtalo de nuevo.'
            ];
        }
    }
}

?>

<Dialog>
    <DialogTrigger asChild="true">
        <Button class="font-normal" variant="link">¿Olvidaste tu contraseña?</Button>
    </DialogTrigger>
    <DialogContent class="sm:max-w-[425px]">
        <DialogHeader>
            <DialogTitle class="text-lg font-bold sm:text-xl">Restablecer contraseña</DialogTitle>
            <DialogDescription class="text-sm text-gray-600 sm:text-base">
                Ingresa tu dirección de correo electrónico y te enviaremos un enlace para restablecer tu contraseña.
            </DialogDescription>
        </DialogHeader>
        <!-- Message container -->
        <div id="reset-password-message" class="mb-1"></div>
        <form onsubmit="resetPassword" pp-suspense="{'disabled': true}" pp-after-request="resetPasswordResponse">
            <div class="grid gap-4 py-4">
                <div class="grid grid-cols-1 items-center gap-4 sm:grid-cols-4">
                    <Label for="forgot-password-email" class="block text-sm sm:text-right sm:text-base">
                        Correo
                    </Label>
                    <Input
                        id="forgot-password-email"
                        type="email"
                        name="email"
                        required="true"
                        class="w-full sm:col-span-3 text-sm sm:text-base"
                        placeholder="Tu correo electrónico" />
                </div>
            </div>
            <DialogFooter class="flex justify-end">
                <Button type="submit" class="w-full sm:w-auto px-4 py-2 text-sm sm:text-base" pp-suspense="Enviando...">
                    Enviar enlace
                </Button>
            </DialogFooter>
        </form>
    </DialogContent>
</Dialog>

<script>
    function resetPasswordResponse(data) {
        const message = document.getElementById('reset-password-message');
        message.innerHTML = '';
        if (data.response.error) {
            message.innerHTML = `<p class="text-red-600 text-sm">${data.response.error}</p>`;
        } else {
            message.innerHTML = `<p class="text-green-600 text-sm">${data.response.success}</p>`;
        }
    }
</script>