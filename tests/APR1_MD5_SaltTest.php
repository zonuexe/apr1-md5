<?php

declare(strict_types=1);

namespace zonuexe;

class APR1_MD5_SaltTest extends \PHPUnit\Framework\TestCase
{
    public function testSaltType(): void
    {
        $this->assertIsString(Apr1md5::salt());
    }

    public function testSaltPattern(): void
    {
        $this->assertMatchesRegularExpression('/.{8}/', Apr1md5::salt());
    }

    public function testSaltRamdomness(): void
    {
        $this->assertNotEquals(Apr1md5::salt(), Apr1md5::salt());
    }
}
