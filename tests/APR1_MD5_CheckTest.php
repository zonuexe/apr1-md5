<?php

declare(strict_types=1);

namespace zonuexe;

class APR1_MD5_CheckTest extends \PHPUnit\Framework\TestCase
{

    public function testHash_WhiteHat101(): void
    {
        $this->assertTrue(
            Apr1md5::check('WhiteHat101','$apr1$HIcWIbgX$G9YqNkCVGlFAN63bClpoT/')
        );
    }

    public function testHash_apache(): void
    {
        $this->assertTrue(
            Apr1md5::check('apache','$apr1$rOioh4Wh$bVD3DRwksETubcpEH90ww0')
        );
    }

    public function testHash_ChangeMe1(): void
    {
        $this->assertTrue(
            Apr1md5::check('ChangeMe1','$apr1$PVWlTz/5$SNkIVyogockgH65nMLn.W1')
        );
    }

}
