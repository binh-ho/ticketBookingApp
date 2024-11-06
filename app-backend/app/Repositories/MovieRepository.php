<?php

namespace App\Repositories;

use App\Models\Movie;

class MovieRepository
{
    protected $movie;

    public function __construct(Movie $movie)
    {
        $this->movie = $movie;
    }

    public function insert(array $movieData)
    {
        $newMovie = new Movie();
        $newMovie->name = $movieData['name']; // Assuming 'name' is a column in your 'movies' table
        $newMovie->save(); // Save the new movie to the database

        return $newMovie;
    }

    public function index()
    {
        return $this->movie->all();
    }

    public function findByID($id)
    {
        return $this->movie->where('id', $id)->first();
    }
}
