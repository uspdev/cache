<?php
// classe a ser utilizada como fake data para os testes
class Pessoa
{
    public static function lista($param = null)
    {
        $lista[0] = 'Jose Delgado - aumentado para não ser dado pequeeeeeeenooooooooo';
        $lista[1] = 'William Buckland ';
        $lista[2] = 'Francis Crick';
        $lista[3] = 'Jack Parsons';

        sleep(2);
        return $param ? $lista[0] : $lista;
    }

    public static function lista2($param1, $param2)
    {
        $lista[0] = 'Jose Delgado - aumentado para não ser dado pequeeeeeeenooooooooo';
        $lista[1] = 'William Buckland ';
        $lista[2] = 'Francis Crick';
        $lista[3] = 'Jack Parsons';

        sleep(2);
        return $param1 ? $lista[0] : $lista;

    }
}
