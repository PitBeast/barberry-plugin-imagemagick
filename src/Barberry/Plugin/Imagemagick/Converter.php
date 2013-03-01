<?php
namespace Barberry\Plugin\Imagemagick;
use Barberry\Plugin;
use Barberry\ContentType;

class Converter implements Plugin\InterfaceConverter
{
    /**
     * @var string
     */
    private $tempPath;

    /**
     * @var ContentType
     */
    private $targetContentType;

    public function configure(ContentType $targetContentType, $tempPath)
    {
        $this->tempPath = $tempPath;
        $this->targetContentType = $targetContentType;
        return $this;
    }

    public function convert($bin, Plugin\InterfaceCommand $command = null)
    {

        if ($command->cropping()) {
            $gravity = '-gravity NorthWest';
            switch ($command->cropping()) {
                case 'w':
                    $resize = '-resize x' . $command->height();
                    $crop = $gravity . ' -crop ' . $command->width() . 'x+0';
                    break;
                case 'h':
                    $resize = '-resize ' . $command->width() . 'x';
                    $crop = $gravity . ' -crop x' . $command->height() . '+0';
                    break;
                default:
                    $resize = ($command->width() || $command->height()) ?
                        '-resize ' . $command->width() . 'x' . $command->height() : '';
                    $crop = '';
            }
        } else {
            $resize = ($command->width() || $command->height()) ?
                '-resize ' . $command->width() . 'x' . $command->height() : '';
            $crop = '';
        }
        $source = tempnam($this->tempPath, "imagemagick_");
        chmod($source, 0664);
        $destination = $source . '.' . $this->targetContentType->standardExtension();
        file_put_contents($source, $bin);
        exec(
            'convert -auto-orient ' . $resize . ' ' . $crop . ' ' . $source . ' ' . $destination
        );
        if (is_file($destination)) {
            $bin = file_get_contents($destination);
            unlink($destination);
        }
        unlink($source);

        return $bin;
    }
}
