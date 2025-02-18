<?php
/**
 * This file is part of FacturaScripts
 * Copyright (C) 2013-2019 Carlos Garcia Gomez <carlos@facturascripts.com>
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
 * The agent/employee is the one associated with a delivery note, invoice o box.
 * Each user can be associated with an agent, an an agent can
 * can be associated with several user of none at all.
 *
 * @author Carlos García Gómez <carlos@facturascripts.com>
 * @author Artex Trading sa    <jcuello@artextrading.com>
 */
class Agente extends Base\Contact
{

    use Base\ModelTrait;

    /**
     * Position in the company.
     *
     * @var string
     */
    public $cargo;

    /**
     * Primary key. Varchar (10).
     *
     * @var string
     */
    public $codagente;

    /**
     * True -> the agent no longer buys us or we do not want anything with him.
     *
     * @var boolean
     */
    public $debaja;

    /**
     * Date of withdrawal from the company.
     *
     * @var string
     */
    public $fechabaja;

    /**
     * Default contact data
     *
     * @var integer
     */
    public $idcontacto;

    /**
     * Link to product model for settle commission
     *
     * @var int
     */
    public $idproducto;

    /**
     * Returns the addresses associated with the provider.
     *
     * @return Contacto
     */
    public function getContact()
    {
        $contact = new Contacto();
        $contact->loadFromCode($this->idcontacto);
        return $contact;
    }

    /**
     * Returns settlement product.
     * 
     * @return Producto
     */
    public function getProduct()
    {
        $product = new Producto();
        $product->loadFromCode($this->idproducto);
        return $product;
    }

    /**
     * This function is called when creating the model table. Returns the SQL
     * that will be executed after the creation of the table. Useful to insert values
     * default.
     *
     * @return string
     */
    public function install()
    {
        /// needed dependencies
        new Producto();

        return parent::install();
    }

    /**
     * Returns the name of the column that is the model's primary key.
     *
     * @return string
     */
    public static function primaryColumn()
    {
        return 'codagente';
    }

    /**
     * Returns the description of the column that is the model's primary key.
     *
     * @return string
     */
    public function primaryDescriptionColumn()
    {
        return 'nombre';
    }

    /**
     * Returns the name of the table that uses this model.
     *
     * @return string
     */
    public static function tableName()
    {
        return 'agentes';
    }

    /**
     * Check employee / agent data, return TRUE if correct.
     *
     * @return bool
     */
    public function test()
    {
        $this->cargo = Utils::noHtml($this->cargo);
        $this->codagente = empty($this->codagente) ? (string) $this->newCode() : trim($this->codagente);
        if (!preg_match('/^[A-Z0-9_\+\.\-]{1,10}$/i', $this->codagente)) {
            self::$miniLog->alert(self::$i18n->trans('invalid-alphanumeric-code', ['%value%' => $this->codagente, '%column%' => 'codagente', '%min%' => '1', '%max%' => '10']));
            return false;
        }

        $this->debaja = !empty($this->fechabaja);
        return parent::test();
    }

    /**
     *
     * @param array $values
     *
     * @return bool
     */
    protected function saveInsert(array $values = [])
    {
        if (parent::saveInsert($values)) {
            /// creates new contact
            $contact = new Contacto();
            $contact->cifnif = $this->cifnif;
            $contact->codagente = $this->codagente;
            $contact->descripcion = $this->nombre;
            $contact->email = $this->email;
            $contact->nombre = $this->nombre;
            $contact->telefono1 = $this->telefono1;
            $contact->telefono2 = $this->telefono2;
            if ($contact->save()) {
                $this->idcontacto = $contact->idcontacto;
                return $this->save();
            }

            return true;
        }

        return false;
    }
}
