<?php
namespace App\Application\Actions\Webscraping;

class WebscrapingAction
{
    private $courses;
    private $urlScraping;

    function __construct()
    {
        $this->courses = [];
        $this->urlScraping = "https://www.tecgurus.net/cursos";
    }

    public function getCourses(string $search)
    {
        $this->scrapCourses();
        foreach ($this->courses as $index => $course) {
            $searchIn = strpos(strtolower($course["name"]), strtolower($search));
            if ($searchIn === false) {
                unset($this->courses[$index]);
            }
        }
        return $this->courses;
    }

    private function scrapCourses()
    {
        $client = new \Goutte\Client();
        $crawler = $client->request('GET', $this->urlScraping);
        $scrap = $crawler->filter('figcaption')->each(function ($node) {
            $course = [
                "name" => $node->filter('a')->text(),
                "url" => $node->filter("a")->attr('href')
            ];
            array_push($this->courses, $course);
        });
    }
  }
