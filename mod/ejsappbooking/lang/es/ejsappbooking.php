<?php

// This file is part of the Moodle module "EJSApp booking system"
//
// EJSApp booking system is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// EJSApp booking system is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// The GNU General Public License is available on <http://www.gnu.org/licenses/>
//
// EJSApp booking system has been developed by:
//  - Javier Pavon: javi.pavon@gmail.com
//  - Luis de la Torre: ldelatorre@dia.uned.es
//	- Ruben Heradio: rheradio@issi.uned.es
//
//  at the Computer Science and Automatic Control, Spanish Open University
//  (UNED), Madrid, Spain


/**
 * English strings for ejsappbooking
 *
 * @package    mod
 * @subpackage ejsappbooking
 * @copyright  2012 Javier Pavon, Luis de la Torre and Ruben Heradio
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['modulename'] = 'Sistema de reservas EJSApp';
$string['modulenameplural'] = 'Sistemas de reservas EJSApp';
$string['modulename_help'] = 'El m&oacute;dulo de recurso EJSAppBooking permite a los usuarios de Moodle reservas franjas de tiempo para experimentaci&oacute;n real y remota usando los applets creados con Easy Java Simulations (EJS) y subidos a los cursos de Moodle mediante el m&oacute;dulo de actividad EJSApp.

Este recurso a&ntilde;ade una aplicaci&oacute;n Java que muestra una lista de los laboratorios remotos disponibles para el usuario y le permite seleccionar una reserva para cualquier d&iacute;a y hora deseados.

El sistema de reservas consiste en dos partes: el cliente de reservas y el servidor de reservas. Mientras que la aplicaci&oacute;n del cliente de reservas se a&ntilde;ade con este m&oacute;dulo, el servidor de reservas necesita estar en ejecuci&oacute;n en el servidor que aloja el portal de Moodle. Puedes encontrar esta aplicaci&oacute;n en tu carpeta /mod/ejsappbooking/applets/BookingServer/';
$string['view_error'] = 'No se pudo abrir la aplicaci&oacute;n del sistema de reservas.';
$string['ejsappbookingname'] = 'Nombre del sistema de reservas EJSApp';
$string['ejsappbookingname_help'] = 'Nombre a mostrar para el sistema de reservas EJSApp en tu curso de Moodle.';
$string['ejsappbooking'] = 'EJSAppBooking';
$string['pluginadministration'] = 'Administraci&oacute;n de EJSAppBooking';
$string['pluginname'] = 'EJSAppBooking';

$string['manage_access_but'] = 'Gestionar el acceso de usuarios';

//select_rem_lab.php
$string['selectRemLab_pageTitle'] = 'Selecci&oacute;n de laboratorio remoto';
$string['rem_lab_selection'] = 'Seleccione un laboratorio remoto';
$string['select_users_but'] = 'Fijar permisos de usuarios para este laboratorio';
$string['no_rem_labs'] = 'No hay laboratorios remotos en este curso';

//select_users.php
$string['selectUsers_pageTitle'] = 'Selecci&oacute;n de usuarios';
$string['users_selection'] = 'Seleccione los usuarios a los que dar&aacute; permisos de reserva en el laboratorio remoto seleccionado';
$string['accept_users_but'] = 'Aceptar';
$string['save_changes'] = 'Guardar cambios';
$string['booking_rights'] = 'Permiso de reserva';

//Send_messages.php:
$string['allow_remlabaccess'] = 'Ha recibido permisos para realizar reservas en un nuevo laboratorio remoto: ';
$string['sending_message'] = 'Enviando mensajes de aviso';

//lin.php
$string['mail_subject'] = 'Alerta de Laboratorio Inactivo';
$string['mail_content1'] = 'Uno de tus laboratorios remotos previamente operativos (';
$string['mail_content2'] = ' - IP: ';
$string['mail_content3'] = ') ha dejado de estar accesible.';

$string['already_enabled'] = 'Ya tiene un sistema de reservas en este curso.';