<?php
/**
 * This file is part of FacturaScripts
 * Copyright (C) 2015-2019 Carlos Garcia Gomez <carlos@facturascripts.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */
namespace FacturaScripts\Core\Model;

use FacturaScripts\Core\Base\Utils;

/**
 * Merchandise transport agency.
 *
 * @author Carlos García Gómez  <carlos@facturascripts.com>
 * @author Artex Trading sa     <jcuello@artextrading.com>
 */
class AgenciaTransporte extends Base\ModelClass
{

    use Base\ModelTrait;

    /**
     * Contains True if is enabled.
     *
     * @var bool
     */
    public $activo;

    /**
     * Primary key. Varchar(8).
     *
     * @var string
     */
    public $codtrans;

    /**
     * Name of the agency.
     *
     * @var string
     */
    public $nombre;

    /**
     *
     * @var string
     */
    public $telefono;

    /**
     *
     * @var string
     */
    public $web;

    /**
     * Reset the values of all model properties.
     */
    public function clear()
    {
        parent::clear();
        $this->activo = true;
    }

    /**
     * Returns the name of the column that is the primary key of the model.
     *
     * @return string
     */
    public static function primaryColumn()
    {
        return 'codtrans';
    }

    /**
     * Returns the name of the table that uses this model.
     *
     * @return string
     */
    public static function tableName()
    {
        return 'agenciastrans';
    }

    /**
     * 
     * @return bool
     */
    public function test()
    {
        $this->codtrans = empty($this->codtrans) ? (string) $this->newCode() : trim($this->codtrans);
        if (!preg_match('/^[A-Z0-9_\+\.\-]{1,8}$/i', $this->codtrans)) {
            self::$miniLog->alert(self::$i18n->trans('invalid-alphanumeric-code', ['%value%' => $this->codtrans, '%column%' => 'codtrans', '%min%' => '1', '%max%' => '8']));
            return false;
        }

        $this->nombre = Utils::noHtml($this->nombre);
        $this->telefono = Utils::noHtml($this->telefono);
        $this->web = Utils::noHtml($this->web);
        return parent::test();
    }
}
