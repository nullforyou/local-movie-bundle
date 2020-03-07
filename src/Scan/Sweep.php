<?php


namespace Jayson\Movie\LocalMovie\Scan;


use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Yaml;

class Sweep
{

    private $storageDir;

    private $filesystem;

    private $movies;

    private $logger;

    public function __construct(array $storageDir = [], Filesystem $filesystem, LoggerInterface $appLogger = null)
    {
        $this->storageDir = $storageDir;
        $this->filesystem = $filesystem;
        $this->logger = $appLogger;
    }


    public function storage():void
    {
        foreach (array_unique($this->storageDir) as $item) {
            if (!is_dir($item)) {
                continue;
            }
            $this->scanDirectory($item);
        }
        $dir = 'movieStorage' . DIRECTORY_SEPARATOR;
        sort($this->movies);
        $yamlStr = Yaml::dump($this->movies);

        $filesystem = new Filesystem();
        if (!$filesystem->exists($dir)) {
            $this->filesystem->mkdir($dir);
        }
        $this->filesystem->dumpFile($dir. 'movie.yaml', $yamlStr);
    }

    private function scanDirectory(string $dir): void
    {
        $handle = @opendir($dir);
        while($file = readdir($handle))
        {
            if (in_array($file, array('.','..'))) {
                continue;
            }
            $splFileInfo = new \SplFileInfo($dir . DIRECTORY_SEPARATOR . $file);
            if ($splFileInfo->getType() == 'dir') {
                $this->scanDirectory($dir . DIRECTORY_SEPARATOR . $file);
            } else {
                if ($splFileInfo->getExtension() == 'torrent') {
                    continue;
                }
                if (empty($movies[$file])) {
                    $this->movies[$file]['dir'] = $splFileInfo->getPath();
                    $this->movies[$file]['name'] = $file;
                    $this->movies[$file]['date'] = (new \DateTime())->setTimestamp($splFileInfo->getMTime());
                } else {
                    $this->movies[$file]['dir'] = sprintf('%s|%s', $this->movies[$file]['dir'], $splFileInfo->getPath());
                    $moviesDate = (new \DateTime())->setTimestamp($splFileInfo->getMTime());
                    if ($this->movies[$file]['date'] > new $moviesDate) {
                        $this->movies[$file]['date'] = $moviesDate;
                    }
                }
            }
        }
    }
}