<?php


namespace Jayson\Movie\LocalMovie\Scan;


use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Yaml;

class Read
{

    private $filesystem;

    private $file;

    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
        $this->file = 'movieStorage' . DIRECTORY_SEPARATOR . 'movie.yaml';
    }

    public function findBy(string $subMovieName): array
    {
        $hasMovies = [];
        foreach ($this->fetchAll() as $value) {
            if (false !== strpos($value['name'], $subMovieName)) {
                $hasMovies[] = $value;
            }
        }
        return $hasMovies;
    }

    public function fetchAll(): array
    {
        if (!$this->filesystem->exists($this->file)) {
            throw new \Exception('资源不存在');
        }
        return Yaml::parseFile($this->filesystem->readlink($this->file, true)) ?? [];
    }
}