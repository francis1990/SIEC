<?php

namespace NomencladorBundle\DataFixtures\ORM;

use AdminBundle\Entity\DatConfig;
use AdminBundle\Entity\Permiso;
use AdminBundle\Entity\Rol;
use AdminBundle\Entity\Usuario;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use NomencladorBundle\Entity\NomUeb;

class CargarIncialAdministracionFixture extends AbstractFixture implements OrderedFixtureInterface
{
    public function getOrder()
    {
        return 1;
    }

    public function load(ObjectManager $manager)
    {
        //$this->loadCargarIncial($manager);
    }

    private function loadCargarIncial(ObjectManager $manager)
    {
        $roles = array("Superadministrador");
        $permisos = array(
            array("Adicionar Admin Entidad", "ROLE_ADICIONAR_ADMINENTIDAD"),
            array("Modificar Admin Entidad", "ROLE_MODIFICAR_ADMINENTIDAD"),
            array("Listar Admin Entidad", "ROLE_LISTAR_ADMINENTIDAD"),
            array("Eliminar Historial", "ROLE_ELIMINAR_HISTORIAL"),
            array("Adicionar Rol", "ROLE_ADICIONAR_ROL"),
            array("Modificar Rol", "ROLE_MODIFICAR_ROL"),
            array("Eliminar Rol", "ROLE_ELIMINAR_ROL"),
            array("Listar Rol", "ROLE_LISTAR_ROL"),
            array("Adicionar Usuario", "ROLE_ADICIONAR_USUARIO"),
            array("Modificar Usuario", "ROLE_MODIFICAR_USUARIO"),
            array("Eliminar Usuario", "ROLE_ELIMINAR_USUARIO"),
            array("Listar Usuario", "ROLE_LISTAR_USUARIO"),
            array("Salvar Base de Datos", "ROLE_SALVAR_BD"),
            array("Listar Historial", "ROLE_LISTAR_HISTORIAL"),
            array("Adicionar DPA", "ROLE_ADICIONAR_DPA"),
            array("Modificar DPA", "ROLE_MODIFICAR_DPA"),
            array("Eliminar DPA", "ROLE_ELIMINAR_DPA"),
            array("Listar DPA", "ROLE_LISTAR_DPA"),
            array("Adicionar UEB", "ROLE_ADICIONAR_UEB"),
            array("Modificar UEB", "ROLE_MODIFICAR_UEB"),
            array("Eliminar UEB", "ROLE_ELIMINAR_UEB"),
            array("Listar UEB", "ROLE_LISTAR_UEB"),
            array("Adicionar Tipo de Entidad", "ROLE_ADICIONAR_TIPOENTIDAD"),
            array("Modificar Tipo de Entidad", "ROLE_MODIFICAR_TIPOENTIDAD"),
            array("Eliminar Tipo de Entidad", "ROLE_ELIMINAR_TIPOENTIDAD"),
            array("Listar Tipo de Entidad", "ROLE_LISTAR_TIPOENTIDAD"),
            array("Adicionar Entidad", "ROLE_ADICIONAR_ENTIDAD"),
            array("Modificar Entidad", "ROLE_MODIFICAR_ENTIDAD"),
            array("Eliminar Entidad", "ROLE_ELIMINAR_ENTIDAD"),
            array("Listar Entidad", "ROLE_LISTAR_ENTIDAD"),
            array("Adicionar Grupo de Interés", "ROLE_ADICIONAR_GRUPOINTERES"),
            array("Modificar Grupo de Interés", "ROLE_MODIFICAR_GRUPOINTERES"),
            array("Eliminar Grupo de Interés", "ROLE_ELIMINAR_GRUPOINTERES"),
            array("Listar Grupo de Interés", "ROLE_LISTAR_GRUPOINTERES"),
            array("Adicionar Unidad de Medida", "ROLE_ADICIONAR_UM"),
            array("Modificar Unidad de Medida", "ROLE_MODIFICAR_UM"),
            array("Eliminar Unidad de Medida", "ROLE_ELIMINAR_UM"),
            array("Listar Unidad de Medida", "ROLE_LISTAR_UM"),
            array("Adicionar Conversión", "ROLE_ADICIONAR_CONVERSION"),
            array("Modificar Conversión", "ROLE_MODIFICAR_CONVERSION"),
            array("Eliminar Conversión", "ROLE_ELIMINAR_CONVERSION"),
            array("Listar Conversión", "ROLE_LISTAR_CONVERSION"),
            array("Adicionar Génerico", "ROLE_ADICIONAR_GENERICO"),
            array("Modificar Génerico", "ROLE_MODIFICAR_GENERICO"),
            array("Eliminar Génerico", "ROLE_ELIMINAR_GENERICO"),
            array("Listar Génerico", "ROLE_LISTAR_GENERICO"),
            array("Adicionar SubGénerico", "ROLE_ADICIONAR_SUBGENERICO"),
            array("Modificar SubGénerico", "ROLE_MODIFICAR_SUBGENERICO"),
            array("Eliminar SubGénerico", "ROLE_ELIMINAR_SUBGENERICO"),
            array("Listar SubGénerico", "ROLE_LISTAR_SUBGENERICO"),
            array("Adicionar Específico", "ROLE_ADICIONAR_ESPECIFICO"),
            array("Modificar Específico", "ROLE_MODIFICAR_ESPECIFICO"),
            array("Eliminar Específico", "ROLE_ELIMINAR_ESPECIFICO"),
            array("Listar Específico", "ROLE_LISTAR_ESPECIFICO"),
            array("Adicionar Tipo Específico", "ROLE_ADICIONAR_TIPOESPECIFICO"),
            array("Modificar Tipo Específico", "ROLE_MODIFICAR_TIPOESPECIFICO"),
            array("Eliminar Tipo Específico", "ROLE_ELIMINAR_TIPOESPECIFICO"),
            array("Listar Tipo Específico", "ROLE_LISTAR_TIPOESPECIFICO"),
            array("Adicionar Formato", "ROLE_ADICIONAR_FORMATO"),
            array("Modificar Formato", "ROLE_MODIFICAR_FORMATO"),
            array("Eliminar Formato", "ROLE_ELIMINAR_FORMATO"),
            array("Listar Formato", "ROLE_LISTAR_FORMATO"),
            array("Adicionar Sabor/Cualidad", "ROLE_ADICIONAR_SABORCUALIDAD"),
            array("Modificar Sabor/Cualidad", "ROLE_MODIFICAR_SABORCUALIDAD"),
            array("Eliminar Sabor/Cualidad", "ROLE_ELIMINAR_SABORCUALIDAD"),
            array("Listar Sabor/Cualidad", "ROLE_LISTAR_SABORCUALIDAD"),
            array("Adicionar Producto", "ROLE_ADICIONAR_PRODUCTO"),
            array("Modificar Producto", "ROLE_MODIFICAR_PRODUCTO"),
            array("Eliminar Producto", "ROLE_ELIMINAR_PRODUCTO"),
            array("Listar Producto", "ROLE_LISTAR_PRODUCTO"),
            array("Adicionar Precio", "ROLE_ADICIONAR_PRECIO"),
            array("Modificar Precio", "ROLE_MODIFICAR_PRECIO"),
            array("Eliminar Precio", "ROLE_ELIMINAR_PRECIO"),
            array("Listar Precio", "ROLE_LISTAR_PRECIO"),
            array("Adicionar Almacén", "ROLE_ADICIONAR_ALMACEN"),
            array("Modificar Almacén", "ROLE_MODIFICAR_ALMACEN"),
            array("Eliminar Almacén", "ROLE_ELIMINAR_ALMACEN"),
            array("Listar Almacén", "ROLE_LISTAR_ALMACEN"),
            array("Adicionar Aseguramiento", "ROLE_ADICIONAR_ASEGURAMIENTO"),
            array("Modificar Aseguramiento", "ROLE_MODIFICAR_ASEGURAMIENTO"),
            array("Eliminar Aseguramiento", "ROLE_ELIMINAR_ASEGURAMIENTO"),
            array("Listar Aseguramiento", "ROLE_LISTAR_ASEGURAMIENTO"),
            array("Adicionar Normas de Consumo", "ROLE_ADICIONAR_NORMACONSUMO"),
            array("Modificar Normas de Consumo", "ROLE_MODIFICAR_NORMACONSUMO"),
            array("Eliminar Normas de Consumo", "ROLE_ELIMINAR_NORMACONSUMO"),
            array("Listar Normas de Consumo", "ROLE_LISTAR_NORMACONSUMO"),
            array("Adicionar Ruta", "ROLE_ADICIONAR_RUTA"),
            array("Modificar Ruta", "ROLE_MODIFICAR_RUTA"),
            array("Eliminar Ruta", "ROLE_ELIMINAR_RUTA"),
            array("Listar Ruta", "ROLE_LISTAR_RUTA"),
            array("Adicionar Tipo de Transporte", "ROLE_ADICIONAR_TIPOTRANSPORTE"),
            array("Modificar Tipo de Transporte", "ROLE_MODIFICAR_TIPOTRANSPORTE"),
            array("Eliminar Tipo de Transporte", "ROLE_ELIMINAR_TIPOTRANSPORTE"),
            array("Listar Tipo de Transporte", "ROLE_LISTAR_TIPOTRANSPORTE"),
            array("Adicionar Tipo de Incidencia", "ROLE_ADICIONAR_TIPOINCIDENCIA"),
            array("Modificar Tipo de Incidencia", "ROLE_MODIFICAR_TIPOINCIDENCIA"),
            array("Eliminar Tipo de Incidencia", "ROLE_ELIMINAR_TIPOINCIDENCIA"),
            array("Listar Tipo de Incidencia", "ROLE_LISTAR_TIPOINCIDENCIA"),
            array("Adicionar Clasificación de Incidencia", "ROLE_ADICIONAR_CLASIFINCIDENCIA"),
            array("Modificar Clasificación de Incidencia", "ROLE_MODIFICAR_CLASIFINCIDENCIA"),
            array("Eliminar Clasificación de Incidencia", "ROLE_ELIMINAR_CLASIFINCIDENCIA"),
            array("Listar Clasificación de Incidencia", "ROLE_LISTAR_CLASIFINCIDENCIA"),
            array("Adicionar Portadores", "ROLE_ADICIONAR_PORTADOR"),
            array("Modificar Portadores", "ROLE_MODIFICAR_PORTADOR"),
            array("Eliminar Portadores", "ROLE_ELIMINAR_PORTADOR"),
            array("Listar Portadores", "ROLE_LISTAR_PORTADOR"),
            array("Adicionar Cuenta Contable", "ROLE_ADICIONAR_CUENTACONTABLE"),
            array("Modificar Cuenta Contable", "ROLE_MODIFICAR_CUENTACONTABLE"),
            array("Eliminar Cuenta Contable", "ROLE_ELIMINAR_CUENTACONTABLE"),
            array("Listar Cuenta Contable", "ROLE_LISTAR_CUENTACONTABLE"),
            array("Adicionar Ejercicio", "ROLE_ADICIONAR_EJERCICIO"),
            array("Modificar Ejercicio", "ROLE_MODIFICAR_EJERCICIO"),
            array("Eliminar Ejercicio", "ROLE_ELIMINAR_EJERCICIO"),
            array("Listar Ejercicio", "ROLE_LISTAR_EJERCICIO"),
            array("Adicionar Tipo de Plan", "ROLE_ADICIONAR_TIPOPLAN"),
            array("Modificar Tipo de Plan", "ROLE_MODIFICAR_TIPOPLAN"),
            array("Eliminar Tipo de Plan", "ROLE_ELIMINAR_TIPOPLAN"),
            array("Listar Tipo de Plan", "ROLE_LISTAR_TIPOPLAN"),
            array("Adicionar Moneda/Destino", "ROLE_ADICIONAR_MONEDADESTINO"),
            array("Modificar Moneda/Destino", "ROLE_MODIFICAR_MONEDADESTINO"),
            array("Eliminar Moneda/Destino", "ROLE_ELIMINAR_MONEDADESTINO"),
            array("Listar Moneda/Destino", "ROLE_LISTAR_MONEDADESTINO"),
            array("Adicionar Concepto", "ROLE_ADICIONAR_CONCEPTO"),
            array("Modificar Concepto", "ROLE_MODIFICAR_CONCEPTO"),
            array("Eliminar Concepto", "ROLE_ELIMINAR_CONCEPTO"),
            array("Listar Concepto", "ROLE_LISTAR_CONCEPTO"),
            array("Adicionar Medidor de Portador", "ROLE_ADICIONAR_MEDIDORPORTADOR"),
            array("Modificar Medidor de Portador", "ROLE_MODIFICAR_MEDIDORPORTADOR"),
            array("Eliminar Medidor de Portador", "ROLE_ELIMINAR_MEDIDORPORTADOR"),
            array("Listar Medidor de Portador", "ROLE_LISTAR_MEDIDORPORTADOR"),
            array("Adicionar Plan de Acopio", "ROLE_ADICIONAR_PLANACOPIO"),
            array("Modificar Plan de Acopio", "ROLE_MODIFICAR_PLANACOPIO"),
            array("Eliminar Plan de Acopio", "ROLE_ELIMINAR_PLANACOPIO"),
            array("Listar Plan de Acopio", "ROLE_LISTAR_PLANACOPIO"),
            array("Adicionar Plan de Aseguramiento", "ROLE_ADICIONAR_PLANASEGURAMIENTO"),
            array("Modificar Plan de Aseguramiento", "ROLE_MODIFICAR_PLANASEGURAMIENTO"),
            array("Eliminar Plan de Aseguramiento", "ROLE_ELIMINAR_PLANASEGURAMIENTO"),
            array("Listar Plan de Aseguramiento", "ROLE_LISTAR_PLANASEGURAMIENTO"),
            array("Adicionar Plan de Desvío", "ROLE_ADICIONAR_PLANDESVIO"),
            array("Modificar Plan de Desvío", "ROLE_MODIFICAR_PLANDESVIO"),
            array("Eliminar Plan de Desvío", "ROLE_ELIMINAR_PLANDESVIO"),
            array("Listar Plan de Desvío", "ROLE_LISTAR_PLANDESVIO"),
            array("Adicionar Plan de Portadores Energéticos", "ROLE_ADICIONAR_PLANPORTADORES"),
            array("Modificar Plan de Portadores Energéticos", "ROLE_MODIFICAR_PLANPORTADORES"),
            array("Eliminar Plan de Portadores Energéticos", "ROLE_ELIMINAR_PLANPORTADORES"),
            array("Listar Plan de Portadores Energéticos", "ROLE_LISTAR_PLANPORTADORES"),
            array("Adicionar Plan de Producción", "ROLE_ADICIONAR_PLANPRODUCCION"),
            array("Modificar Plan de Producción", "ROLE_MODIFICAR_PLANPRODUCCION"),
            array("Eliminar Plan de Producción", "ROLE_ELIMINAR_PLANPRODUCCION"),
            array("Listar Plan de Producción", "ROLE_LISTAR_PLANPRODUCCION"),
            array("Adicionar Plan de Venta", "ROLE_ADICIONAR_PLANVENTA"),
            array("Modificar Plan de Venta", "ROLE_MODIFICAR_PLANVENTA"),
            array("Eliminar Plan de Venta", "ROLE_ELIMINAR_PLANVENTA"),
            array("Listar Plan de Venta", "ROLE_LISTAR_PLANVENTA"),
            array("Adicionar Parte de Acopio", "ROLE_ADICIONAR_PARTEACOPIO"),
            array("Modificar Parte de Acopio", "ROLE_MODIFICAR_PARTEACOPIO"),
            array("Eliminar Parte de Acopio", "ROLE_ELIMINAR_PARTEACOPIO"),
            array("Listar Parte de Acopio", "ROLE_LISTAR_PARTEACOPIO"),
            array("Adicionar Parte de Aseguramiento", "ROLE_ADICIONAR_PARTEASEGURAMIENTO"),
            array("Modificar Parte de Aseguramiento", "ROLE_MODIFICAR_PARTEASEGURAMIENTO"),
            array("Eliminar Parte de Aseguramiento", "ROLE_ELIMINAR_PARTEASEGURAMIENTO"),
            array("Listar Parte de Aseguramiento", "ROLE_LISTAR_PARTEASEGURAMIENTO"),
            array("Adicionar Parte de Portadores Energéticos", "ROLE_ADICIONAR_PARTEPORTADORES"),
            array("Modificar Parte de Portadores Energéticos", "ROLE_MODIFICAR_PARTEPORTADORES"),
            array("Eliminar Parte de Portadores Energéticos", "ROLE_ELIMINAR_PARTEPORTADORES"),
            array("Listar Parte de Portadores Energéticos", "ROLE_LISTAR_PARTEPORTADORES"),
            array("Adicionar Parte de Nivel Actividad", "ROLE_ADICIONAR_PARTENIVELACTV"),
            array("Modificar Parte de Nivel Actividad", "ROLE_MODIFICAR_PARTENIVELACTV"),
            array("Eliminar Parte de Nivel Actividad", "ROLE_ELIMINAR_PARTENIVELACTV"),
            array("Listar Parte de Nivel Actividad", "ROLE_LISTAR_PARTENIVELACTV"),
            array("Adicionar Parte de Venta", "ROLE_ADICIONAR_PARTEVENTA"),
            array("Modificar Parte de Venta", "ROLE_MODIFICAR_PARTEVENTA"),
            array("Eliminar Parte de Venta", "ROLE_ELIMINAR_PARTEVENTA"),
            array("Listar Parte de Venta", "ROLE_LISTAR_PARTEVENTA"),
            array("Adicionar Parte de Cuentas por Cobrar", "ROLE_ADICIONAR_PARTECUENTAS"),
            array("Modificar Parte de Cuentas por Cobrar", "ROLE_MODIFICAR_PARTECUENTAS"),
            array("Eliminar Parte de Cuentas por Cobrar", "ROLE_ELIMINAR_PARTECUENTAS"),
            array("Listar Parte de Cuentas por Cobrar", "ROLE_LISTAR_PARTECUENTAS"),
            array("Adicionar Parte de Economía", "ROLE_ADICIONAR_PARTEECONOMIA"),
            array("Modificar Parte de Economía", "ROLE_MODIFICAR_PARTEECONOMIA"),
            array("Eliminar Parte de Economía", "ROLE_ELIMINAR_PARTEECONOMIA"),
            array("Listar Parte de Economía", "ROLE_LISTAR_PARTEECONOMIA"),
            array("Adicionar Parte de Mercancía por Vínculo", "ROLE_ADICIONAR_PARTEMERCANCIA"),
            array("Modificar Parte de Mercancía por Vínculo", "ROLE_MODIFICAR_PARTEMERCANCIA"),
            array("Eliminar Parte de Mercancía por Vínculo", "ROLE_ELIMINAR_PARTEMERCANCIA"),
            array("Listar Parte de Mercancía por Vínculo", "ROLE_LISTAR_PARTEMERCANCIA"),
            array("Adicionar Parte de Movimiento de Almacén", "ROLE_ADICIONAR_PARTEMOVIMIENTO"),
            array("Modificar Parte de Movimiento de Almacén", "ROLE_MODIFICAR_PARTEMOVIMIENTO"),
            array("Eliminar Parte de Movimiento de Almacén", "ROLE_ELIMINAR_PARTEMOVIMIENTO"),
            array("Listar Parte de Movimiento de Almacén", "ROLE_LISTAR_PARTEMOVIMIENTO"),
            array("Adicionar Parte de Consumo de Materias Primas", "ROLE_ADICIONAR_PARTECONSUMO"),
            array("Modificar Parte de Consumo de Materias Primas", "ROLE_MODIFICAR_PARTECONSUMO"),
            array("Eliminar Parte de Consumo de Materias Primas", "ROLE_ELIMINAR_PARTECONSUMO"),
            array("Listar Parte de Consumo de Materias Primas", "ROLE_LISTAR_PARTECONSUMO"),
            array("Adicionar Parte de Transporte", "ROLE_ADICIONAR_PARTETRANSPORTE"),
            array("Modificar Parte de Transporte", "ROLE_MODIFICAR_PARTETRANSPORTE"),
            array("Eliminar Parte de Transporte", "ROLE_ELIMINAR_PARTETRANSPORTE"),
            array("Listar Parte de Transporte", "ROLE_LISTAR_PARTETRANSPORTE"),
            array("Adicionar Incidencia", "ROLE_ADICIONAR_INCIDENCIA"),
            array("Modificar Incidencia", "ROLE_MODIFICAR_INCIDENCIA"),
            array("Eliminar Incidencia", "ROLE_ELIMINAR_INCIDENCIA"),
            array("Listar Incidencia", "ROLE_LISTAR_INCIDENCIA"),
            array("Adicionar Alerta", "ROLE_ADICIONAR_ALERTA"),
            array("Modificar Alerta", "ROLE_MODIFICAR_ALERTA"),
            array("Eliminar Alerta", "ROLE_ELIMINAR_ALERTA"),
            array("Listar Alerta", "ROLE_LISTAR_ALERTA"),
            array("Generar Reporte", "ROLE_GENERAR_REPORTE")
        );
        $usuarios = array("administrador", "nhDr7OyKlXQju+Ge/WKGrPQ9lPBSUFfpK+B1xqx/+8zLZqRNX0+5G1zBQklXUFy86lCpkAofsExlXiorUcKSNQ==", "admin@coppelia.cu");
        $ueb = array("01", "Oficina Central");
        $datosEmpresa = array("Empresa de Productos Lácteos Coppelia.", "4.99", "calle 10 % Avenida 5ta y 31 Playa La Habana");

        $empresa = new DatConfig();
        $empresa->setFechaTrabajo(new \DateTime());
        $empresa->setNombreEntidad($datosEmpresa[0]);
        $empresa->setReupEntidad($datosEmpresa[1]);
        $empresa->setReupEntidad($datosEmpresa[2]);
        $manager->persist($empresa);

        $entidadRol = new Rol();
        $entidadRol->setDescRol($roles[0]);
        $entidadRol->setActivo(1);
        $manager->persist($entidadRol);

        $entidadUeb = new NomUeb();
        $entidadUeb->setCodigo($ueb[0]);
        $entidadUeb->setNombre($ueb[1]);
        $entidadUeb->setActivo(1);
        $manager->persist($entidadUeb);

        $entidad = new Usuario();
        $entidad->setUsuario($usuarios[0]);
        $entidad->setPassword($usuarios[1]);
        $entidad->setActivo(1);
        $entidad->setCorreo($usuarios[2]);
        $entidad->setRol($entidadRol);
        $entidad->setUeb($entidadUeb);
        $manager->persist($entidad);

        foreach ($permisos as $permiso) {
            $entidadPer = new Permiso();
            $entidadPer->setDescPermiso($permiso[0]);
            $entidadPer->setAlias($permiso[1]);
            $entidadPer->addRole($entidadRol);
            $manager->persist($entidadPer);
            $entidadRol->addPermiso($entidadPer);
        }
        $manager->flush();
    }

}