<?php

namespace Leads\Pipes;

use App\Lead;
use App\Leads\Pipes\ValidateName;
use Tests\TestCase;

class ValidateNameTest extends TestCase
{

        /** @test */
    public function nullNameIsInvalid()
    {
        $lead = new Lead();

        $pipe = new ValidateName();

        $this->assertNull($lead->firstname);
        $this->assertNull($lead->lastname);
        $pipe->handle($lead, function ($lead) {
            $this->assertFalse($lead->valid);
        });
    }

    /** @test */
    public function emptyNameIsInvalid()
    {
        $lead = new Lead(['firstname' => '']);

        $pipe = new ValidateName();

        $pipe->handle($lead, function ($lead) {
            $this->assertFalse($lead->valid);
        });
    }

    /**
    * @dataProvider validNamesProvider
    *
    * @param $name
    * @test
    */
    public function normalNamesIsPassing($name)
    {
        $lead = new Lead(['firstname' => $name]);

        $pipe = new ValidateName();

        $pipe->handle($lead, function ($lead) {
            $this->assertTrue($lead->valid);
        });
    }

    /**
     * @dataProvider bullshitNamesProvider
     *
     * @param $name
     * @test
     */
    public function bullshitNamesIsRejected($name)
    {
        $lead = new Lead(['firstname' => $name]);

        $pipe = new ValidateName();

        $pipe->handle($lead, function ($lead) {
            $this->assertFalse($lead->valid);
        });
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
