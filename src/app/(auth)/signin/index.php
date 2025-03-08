<?php

use Lib\PHPX\PHPXUI\{
    Card,
    CardContent,
    CardHeader,
    CardTitle,
    CardDescription,
    Input,
    Label,
    Button,
    Dialog,
    DialogTrigger,
    DialogContent,
    DialogHeader,
    DialogTitle,
    DialogDescription,
    DialogFooter
};
use Lib\{Validator, StateManager};
use Lib\Auth\Auth;
use Lib\Prisma\Classes\Prisma;
use Lib\PHPMailer\Mailer;
use app\_components\PasswordInput;
use Sonata\GoogleAuthenticator\GoogleQrUrl;
use Sonata\GoogleAuthenticator\GoogleAuthenticator;

function SignIn($data)
{
    $email = Validator::email($data->email);
    $password = Validator::string($data->password);

    $agentIP = $_SERVER['REMOTE_ADDR'];
    $bruteForceSession = $_SESSION['bruteForceSession'] ?? null;
    if ($bruteForceSession) {
        StateManager::setState('bruteForce', $bruteForceSession);
    }

    $bruteForce = StateManager::getState('bruteForce', [
        'count' => 0,
        'lastAttempt' => time(),
        'agentIP' => $agentIP
    ]);

    $remainingTime = ceil((300 - (time() - $bruteForce->lastAttempt)) / 60); // 5 minutes
    if ($bruteForce->count >= 5 && time() - $bruteForce->lastAttempt < 300 && $bruteForce->agentIP === $agentIP) {
        $_SESSION['bruteForceSession'] = [
            'count' => $bruteForce->count,
            'lastAttempt' => $bruteForce->lastAttempt,
            'agentIP' => $bruteForce->agentIP
        ];
        return [
            'error' => "Has intentado iniciar sesión demasiadas veces. Por favor, intenta nuevamente en $remainingTime minutos."
        ];
    } else {
        $_SESSION['bruteForceSession'] = null;
    }

    $prisma = Prisma::getInstance();

    $userExist = $prisma->user->findUnique([
        'where' => [
            'email' => $email
        ],
        'include' => [
            'userRole' => true
        ]
    ]);

    if (!$userExist) {
        StateManager::setState('bruteForce', [
            'count' => ++$bruteForce->count,
            'lastAttempt' => time(),
            'agentIP' => $agentIP
        ]);
        return [
            'error' => 'Las credenciales son incorrectas. Por favor, intenta nuevamente.'
        ];
    }

    if ($userExist) {
        if (!password_verify($password, $userExist->password)) {
            StateManager::setState('bruteForce', [
                'count' => ++$bruteForce->count,
                'lastAttempt' => time(),
                'agentIP' => $agentIP
            ]);
            return [
                'error' => 'Las credenciales son incorrectas. Por favor, intenta nuevamente.'
            ];
        }

        StateManager::setState('userExist', $userExist);
        return [
            'email' => $userExist->email,
            'isGoogle2faActive' => $userExist->isGoogle2faActive,
            'qrCodeUrl' => $userExist->isGoogle2faActive ? null : generateQRCode($userExist->email, $userExist->google2faSecret)
        ];
    }
}

function generateQRCode($userEmail, $secret)
{
    return GoogleQrUrl::generate($userEmail, $secret, 'PRISMA-PHP');
}

function authenticateUser($data)
{
    $userExist = StateManager::getState('userExist');
    $twoFactorCode = Validator::string($data->twoFactorCode);
    $authenticator = new GoogleAuthenticator();

    if ($userExist->google2faSecret && !$authenticator->checkCode($userExist->google2faSecret, $twoFactorCode)) {
        return [
            'error' => 'Código de autenticación inválido. Intenta nuevamente.'
        ];
    }

    $prisma = Prisma::getInstance();
    $prisma->user->update([
        'where' => [
            'id' => $userExist->id
        ],
        'data' => [
            'isGoogle2faActive' => true
        ]
    ]);

    $user = $prisma->user->findUnique([
        'where' => [
            'id' => $userExist->id
        ],
        'include' => [
            'userRole' => true
        ]
    ]);

    Auth::getInstance()->signIn($user);
}

?>

<div class="min-h-screen flex items-center justify-center bg-background text-foreground px-4">
    <Card class="w-full max-w-md shadow-lg border border-border rounded-lg bg-card text-card-foreground p-6">
        <CardHeader class="text-center">
            <CardTitle class="text-xl font-bold sm:text-2xl">Bienvenido</CardTitle>
            <CardDescription class="text-sm sm:text-base">Inicia sesión en tu cuenta</CardDescription>
            <div id="error-message" class="hidden text-red-600 text-sm sm:text-base text-center"></div>
        </CardHeader>
        <CardContent class="flex flex-col space-y-6">
            <form class="flex flex-col space-y-4" onsubmit="SignIn" pp-after-request="getResponseAfterSubmit" pp-suspense="{'disabled': true}">
                <div class="space-y-2">
                    <Label for="email">Correo Electrónico</Label>
                    <Input name="email" type="email" id="email" placeholder="tucorreo@example.com" class="w-full" required="true" />
                </div>

                <div class="space-y-2">
                    <Label for="password">Contraseña</Label>
                    <PasswordInput name="password" id="password" placeholder="••••••••" class="w-full" required="true" />
                </div>

                <div class="flex items-center justify-between text-sm">
                    <a href="#" class="text-primary hover:underline">¿Olvidaste tu contraseña?</a>
                </div>

                <Button type="submit" class="w-full bg-primary text-primary-foreground hover:bg-primary/90 mt-2" pp-suspense="Iniciando...">
                    Iniciar Sesión
                </Button>
            </form>

            <p class="text-center text-sm text-muted-foreground">
                ¿No tienes una cuenta? <a href="/signup" class="text-primary hover:underline">Regístrate</a>
            </p>
        </CardContent>
    </Card>
</div>

<Dialog>
    <DialogTrigger asChild="true" id="qr-code-dialog"/>
    <DialogContent class="sm:max-w-[425px]">
        <DialogHeader>
            <DialogTitle>Autenticación de Dos Factores</DialogTitle>
            <DialogDescription>
                Escanea el código QR a continuación con tu aplicación de autenticación para configurar 2FA.
            </DialogDescription>
            <div id="qr-code-error-message" class="hidden text-red-600 text-sm sm:text-base text-center"></div>
        </DialogHeader>
        <form onsubmit="authenticateUser" pp-suspense="{'disabled': true}" pp-after-request="qrAfterSubmit">
            <div class="grid gap-4 py-4">
                <div class="flex justify-center" id="qr-code-dialog-image">
                    <img
                        alt="Código QR para 2FA"
                        class="w-40 h-40 border rounded-lg p-2" />
                </div>
                <div class="grid grid-cols-4 items-center gap-4">
                    <Label for="code" class="text-right">
                        Código
                    </Label>
                    <Input
                        name="twoFactorCode"
                        id="code"
                        type="text"
                        placeholder="123456"
                        class="col-span-3" />
                </div>
            </div>
            <DialogFooter>
                <Button variant="outline">Cancelar</Button>
                <Button type="submit">Verificar</Button>
            </DialogFooter>
        </form>
    </DialogContent>
</Dialog>

<script>
    function getResponseAfterSubmit(data) {
        console.log("🚀 ~ getResponseAfterSubmit ~ data:", data)
        if (data.success && data.response.email) {
            document.getElementById('qr-code-dialog').showModal();
            const qrCodeDialogImage = document.getElementById('qr-code-dialog-image');
            const qrCodeImg = qrCodeDialogImage.querySelector("img");

            if (data.response.isGoogle2faActive) {
                qrCodeDialogImage.classList.add('hidden');
            } else {
                qrCodeDialogImage.classList.remove('hidden');
                qrCodeImg.src = data.response.qrCodeUrl;
                qrCodeImg.alt = "Código QR para 2FA";
            }
        } else if (data.response && data.response.error) {
            const errorMessage = document.getElementById('error-message');
            errorMessage.innerHTML = '';
            errorMessage.classList.remove('hidden');
            errorMessage.innerHTML = `<p>${data.response.error}</p>`;
        }
    }

    function qrAfterSubmit(data) {
        if (data.response && data.response.error) {
            const errorMessage = document.getElementById('qr-code-error-message');
            errorMessage.innerHTML = '';
            errorMessage.classList.remove('hidden');
            errorMessage.innerHTML = `<p>${data.response.error}</p>`;
        }
    }
</script>