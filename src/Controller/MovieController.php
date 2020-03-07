<?php


namespace Jayson\Movie\LocalMovie\Controller;



use Jayson\Movie\LocalMovie\Scan\Read;
use Jayson\Movie\LocalMovie\Scan\Sweep;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MovieController extends AbstractController
{

    /**
     * @param array $movies
     * @return array
     * @throws \Exception
     */
    private function moviesTimeSort(array $movies = []): array
    {
        if (empty($movies)) {
            return $movies;
        };
        $newMovies = [];
        $nowDate = new \DateTime();
        foreach ($movies as $index => $movie) {
            $key = $movie['date'] . str_pad($index, 5, 0, STR_PAD_LEFT);
            $movie['date'] = date('Y-m-d', $movie['date']);
            $newMovies[$key] = $movie;
        }
        krsort($newMovies);
        return $newMovies;
    }

    /**
     * @param Read $read
     * @return Response
     * @throws \Exception
     */
    public function index(Read $read):Response
    {
        return $this->render('@LocalMovie/movies.html.twig', [
            'movies' => $this->moviesTimeSort($read->fetchAll())
        ]);
    }

    /**
     * @Route("/movies/scan", name="local.movies.scan", methods={"POST"})
     * @param Sweep $sweep
     * @return JsonResponse
     */
    public function scan(Sweep $sweep): JsonResponse
    {
        $sweep->storage();
        return JsonResponse::create([
            'success' => true
        ]);
    }

    /**
     * @Route("/movies/search", name="local.movies.search", methods={"POST"})
     * @param Read $read
     * @param Request $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function search(Read $read, Request $request): JsonResponse
    {
        $movieName = $request->get('movieName') ?? '';
        if (empty($movieName)) {
            $movies = $read->fetchAll();
        } else {
            $movies = $read->findBy($movieName);
        }
        return JsonResponse::create($this->moviesTimeSort($movies));
    }

    /**
     * @Route("/movies/play", name="local.movies.play", methods={"GET", "POST"})
     * @param Read $read
     * @param Request $request
     * @return JsonResponse
     */
    public function play(Read $read, Request $request): JsonResponse
    {
        try {
			setlocale(LC_ALL, 'zh_CN.UTF-8');
            $movieDir = $request->get('movieDir') ?? '';
            $movieName = $request->get('movieName') ?? '';
            if (empty($movieDir . DIRECTORY_SEPARATOR . $movieName)) {
                throw new \LogicException('文件不正确');
            }
			$file = str_replace("\\", '/', $movieDir . DIRECTORY_SEPARATOR . $movieName);
            $movieInfo = pathinfo(substr($file, strrpos($file, '/')));
            $movies = $read->findBy($movieInfo['filename']);
            if (empty($movies)) {
                throw new \LogicException('文件不存在');
            }
            @exec(sprintf('%s%s%s', current($movies)['dir'], DIRECTORY_SEPARATOR,  current($movies)['name']));
            return JsonResponse::create(['ok']);
        } catch (\Exception $exception) {
            return JsonResponse::create(['message'=>$exception->getMessage()], 401);
        }
    }
}