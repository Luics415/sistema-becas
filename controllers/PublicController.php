<?php
/**
 * Controlador público: consulta de estatus sin login
 */

require_once __DIR__ . '/Controller.php';
require_once dirname(__DIR__) . '/models/SolicitudModel.php';

class PublicController extends Controller
{
    private SolicitudModel $solicitudModel;

    public function __construct()
    {
        parent::__construct();
        $this->solicitudModel = new SolicitudModel();
    }

    /** Portal de consulta pública por folio */
    public function consultaEstatus(): void
    {
        $folio     = strtoupper(trim($this->get('folio', '')));
        $solicitud = null;
        $historial = [];
        $error     = '';

        if ($folio) {
            $solicitud = $this->solicitudModel->porFolio($folio);
            if (!$solicitud) {
                $error = 'No se encontró ninguna solicitud con ese folio. Verifica el número e intenta de nuevo.';
            } else {
                $historial = $this->solicitudModel->historial($solicitud['id']);
            }
        }

        $this->render('public/consulta_estatus', [
            'title'     => 'Consulta de Estatus de Beca',
            'folio'     => $folio,
            'solicitud' => $solicitud,
            'historial' => $historial,
            'error'     => $error,
        ]);
    }
}
