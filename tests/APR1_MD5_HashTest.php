<?php

namespace zonuexe;

class APR1_MD5_HashTest extends \PHPUnit\Framework\TestCase
{

    public function testHash_WhiteHat101(): void
    {
        $this->assertEquals(
            '$apr1$HIcWIbgX$G9YqNkCVGlFAN63bClpoT/',
            Apr1md5::hash('WhiteHat101','HIcWIbgX')
        );
    }

    public function testHash_apache(): void
    {
        $this->assertEquals(
            '$apr1$rOioh4Wh$bVD3DRwksETubcpEH90ww0',
            Apr1md5::hash('apache','rOioh4Wh')
        );
    }

    public function testHash_ChangeMe1(): void
    {
        $this->assertEquals(
            '$apr1$PVWlTz/5$SNkIVyogockgH65nMLn.W1',
            Apr1md5::hash('ChangeMe1','PVWlTz/5')
        );
    }

    // Test some awkward inputs

    public function testHash_ChangeMe1_blankSalt(): void
    {
        $this->assertEquals(
            '$apr1$$DbHa0iITto8vNFPlkQsBX1',
            Apr1md5::hash('ChangeMe1','')
        );
    }

    public function testHash_ChangeMe1_longSalt(): void
    {
        $this->assertEquals(
            '$apr1$PVWlTz/5$SNkIVyogockgH65nMLn.W1',
            Apr1md5::hash('ChangeMe1','PVWlTz/50123456789')
        );
    }

    public function testHash_ChangeMe1_nullSalt(): void
    {
        $hash = Apr1md5::hash('ChangeMe1');
        $this->assertEquals(37, strlen($hash));
    }

    public function testHash__nullSalt(): void
    {
        $hash = Apr1md5::hash('');
        $this->assertEquals(37, strlen($hash));
    }

    // a null password gets coerced into the blank string.
    // is this sensible?
    public function testHash_null_nullSalt(): void
    {
        $hash = Apr1md5::hash('');
        $this->assertEquals(37, strlen($hash));
    }
}
