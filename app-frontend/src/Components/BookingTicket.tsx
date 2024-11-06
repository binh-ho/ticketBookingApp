import React, { useState, useEffect } from 'react';
import axios from 'axios';
import Textfield from "@atlaskit/textfield";
import Button from "@atlaskit/button";
import Popup from 'reactjs-popup';
import { Link, useNavigate } from 'react-router-dom';

interface Movie {
    id: number;
    name: string;
}

export default function MovieBooking() {
    const [movieList, setMovieList] = useState<Movie[]>([]);
    const [name, setName] = useState("");
    const [showTime, setShowTime] = useState("morning");
    const [movieClass, setMovieClass] = useState("normal");
    const [token, setToken] = useState(localStorage.getItem('ACCESS_TOKEN'));
    const [isSearching, setIsSearching] = useState(false);
    const [isLoading, setLoading] = useState(true);
    const [message, setMessage] = useState("");
    const navigate = useNavigate();

    useEffect(() => {
        if (!token) {
            navigate('/login'); // Redirect if no token
        } else {
            getMovies();
        }
    }, [token]);

    function getMovies() {
        axios.get(`http://localhost:8000/api/users/getMovie`, {
            headers: { Authorization: `Bearer ${token}` }
        })
        .then(res => {
            setMovieList(res.data.movie || []);
            setLoading(false);
        })
        .catch(err => {
            console.error("Request failed:", err);
            setLoading(false);
        });
    }

    function bookTicket(event: React.FormEvent) {
        event.preventDefault();
        const ticket = { name, show_time: showTime, class: movieClass };

        axios.post(`http://localhost:8000/api/users/buyTicket`, ticket, {
            headers: { Authorization: `Bearer ${token}` }
        })
        .then(res => {
            console.log(res);
            getMovies(); // Refresh the movie list after booking
        })
        .catch(err => {
            console.error("Request failed:", err);
        });
    }

    function handleSearch(event: React.FormEvent) {
        event.preventDefault();
        setIsSearching(true);
        // Implement search logic here
    }

    function editMovie(movieId: number, updatedName: string) {
        const updatedMovie = { name: updatedName, show_time: showTime, class: movieClass };

        axios.put(`http://localhost:8000/api/users/updateMovie/${movieId}`, updatedMovie, {
            headers: { Authorization: `Bearer ${token}` }
        })
        .then(res => {
            console.log(res);
            if(res.status === 200)
            {
                setMovieList(prevList =>
                    prevList.map(movie => movie.id === movieId ? { ...movie, name: updatedName } : movie)
                );
            }
        })
        .catch(err => {
            console.error("Request failed:", err);
        });
    }

    function deleteMovie(movieId: number) {
        axios.delete(`http://localhost:8000/api/users/dropMovie/${movieId}`, {
            headers: { Authorization: `Bearer ${token}` }
        })
        .then(res => {
            console.log(res);
            if(res.status === 200)
            {
                setMovieList(prevList => prevList.filter(movie => movie.id !== movieId));
            }
        })
        .catch(err => {
            console.error("Request failed:", err);
        });

    }

    function addMovie() {
        const movie = { name };
        axios.post(`http://localhost:8000/api/users/addMovie`, movie, {
            headers: { Authorization: `Bearer ${token}` }
        })
        .then(res => {
            console.log(res);
            getMovies();
        })
        .catch(err => {
            console.error("Request failed:", err);
        });
    }

    function logOut() {
        axios.post(`http://localhost:8000/api/users/logout`, {}, {
            headers: { Authorization: `Bearer ${token}` }
        })
        .then(() => {
            localStorage.removeItem('ACCESS_TOKEN');
            setToken(null);
            navigate('/login'); // Redirect after logout
        })
        .catch(err => console.error("Logout failed:", err));
    }

    const PromptPopup = ({ movie }: { movie: Movie }) => {
        const [editName, setEditName] = useState(movie.name);

        return (
            <Popup trigger={<Button>Edit</Button>} position="left center">
                <div>
                    <form onSubmit={(e) => { e.preventDefault(); editMovie(movie.id, editName); }}>
                        <input
                            type="text"
                            value={editName}
                            onChange={(e) => setEditName(e.target.value)}
                            placeholder="Movie name"
                        />
                        <br /><br />
                        <Button type="submit">Submit</Button>
                    </form>
                </div>
            </Popup>
        );
    };

    return (
        <>  
            <div>
                <Button type="button" onClick={logOut}>Log out</Button>
                <h1>Ticket List</h1>
                <Link to='/user'>Tickets list</Link><br /><br />
            </div>
            <div>
                <form onSubmit={bookTicket}>
                    <input
                        value={name}
                        onChange={(e) => setName(e.target.value)}
                        type="text"
                        placeholder="Movie name"
                    />
                    <select value={showTime} onChange={(e) => setShowTime(e.target.value)}>
                        <option value="morning">morning</option>
                        <option value="noon">noon</option>
                        <option value="night">night</option>
                    </select>
                    <select value={movieClass} onChange={(e) => setMovieClass(e.target.value)}>
                        <option value="normal">normal</option>
                        <option value="vip">vip</option>
                    </select>
                    <Button type="submit">Book</Button>
                    <Button type="button" onClick={handleSearch}>Search</Button>
                    <Button type="button" onClick={addMovie}>Add</Button>
                </form>
                {isSearching && <p>Search for {name}</p>}
            </div>
            {isLoading ? (
                <div><p>Loading...</p></div>
            ) : (
                <div style={{ marginTop: '20px' }}>           
                    {movieList.map((movie) => (
                        <Textfield
                            elemAfterInput={
                                <>
                                    <PromptPopup movie={movie} />
                                    <Button appearance="primary" onClick={() => deleteMovie(movie.id)}>
                                        Delete
                                    </Button>
                                </>
                            }
                            style={{ padding: "2px 4px", marginBottom: "10px" }}
                            key={movie.id}
                            value={movie.name}
                            isReadOnly
                        />
                    ))}
                </div>
            )}
        </>
    );
}
