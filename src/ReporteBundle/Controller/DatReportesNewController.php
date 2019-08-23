<?php

namespace ReporteBundle\Controller;

use ReporteBundle\Util\Util;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use ReporteBundle\Util\DatosReportes;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/*No notificar errores*/
error_reporting(0);

/**
 * DatReporte controller.
 *
 */

/**
 * @Security("is_granted('ROLE_GENERAR_REPORTE')")
 */
class DatReportesNewController extends Controller
{
    /**
     * @Route("/reporte/",name="reporte_index")
     */
    public function indexAction()
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $defaultData = array('message' => 'Type your message here');
        $form = DatosReportes::CrearFormularioReporte($this->createFormBuilder($defaultData));

        $em = $this->getDoctrine()->getManager();
        $aseg = $em->getRepository('NomencladorBundle:NomAseguramiento')->listaAsegNivel1();
        $aseArray = array();
        $nivelase = 0;
        for ($j = 0; $j < count($aseg); $j++) {
            $aseArray[] = $this->get('nomenclador.nomaseguramiento')->getHijosAseguramiento($aseg[$j]);
        }
        $nivelProd = $this->get('nomenclador.nomproducto')->maxNivel();
        $nivelGru = $this->get('nomenclador.nomgrupointeres')->maxNivel();
        $nivelase = $this->get('nomenclador.nomaseguramiento')->maxNivel();
        $vinculos = $this->get('reporte.services')->arbolVinculo();
        $portador = $this->get('reporte.services')->arbolPortador();
        $ueb = $this->get('reporte.services')->arbolUeb();
        $datosa = json_encode($aseArray);
        $datVin = json_encode($vinculos);
        $datport = json_encode($portador);
        $datueb = json_encode($ueb);
        return $this->render('ReporteBundle:Default/index.html.twig', array(
            'aseguramiento' => $datosa,
            'nivelgrupo' => $nivelGru,
            'nivelaseg' => $nivelase,
            'nivelproducto' => $nivelProd,
            'vinculos' => $datVin,
            'portador' => $datport,
            'ueb' => $datueb,
            'form' => $form->createView()
        ));
    }

    /*Generar el reporte de los Portadores*/
    /**
     * @Route("/reporte/portador",name="reporte_portador")
     */
    public function exportarEnergiaPorPortadoresAction(Request $request)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        ini_set('memory_limit', '3072M');
        set_time_limit(3600);
        $em = $this->getDoctrine()->getManager();
        $data = $request->request->get('form');
        $data['usuarioLog'] = $this->get('security.context')->getToken()->getUser()->getUsuario();
        $data['uebUserLog'] = $this->get('security.context')->getToken()->getUser()->getUeb();
        $nameArchivo = "portadores_energeticos";

        $data['arbolizq_array'] = json_decode($data['arbolizq']);
        if (isset($data['arbolder']) && $data['arbolder'] != '[]') {
            $data['arbolder_array'] = json_decode($data['arbolder']);
            $data['empresa'] = false;
        } else {
            $data['empresa'] = true;
        }
        $data['monedaNombre'] = "";
        $data['nameArchivo'] = $nameArchivo;
        $data['nameReporte'] = $data['nombreReporte'];
        if ($data['fecha'] == "") {
            $data['fecha'] = date('d/m/Y');
            $data['fechaCierre'] = date('d/m/Y');
        }
        $a = new DatosReportes();
        $a->generarExcelPortadores($data);
    }

    /*Generar el reporte de la Produccion por UEB*/
    /**
     * @Route("/reporte/ueb",name="reporte_produccion_ueb")
     */
    public function exportarProduccionPorUebAction(Request $request)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        ini_set('memory_limit', '3072M');
        set_time_limit(3600);
        $em = $this->getDoctrine()->getManager();
        $data = $request->request->get('form');
        $data['usuarioLog'] = $this->get('security.context')->getToken()->getUser()->getUsuario();
        $data['uebUserLog'] = $this->get('security.context')->getToken()->getUser()->getUeb();
        $nameArchivo = "produccion_ueb";
        $data['show_hijos'] = isset($data['show_hijos']) ? true : false;
        $data['arbolizq_array'] = json_decode($data['arbolizq']);
        if (isset($data['arbolder']) && $data['arbolder'] != '[]') {
            $data['arbolder_array'] = json_decode($data['arbolder']);
            $data['uebNombre'] = $this->get('nomenclador.nomueb')->getUebNombre($em, $data);
            $data['empresa'] = false;
        } else {
            $data['empresa'] = true;
        }
        $data['nameArchivo'] = $nameArchivo;
        $data['nameReporte'] = $data['nombreReporte'];
        if ($data['fecha'] == "") {
            $data['fecha'] = date('d/m/Y');
            $data['fechaCierre'] = date('d/m/Y');
        }

        $a = new DatosReportes();
        $a->generarExcelProduccionUeb($data);
    }

    /*Generar el reporte del Acopio por dia*/
    /**
     * @Route("/reporte/acopiodias",name="reporte_acopio_dias")
     */

    public function exportarAcopioDias(Request $request)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        ini_set('memory_limit', '3072M');
        set_time_limit(3600);
        $em = $this->getDoctrine()->getManager();
        $data = $request->request->get('form');
        $data['usuarioLog'] = $this->get('security.context')->getToken()->getUser()->getUsuario();
        $data['uebUserLog'] = $this->get('security.context')->getToken()->getUser()->getUeb();
        $nameArchivo = "cumplimiento_acopio_produccion";
        $data['show_hijos'] = isset($data['show_hijos']) ? true : false;
        $data['arbolizq_array'] = json_decode($data['arbolizq']);
        if (isset($data['arbolder']) && $data['arbolder'] != '[]') {
            $data['arbolder_array'] = json_decode($data['arbolder']);
        } else {
            $data['arbolder_array'] = null;
        }
        $data['nameArchivo'] = $nameArchivo;
        $data['nameReporte'] = $data['nombreReporte'];
        if ($data['fecha'] == "") {
            $data['fecha'] = date('d/m/Y');
            $data['fechaCierre'] = date('d/m/Y');
        }
        $a = new DatosReportes();
        $a->generarExcelAcopioDias($data);
    }

    /*Generar el reporte del Acopio por Mes*/
    /**
     * @Route("/reporte/acopiomes",name="reporte_acopio_mes")
     */

    public function exportarAcopioMes(Request $request)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        ini_set('memory_limit', '3072M');
        set_time_limit(3600);
        $em = $this->getDoctrine()->getManager();
        $data = $request->request->get('form');
        $data['usuarioLog'] = $this->get('security.context')->getToken()->getUser()->getUsuario();
        $data['uebUserLog'] = $this->get('security.context')->getToken()->getUser()->getUeb();
        $nameArchivo = "produccion_mes_acumulado";
        $data['show_hijos'] = isset($data['show_hijos']) ? true : false;
        $data['arbolizq_array'] = json_decode($data['arbolizq']);
        if (isset($data['arbolder']) && $data['arbolder'] != '[]') {
            $data['arbolder_array'] = json_decode($data['arbolder']);
        } else {
            $data['arbolder_array'] = null;
        }

        $data['nameArchivo'] = $nameArchivo;
        $data['nameReporte'] = $data['nombreReporte'];
        if ($data['fecha'] == "") {
            $data['fecha'] = date('d/m/Y');
            $data['fechaCierre'] = date('d/m/Y');
        }

        $a = new DatosReportes();
        $a->generarDatosAcopioMes($data);
    }

    /*Generar el reporte de los Aseguramientos*/
    /**
     * @Route("/reporte/aseguramiento",name="reporte_aseguramiento")
     */

    public function exportarAseguramiento(Request $request)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        ini_set('memory_limit', '3072M');
        set_time_limit(3600);
        $em = $this->getDoctrine()->getManager();
        $data = $request->request->get('form');
        $data['usuarioLog'] = $this->get('security.context')->getToken()->getUser()->getUsuario();
        $data['uebUserLog'] = $this->get('security.context')->getToken()->getUser()->getUeb();
        $nameArchivo = "aseguramiento";
        $data['show_hijos'] = isset($data['show_hijos']) ? true : false;
        $data['arbolizq_array'] = json_decode($data['arbolizq']);
        if (isset($data['arbolder']) && $data['arbolder'] != '[]') {
            $data['arbolder_array'] = json_decode($data['arbolder']);
            $data['empresa'] = false;
        } else {
            $data['empresa'] = true;
        }
        $data['nameArchivo'] = $nameArchivo;
        $data['nameReporte'] = $data['nombreReporte'];
        if ($data['fecha'] == "") {
            $data['fecha'] = date('d/m/Y');
            $data['fechaCierre'] = date('d/m/Y');
        }
        $a = new DatosReportes();
        $a->generarDatosAseguramiento($data);

    }

    /*Generar el reporte de la Existencia diaria por UEB*/
    /**
     * @Route("/reporte/existdiariaueb",name="reporte_existencia_diaria_ueb")
     */

    public function exportarExistenciaDiariaUeb(Request $request)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        ini_set('memory_limit', '3072M');
        set_time_limit(3600);
        $em = $this->getDoctrine()->getManager();
        $data = $request->request->get('form');
        $data['show_hijos'] = isset($data['show_hijos']) ? true : false;
        $data['usuarioLog'] = $this->get('security.context')->getToken()->getUser()->getUsuario();
        $data['uebUserLog'] = $this->get('security.context')->getToken()->getUser()->getUeb();
        $nameArchivo = "existencia_diaria_ueb";

        $data['arbolizq_array'] = json_decode($data['arbolizq']);

        if (isset($data['arbolder']) && $data['arbolder'] != '[]') {
            $data['arbolder_array'] = json_decode($data['arbolder']);
        }
        $data['nameArchivo'] = $nameArchivo;
        $data['nameReporte'] = $data['nombreReporte'];
        if (isset($data['arbolder_array'])) {
            $data['uebNombre'] = $this->get('nomenclador.nomueb')->getUebNombre($em, $data);
            $data['empresa'] = false;
        } else {
            $data['empresa'] = true;
        }
        if ($data['fecha'] == "") {
            $data['fecha'] = date('d/m/Y');
            $data['fechaCierre'] = date('d/m/Y');
        }
        $a = new DatosReportes();
        $a->generarExcelExistenciaDiariaUeb($data);
    }

    /*Generar el reporte de la Producción por Grupos*/
    /**
     * @Route("/reporte/producciongrupos",name="reporte_produccion_grupos")
     */

    public function exportarProduccionGrupos(Request $request)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        ini_set('memory_limit', '3072M');
        set_time_limit(3600);
        $em = $this->getDoctrine()->getManager();
        $data = $request->request->get('form');
        $data['usuarioLog'] = $this->get('security.context')->getToken()->getUser()->getUsuario();
        $data['uebUserLog'] = $this->get('security.context')->getToken()->getUser()->getUeb();
        $nameArchivo = "produccion_diario_acumulado";
        $data['show_hijos'] = isset($data['show_hijos']) ? true : false;
        $data['arbolizq_array'] = json_decode($data['arbolizq']);
        if (isset($data['arbolder']) && $data['arbolder'] != '[]') {
            $data['arbolder_array'] = json_decode($data['arbolder']);
        } else {
            $data['arbolder_array'] = null;
        }
        $data['nameArchivo'] = $nameArchivo;
        $data['nameReporte'] = $data['nombreReporte'];
        if ($data['fecha'] == "") {
            $data['fecha'] = date('d/m/Y');
            $data['fechaCierre'] = date('d/m/Y');
        }
        $a = new DatosReportes();
        $a->generarExcelProduccionGrupos($data);
    }

    /*Generar el reporte del Cumplimiento de los Planes de Producción por Destinos*/
    /**
     * @Route("/reporte/producciondestinos",name="reporte_produccion_destinos")
     */

    public function exportarPlanesProduccionDestinos(Request $request)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        ini_set('memory_limit', '3072M');
        set_time_limit(3600);
        $em = $this->getDoctrine()->getManager();
        $data = $request->request->get('form');
        $data['usuarioLog'] = $this->get('security.context')->getToken()->getUser()->getUsuario();
        $data['uebUserLog'] = $this->get('security.context')->getToken()->getUser()->getUeb();
        $nameArchivo = "produccion_destinos";
        $data['show_hijos'] = isset($data['show_hijos']) ? true : false;
        $data['arbolizq_array'] = json_decode($data['arbolizq']);
        if (isset($data['arbolder']) && $data['arbolder'] != '[]') {
            $data['arbolder_array'] = json_decode($data['arbolder']);
            $data['empresa'] = false;
        } else {
            $data['empresa'] = true;
        }
        $data['nameArchivo'] = $nameArchivo;
        $data['nameReporte'] = $data['nombreReporte'];
        if ($data['fecha'] == "") {
            $data['fecha'] = date('d/m/Y');
            $data['fechaCierre'] = date('d/m/Y');
        }
        $a = new DatosReportes();
        $a->generarExcelPlanesProduccionDestinos($data);
    }

    /*Cumplimiento con la Canasta Familiar Normada (ORC)*/
    /**
     * @Route("/reporte/canastafamiliar",name="reporte_canasta_familiar")
     */

    public function exportarCanastaFamiliar(Request $request)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        ini_set('memory_limit', '3072M');
        set_time_limit(3600);
        $em = $this->getDoctrine()->getManager();
        $data = $request->request->get('form');
        $data['usuarioLog'] = $this->get('security.context')->getToken()->getUser()->getUsuario();
        $data['uebUserLog'] = $this->get('security.context')->getToken()->getUser()->getUeb();
        $nameArchivo = "canasta_familiar";
        $data['idueb'] = $data['ueb'];
        $data['empresa'] = $data['ueb'] != "" ? false : true;
        $data['arbolizq_array'] = json_decode($data['arbolizq']);
        $data['show_hijos'] = isset($data['show_hijos']) ? true : false;
        if (isset($data['arbolder']) && $data['arbolder'] != '[]') {
            $data['arbolder_array'] = json_decode($data['arbolder']);
            $data['empresa'] = false;
        } else {
            $data['empresa'] = true;
            $resultGrupos = $em->getRepository('NomencladorBundle:NomGrupointeres')->findByIdpadre(0);
            foreach ($resultGrupos as $valueGrupos) {
                $data['arbolder_array'][] = $valueGrupos->getIdgrupointeres();
            }
        }
        $data['nameArchivo'] = $nameArchivo;
        $data['nameReporte'] = $data['nombreReporte'];
        if ($data['fecha'] == "") {
            $data['fecha'] = date('d/m/Y');
            $data['fechaCierre'] = date('d/m/Y');
        }
        $a = new DatosReportes();
        $a->generarExcelCanastaFamiliar($data);
    }

    /*Resumen del Parte Diario de Ventas por Destino*/
    /**
     * @Route("/reporte/partemn",name="reporte_parte_monedanacional")
     */

    public function exportarParteMonedaNacional(Request $request)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        ini_set('memory_limit', '3072M');
        set_time_limit(3600);
        $em = $this->getDoctrine()->getManager();
        $data = $request->request->get('form');
        $data['usuarioLog'] = $this->get('security.context')->getToken()->getUser()->getUsuario();
        $data['uebUserLog'] = $this->get('security.context')->getToken()->getUser()->getUeb();
        $nameArchivo = "resumen_venta_destino";
        $data['idueb'] = $data['ueb'];
        $data['arbolizq_array'] = json_decode($data['arbolizq']);
        $data['show_hijos'] = isset($data['show_hijos']) ? true : false;
        if (isset($data['arbolder']) && $data['arbolder'] != '[]') {
            $data['arbolder_array'] = json_decode($data['arbolder']);
            $data['empresa'] = false;
        } else {
            $resultGrupos = $em->getRepository('NomencladorBundle:NomGrupointeres')->findByIdpadre(0);
            foreach ($resultGrupos as $valueGrupos) {
                $data['arbolder_array'][] = $valueGrupos->getIdgrupointeres();
            }
            $data['empresa'] = true;
        }
        $data['nameArchivo'] = $nameArchivo;
        $data['nameReporte'] = $data['nombreReporte'];
        if ($data['fecha'] == "") {
            $data['fecha'] = date('d/m/Y');
            $data['fechaCierre'] = date('d/m/Y');
        }
        $a = new DatosReportes();
        $a->generarExcelParteMonedaNacional($data);
    }

    /*Cumplimiento de las Ventas en MN (Parte DG)*/
    /**
     * @Route("/reporte/ventasmn",name="reporte_ventas_monedanacional")
     */

    public function exportarVentasMonedaNacional(Request $request)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        ini_set('memory_limit', '3072M');
        set_time_limit(3600);
        $em = $this->getDoctrine()->getManager();
        $data = $request->request->get('form');
        $data['usuarioLog'] = $this->get('security.context')->getToken()->getUser()->getUsuario();
        $data['uebUserLog'] = $this->get('security.context')->getToken()->getUser()->getUeb();
        $nameArchivo = "ventas_destino_totalizado";
        $data['idueb'] = $data['ueb'];
        $data['empresa'] = $data['ueb'] != "" ? false : true;
        $data['arbolizq_array'] = json_decode($data['arbolizq']);
        $data['show_hijos'] = isset($data['show_hijos']) ? true : false;
        if (isset($data['arbolder']) && $data['arbolder'] != '[]') {
            $data['arbolder_array'] = json_decode($data['arbolder']);
        } else {
            $data['arbolder_array'] = $this->get('nomenclador.nomueb')->getUebAll();

        }
        $data['nameArchivo'] = $nameArchivo;
        $data['nameReporte'] = $data['nombreReporte'];
        if ($data['fecha'] == "") {
            $data['fecha'] = date('d/m/Y');
            $data['fechaCierre'] = date('d/m/Y');
        }
        $a = new DatosReportes();
        $a->generarExcelVentasMonedaNacional($data);
    }

    /*Ventas por Producto y Grupos de Interés (1026)*/
    /**
     * @Route("/reporte/ventasprodgrup",name="reporte_ventas_productogrupo")
     */

    public function exportarVentasProductoGrupo(Request $request)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        ini_set('memory_limit', '3072M');
        set_time_limit(3600);
        $em = $this->getDoctrine()->getManager();
        $data = $request->request->get('form');
        $data['usuarioLog'] = $this->get('security.context')->getToken()->getUser()->getUsuario();
        $data['uebUserLog'] = $this->get('security.context')->getToken()->getUser()->getUeb();
        $nameArchivo = "ventas_productogrupo";
        $data['show_hijos'] = isset($data['show_hijos']) ? true : false;
        $data['arbolizq_array'] = json_decode($data['arbolizq']);
        if (isset($data['arbolder']) && $data['arbolder'] != '[]') {
            $data['arbolder_array'] = json_decode($data['arbolder']);
        } else {
            $resultGrupos = $em->getRepository('NomencladorBundle:NomGrupointeres')->findByIdpadre(0);
            foreach ($resultGrupos as $valueGrupos) {
                $data['arbolder_array'][] = $valueGrupos->getIdgrupointeres();
            }
        }
        $data['nameArchivo'] = $nameArchivo;
        $data['nameReporte'] = $data['nombreReporte'];
        if ($data['fecha'] == "") {
            $data['fecha'] = date('d/m/Y');
            $data['fechaCierre'] = date('d/m/Y');
        }
        $a = new DatosReportes();
        $a->generarExcelVentasProductoGrupo($data);
    }

    /*Parte Diario de Ventas en Divisa*/
    /**
     * @Route("/reporte/ventacucmincin",name="reporte_cuc_mincin")
     */

    public function exportarVentaCuc(Request $request)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        ini_set('memory_limit', '3072M');
        set_time_limit(3600);
        $em = $this->getDoctrine()->getManager();
        $data = $request->request->get('form');
        $data['usuarioLog'] = $this->get('security.context')->getToken()->getUser()->getUsuario();
        $data['uebUserLog'] = $this->get('security.context')->getToken()->getUser()->getUeb();
        $nameArchivo = "parte_venta_destino";
        $data['show_hijos'] = isset($data['show_hijos']) ? true : false;
        $data['arbolizq_array'] = json_decode($data['arbolizq']);
        if (isset($data['arbolder']) && $data['arbolder'] != '[]') {
            $data['arbolder_array'] = json_decode($data['arbolder']);
        } else {
            $resultGrupos = $em->getRepository('NomencladorBundle:NomGrupointeres')->findByIdpadre(0);
            foreach ($resultGrupos as $valueGrupos) {
                $data['arbolder_array'][] = $valueGrupos->getIdgrupointeres();
            }
        }
        if ($data['fecha'] == "") {
            $data['fecha'] = date('d/m/Y');
            $data['fechaCierre'] = date('d/m/Y');
        }
        $data['nameArchivo'] = $nameArchivo;
        $data['nameReporte'] = $data['nombreReporte'];
        $a = new DatosReportes();
        $a->generarExcelVentaCucMincin($data);
    }

    /**
     * @Route("/reporte/resumenventacuc",name="resumen_venta")
     */

    public function exportarResumenVentasCuc(Request $request)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        ini_set('memory_limit', '3072M');
        set_time_limit(3600);
        $em = $this->getDoctrine()->getManager();
        $data = $request->request->get('form');
        $data['usuarioLog'] = $this->get('security.context')->getToken()->getUser()->getUsuario();
        $data['uebUserLog'] = $this->get('security.context')->getToken()->getUser()->getUeb();
        $nameArchivo = "resumen_venta";
        $data['show_hijos'] = isset($data['show_hijos']) ? true : false;
        $data['arbolizq_array'] = json_decode($data['arbolizq']);
        if (isset($data['arbolder']) && $data['arbolder'] != '[]') {
            $data['arbolder_array'] = json_decode($data['arbolder']);
            $data['idueb'] = implode(",", $data['arbolder_array']);
            $data['empresa'] = false;
        } else {
            //$data['arbolder_array'] = $this->get('nomenclador.nomueb')->getUebAll();
            $data['empresa'] = true;
        }
        $data['nameArchivo'] = $nameArchivo;
        $data['nameReporte'] = $data['nombreReporte'];
        if ($data['fecha'] == "") {
            $data['fecha'] = date('d/m/Y');
            $data['fechaCierre'] = date('d/m/Y');
        }
        $a = new DatosReportes();
        $a->generarExcelResumenVentasCuc($data);
    }

    /**
     * @Route("/reporte/ventaproducto",name="reporte_venta_producto")
     */

    public function exportarVentaCucProductoAction(Request $request)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        ini_set('memory_limit', '3072M');
        set_time_limit(3600);
        $em = $this->getDoctrine()->getManager();
        $data = $request->request->get('form');
        $data['usuarioLog'] = $this->get('security.context')->getToken()->getUser()->getUsuario();
        $data['uebUserLog'] = $this->get('security.context')->getToken()->getUser()->getUeb();
        $nameArchivo = "venta_producto";
        $data['show_hijos'] = isset($data['show_hijos']) ? true : false;
        if (empty($data['ueb']))
            $data['empresa'] = true;
        $data['arbolizq_array'] = json_decode($data['arbolizq']);
        if (isset($data['arbolder']) && $data['arbolder'] != '[]') {
            $data['arbolder_array'] = json_decode($data['arbolder']);
        } else {
            $resultGrupos = $em->getRepository('NomencladorBundle:NomGrupointeres')->findByIdpadre(0);
            foreach ($resultGrupos as $valueGrupos) {
                $data['arbolder_array'][] = $valueGrupos->getIdgrupointeres();
            }
        }
        $data['nameArchivo'] = $nameArchivo;
        $data['nameReporte'] = $data['nombreReporte'];
        if ($data['fecha'] == "") {
            $data['fecha'] = date('d/m/Y');
            $data['fechaCierre'] = date('d/m/Y');
        }
        $a = new DatosReportes();
        $a->generarExcelVentaCucProducto($data);
    }

    /*Cumplimiento de las ventas en divisa*/
    /**
     * @Route("/reporte/venta",name="reporte_venta")
     */
    public function exportarVentaCucAction(Request $request)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        ini_set('memory_limit', '3072M');
        set_time_limit(3600);
        $em = $this->getDoctrine()->getManager();
        $data = $request->request->get('form');
        $data['usuarioLog'] = $this->get('security.context')->getToken()->getUser()->getUsuario();
        $data['uebUserLog'] = $this->get('security.context')->getToken()->getUser()->getUeb();
        $nameArchivo = "venta_producto";
        $data['idueb'] = $data['ueb'];
        $data['show_hijos'] = isset($data['show_hijos']) ? true : false;
        $data['arbolizq_array'] = json_decode($data['arbolizq']);
        if (isset($data['arbolder']) && $data['arbolder'] != '[]') {
            $data['arbolder_array'] = json_decode($data['arbolder']);
        } else {
            $resultGrupos = $em->getRepository('NomencladorBundle:NomGrupointeres')->findByIdpadre(0);
            foreach ($resultGrupos as $valueGrupos) {
                $data['arbolder_array'][] = $valueGrupos->getIdgrupointeres();
            }
        }
        $data['nameArchivo'] = $nameArchivo;
        $data['nameReporte'] = $data['nombreReporte'];
        if ($data['fecha'] == "") {
            $data['fecha'] = date('d/m/Y');
            $data['fechaCierre'] = date('d/m/Y');
        }
        $a = new DatosReportes();
        $a->generarExcelVentaCuc($data);
    }

    /**
     * @Route("/reporte/resumenventames",name="resumen_venta_mes")
     */

    public function exportarResumenVentaMesAction(Request $request)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        ini_set('memory_limit', '3072M');
        set_time_limit(3600);
        $em = $this->getDoctrine()->getManager();
        $data = $request->request->get('form');
        $data['usuarioLog'] = $this->get('security.context')->getToken()->getUser()->getUsuario();
        $data['uebUserLog'] = $this->get('security.context')->getToken()->getUser()->getUeb();
        $nameArchivo = "ventaproducto";
        $data['show_hijos'] = isset($data['show_hijos']) ? true : false;
        if ($data['fecha'] == "") {
            $data['fecha'] = date('d/m/Y');
            $data['fechaCierre'] = date('d/m/Y');
        }
        $data['arbolizq_array'] = json_decode($data['arbolizq']);
        if (isset($data['arbolder']) && $data['arbolder'] != '[]') {
            $data['arbolder_array'] = json_decode($data['arbolder']);
        } else {
            $data['arbolder_array'] = $this->get('nomenclador.nomueb')->getUebAll();
        }
        $data['nameArchivo'] = $nameArchivo;
        $data['nameReporte'] = $data['nombreReporte'];
        $a = new DatosReportes();
        $a->generarExcelResumenVentaMes($data);
    }

    /**
     * @Route("/reporte/partecad",name="resumen_parte_cad")
     */

    public function exportarParteVentaCadAction(Request $request)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        ini_set('memory_limit', '3072M');
        set_time_limit(3600);
        $em = $this->getDoctrine()->getManager();
        $data = $request->request->get('form');
        $data['usuarioLog'] = $this->get('security.context')->getToken()->getUser()->getUsuario();
        $data['uebUserLog'] = $this->get('security.context')->getToken()->getUser()->getUeb();
        $nameArchivo = "venta_cad";

        $data['arbolizq_array'] = json_decode($data['arbolizq']);
        if (isset($data['arbolder']) && $data['arbolder'] != '[]') {
            $data['arbolder_array'] = json_decode($data['arbolder']);
        } else {
            $data['arbolder_array'] = $this->get('nomenclador.nomueb')->getUebAll();
        }
        $data['nameArchivo'] = $nameArchivo;
        $data['nameReporte'] = $data['nombreReporte'];
        if ($data['fecha'] == "") {
            $data['fecha'] = date('d/m/Y');
            $data['fechaCierre'] = date('d/m/Y');
            $data['fechaCierre'] = date('d/m/Y');
        }
        $a = new DatosReportes();
        $a->generarExcelParteVentaCad($data);
    }

    /**
     * @Route("/reporte/vinculo",name="reporte_vinculo")
     */

    public function exportarVinculosAction(Request $request)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        ini_set('memory_limit', '3072M');
        set_time_limit(3600);
        $em = $this->getDoctrine()->getManager();
        $data = $request->request->get('form');
        $data['usuarioLog'] = $this->get('security.context')->getToken()->getUser()->getUsuario();
        $data['uebUserLog'] = $this->get('security.context')->getToken()->getUser()->getUeb();
        $nameArchivo = "vinculos";
        $data['idueb'] = $data['ueb'];
        $data['empresa'] = $data['ueb'] != "" ? false : true;
        $data['show_hijos'] = isset($data['show_hijos']) ? true : false;
        if ($data['fecha'] == "") {
            $data['fecha'] = date('d/m/Y');
            $data['fechaCierre'] = date('d/m/Y');
        }
        $data['arbolizq_array'] = json_decode($data['arbolizq']);
        if (isset($data['arbolder']) && $data['arbolder'] != '[]') {
            $data['arbolder_array'] = json_decode($data['arbolder']);
            $data['arboldernombre_array'] = $this->get('nomenclador.nomentidad')->getNombreByIds(json_decode($data['arbolder']));
        } else {
            $data['arbolder_array'] = $this->get('nomenclador.nomentidad')->getVinculosAll();
            $data['arboldernombre_array'] = $this->get('nomenclador.nomentidad')->getNombreVinculosAll();
        }
        $data['nameArchivo'] = $nameArchivo;
        $data['nameReporte'] = $data['nombreReporte'];

        $a = new DatosReportes();
        $a->generarExcelCumpVinculos($data);
    }

    /*Reporte del Consumo de Materia Prima*/
    /**
     * @Route("/reporte/materiaprimabalance",name="materiaprima_balance")
     */

    public function exportarMateriaPrimaBalance(Request $request)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        ini_set('memory_limit', '3072M');
        set_time_limit(3600);
        $em = $this->getDoctrine()->getManager();
        $data = $request->request->get('form');
        $data['usuarioLog'] = $this->get('security.context')->getToken()->getUser()->getUsuario();
        $data['uebUserLog'] = $this->get('security.context')->getToken()->getUser()->getUeb();
        $nameArchivo = "cons_aseg_balance";
        $data['show_hijos'] = isset($data['show_hijos']) ? true : false;
        if ($data['fecha'] == "") {
            $data['fecha'] = date('d/m/Y');
            $data['fechaCierre'] = date('d/m/Y');
        }
        $data['arbolizq_array'] = json_decode($data['arbolizq']);
        if (isset($data['arbolder']) && $data['arbolder'] != '[]') {
            $data['arbolder_array'] = $this->get('nomenclador.nomaseguramiento')->getAsegByIds(json_decode($data['arbolder']));
        } else {
            $data['arbolder_array'] = array();
        }

        $data['nameArchivo'] = $nameArchivo;
        $data['nameReporte'] = $data['nombreReporte'];
        $a = new DatosReportes();
        $a->generarExcelMateriaPrimaBalance($data);
    }

    /**
     * @Route("/reporte/puntocontrol",name="reporte_produccion_formato")
     */

    public function exportarProduccionFormato(Request $request)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        ini_set('memory_limit', '3072M');
        set_time_limit(3600);
        $em = $this->getDoctrine()->getManager();
        $data = $request->request->get('form');
        $data['usuarioLog'] = $this->get('security.context')->getToken()->getUser()->getUsuario();
        $data['uebUserLog'] = $this->get('security.context')->getToken()->getUser()->getUeb();
        $nameArchivo = "punto_control";
        $data['show_hijos'] = isset($data['show_hijos']) ? true : false;
        if ($data['fecha'] == "") {
            $data['fecha'] = date('d/m/Y');
            $data['fechaCierre'] = date('d/m/Y');
        }
        $data['arbolizq_array'] = json_decode($data['arbolizq']);

        if (isset($data['arbolder']) && $data['arbolder'] != '[]') {
            $data['arbolder_array'] = json_decode($data['arbolder']);
            $data['empresa'] = false;
        } else {
            $data['arbolder_array'] = null;
            $data['empresa'] = true;
        }

        $data['nameArchivo'] = $nameArchivo;
        $data['nameReporte'] = $data['nombreReporte'];
        $a = new DatosReportes();
        $a->generarProduccionFormato($data);
    }
}



