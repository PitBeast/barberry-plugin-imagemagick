<?php
namespace Barberry\Plugin\Imagemagick;

class CommandTest extends \PHPUnit_Framework_TestCase
{
    public function testNoWidthByDefault()
    {
        $this->assertNull(self::command()->width());
    }

    public function testNoHeightByDefault()
    {
        $this->assertNull(self::command()->height());
    }

    public function testReadsWidthOnly()
    {
        $this->assertEquals(123, self::command('123x')->width());
        $this->assertNull(self::command()->height());
    }

    public function testReadsHeightOnly()
    {
        $this->assertNull(self::command()->width());
        $this->assertEquals(123, self::command('x123')->height());
    }

    public function testReadsBothWidthAndHeight()
    {
        $command = self::command('321x123');
        $this->assertEquals(321, $command->width());
        $this->assertEquals(123, $command->height());
    }

    public function testAmbiguityTest()
    {
        $this->assertFalse(self::command('200sda2x100')->conforms('200x100'));
        $this->assertTrue(self::command('')->conforms(''));
    }

    public function testReplaceInitialSizesIntoMaxAndMin()
    {
        $command = self::command('9000x9000');
        $this->assertEquals($command::MAX_WIDTH, $command->width());
        $this->assertEquals($command::MAX_HEIGHT, $command->height());
    }

    public function testInitialCommandStringEqualsObjectToStringConvertion()
    {
        $command = self::command('9000x9000');
        $this->assertSame(strval($command),'9000x9000');
    }

    public function testCropThenReadsBothWidthAndHeight()
    {
        $command = self::command('321x123_w');
        $this->assertEquals('w', $command->cropping());

        $command = self::command('x123_w');
        $this->assertNull($command->cropping());

        $command = self::command('321x_h');
        $this->assertNull($command->cropping());
    }

//--------------------------------------------------------------------------------------------------

    private static function command($commandString = null)
    {
        $command = new Command();
        return $command->configure($commandString);
    }
}
