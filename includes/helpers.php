<?php
/**
 * Funciones auxiliares globales
 */

/** Sanitiza output para evitar XSS */
function e(string $str): string
{
    return htmlspecialchars($str, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** Redirige a una URL relativa al BASE_URL */
function redirect(string $path): void
{
    header('Location: ' . BASE_URL . '/' . ltrim($path, '/'));
    exit;
}

/** Formatea moneda en pesos mexicanos */
function formatMoney(float $amount): string
{
    return '$' . number_format($amount, 2, '.', ',') . ' MXN';
}

/** Formatea fecha en español */
function formatDate(string $date, string $format = 'd/m/Y'): string
{
    if (empty($date)) return '—';
    return date($format, strtotime($date));
}

/** Genera un folio único para solicitudes */
function generarFolio(): string
{
    $year = date('Y');
    $rand = str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
    return "B-{$year}-{$rand}";
}

/** Retorna badge HTML según estado de solicitud */
function badgeEstado(string $estado): string
{
    $map = [
        'borrador'    => ['secondary', 'Borrador'],
        'enviada'     => ['info',      'Enviada'],
        'en_revision' => ['warning',   'En Revisión'],
        'aprobada'    => ['success',   'Aprobada'],
        'rechazada'   => ['danger',    'Rechazada'],
        'cancelada'   => ['dark',      'Cancelada'],
    ];
    [$color, $label] = $map[$estado] ?? ['secondary', ucfirst($estado)];
    return "<span class=\"badge bg-{$color}\">" . e($label) . "</span>";
}

/** Retorna badge HTML según estado de documento */
function badgeDocumento(string $estado): string
{
    $map = [
        'pendiente'   => ['secondary', 'Pendiente'],
        'en_revision' => ['warning',   'En Revisión'],
        'validado'    => ['success',   'Validado'],
        'rechazado'   => ['danger',    'Rechazado'],
    ];
    [$color, $label] = $map[$estado] ?? ['secondary', ucfirst($estado)];
    return "<span class=\"badge bg-{$color}\">" . e($label) . "</span>";
}

/** Valida CURP mexicana */
function validarCURP(string $curp): bool
{
    return (bool) preg_match(
        '/^[A-Z]{1}[AEIOU]{1}[A-Z]{2}[0-9]{2}(0[1-9]|1[0-2])(0[1-9]|1[0-9]|2[0-9]|3[0-1])[HM]{1}(AS|BC|BS|CC|CS|CH|CL|CM|DF|DG|GT|GR|HG|JC|MC|MN|MS|NT|NL|OC|PL|QT|QR|SP|SL|SR|TC|TS|TL|VZ|YN|ZS|NE)[B-DF-HJ-NP-TV-Z]{3}[0-9A-Z]{1}[0-9]{1}$/',
        strtoupper($curp)
    );
}

/** Verifica si la subida de archivo fue exitosa y el tipo es permitido */
function validarArchivo(array $file): array
{
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['ok' => false, 'msg' => 'Error al subir el archivo (código ' . $file['error'] . ').'];
    }
    if ($file['size'] > MAX_FILE_SIZE) {
        return ['ok' => false, 'msg' => 'El archivo supera el tamaño máximo de 5 MB.'];
    }
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, ALLOWED_EXTS, true)) {
        return ['ok' => false, 'msg' => 'Tipo de archivo no permitido. Use: ' . implode(', ', ALLOWED_EXTS)];
    }
    // Verificar MIME real con finfo
    $finfo    = finfo_open(FILEINFO_MIME_TYPE);
    $mimeReal = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    $allowedMimes = ['application/pdf', 'image/jpeg', 'image/png'];
    if (!in_array($mimeReal, $allowedMimes, true)) {
        return ['ok' => false, 'msg' => 'Tipo MIME no permitido.'];
    }
    return ['ok' => true, 'ext' => $ext, 'mime' => $mimeReal];
}

/** Paginación: retorna datos para renderizar controles */
function paginar(int $total, int $pagina, int $porPagina = ITEMS_PER_PAGE): array
{
    $totalPaginas = (int) ceil($total / $porPagina);
    $pagina       = max(1, min($pagina, $totalPaginas));
    $offset       = ($pagina - 1) * $porPagina;
    return [
        'total'        => $total,
        'pagina'       => $pagina,
        'total_paginas'=> $totalPaginas,
        'por_pagina'   => $porPagina,
        'offset'       => $offset,
    ];
}

/** Respuesta JSON estandarizada */
function jsonResponse(bool $success, string $message, array $data = []): void
{
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(array_merge(['success' => $success, 'message' => $message], $data));
    exit;
}
