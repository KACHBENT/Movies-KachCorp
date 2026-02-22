<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <title>Accesos al sistema</title>
</head>

<body style="margin:0;padding:0;background:#0b1020;font-family:Arial,Helvetica,sans-serif;">
    <div style="max-width:600px;margin:0 auto;padding:24px;">
        <div style="background:#121a33;border-radius:16px;padding:22px;color:#fff;">
            <h2 style="margin:0 0 10px;font-size:22px;">Bienvenido(a),
                <?= esc($nombre) ?>
            </h2>

            <p style="margin:0 0 12px;color:#cbd5e1;">
                Tu cuenta fue creada correctamente. Aquí están tus accesos:
            </p>

            <div
                style="background:#0b1226;border:1px solid rgba(255,255,255,.08);border-radius:14px;padding:14px;margin:14px 0;">
                <p style="margin:0 0 8px;color:#cbd5e1;"><b>Usuario:</b>
                    <?= esc($correo) ?>
                </p>
                <p style="margin:0;color:#cbd5e1;"><b>Contraseña:</b>
                    <?= esc($passTemp) ?>
                </p>
            </div>

            <p style="margin:0 0 14px;color:#cbd5e1;">
                Inicia sesión aquí:
            </p>

            <a href="<?= esc($loginUrl) ?>"
                style="display:inline-block;background:#4f46e5;color:#fff;text-decoration:none;padding:10px 14px;border-radius:12px;font-weight:bold;     width: 92%;
    text-align: center;">
               Iniciar Sesión
            </a>
        </div>

        <p style="text-align:center;color:#94a3b8;font-size:11px;margin:14px 0 0;">
            Movies KachCorp ·
            <?= esc($fecha ?? date('Y-m-d H:i')) ?>
        </p>
    </div>
</body>

</html>