<?php
class Pessoa
{
    public function lista($param = null)
    {
        $lista[0] = 'Jose Delgado';
        $lista[1] = 'William Buckland ';
        $lista[3] = 'Francis Crick';
        $lista[3] = 'Jack Parsons';

        sleep(2);
        return $param ? $lista[0] : $lista;

    }
}