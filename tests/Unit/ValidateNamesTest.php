<?php

namespace Tests\Unit;

use App\Rules\ObsceneCensorRus;
use PHPUnit\Framework\TestCase;

class ValidateNamesTest extends TestCase
{
    /**
     * @dataProvider validNamesProvider
     *
     * @param $name
     * @test
     */
    public function normalNamesIsPassing($name)
    {
        $this->assertTrue(ObsceneCensorRus::isAllowed($name));
    }

    /**
     * @dataProvider bullshitNamesProvider
     *
     * @param $name
     * @test
     */
    public function bullshitNamesIsRejected($name)
    {
        $this->assertFalse(ObsceneCensorRus::isAllowed($name), "Name " . $name . ' passed, but invalid');
    }

    /**
     * Provides valid names for tests
     *
     * @return \Generator
     */
    public function validNamesProvider()
    {
        yield ["Эллина Викторовна Демидас"];
        yield ["Сергей Александрович Филатов"];
        yield ["Сергей Владимирович Лаппо"];
        yield ["Олег Михайлович Мокринский"];
        yield ["Степан Сергеевич Хуторной"];
        yield ["Али Усманович Рахманов"];
        yield ["Людмила Пантелеева Анатольевна Пантелеева"];
        yield ["Юрий Алексеевич Горбачев"];
        yield ["Walerij Tadeewiz Boqyslawez"];
        yield ["Ильдус Исмагилович Дашкин"];
    }

    /**
     * Provides invalid names for tests
     *
     * @return \Generator
     */
    public function bullshitNamesProvider()
    {
        yield ["Ебанько Лохотроновское"];
        yield ["хуимя хуечистов"];
        yield ["Алексей Викторович Лохов "];
        yield ["Лохи Конченные Придурки"];
        yield ["Совсем Сволочи Охерели"];
    }
}
