<?php
namespace Barberry\Plugin\Imagemagick;

use Barberry\Plugin\InterfaceCommand;

class Command implements InterfaceCommand
{
    const MAX_WIDTH = 800;
    const MAX_HEIGHT = 600;

    private $width;
    private $height;

    private $cropping = null;

    /**
     * @param string $commandString
     * @return self
     */
    public function configure($commandString)
    {
        $params = explode("_", $commandString);
        for ($i=0; $i<count($params); $i++) {
            switch ($i) {
                case 0:
                    if (preg_match("@^([\d]*)x([\d]*)$@", $params[$i], $regs)) {
                        $this->width = strlen($regs[1]) ? (int)$regs[1] : null;
                        $this->height = strlen($regs[2]) ? (int)$regs[2] : null;
                    }
                    break;
                case 1:
                    if ($this->width && $this->height && preg_match("@^([wh])$@", $params[$i], $regs)) {
                        $this->cropping = $regs[1];
                    }
                    break;
            }
        }
        return $this;
    }

    public function conforms($commandString)
    {
        return strval($this) === $commandString;
    }

    public function width()
    {
        return min($this->width, self::MAX_WIDTH);
    }

    public function height()
    {
        return min($this->height, self::MAX_HEIGHT);
    }

    public function cropping()
    {
        return $this->cropping;
    }

    public function __toString()
    {
        $croppingPart = ($this->width && $this->height && $this->cropping) ? strval('_' . $this->cropping) : '';
        return (($this->width || $this->height) ? strval($this->width . 'x' . $this->height) : '') . $croppingPart;
    }
}
