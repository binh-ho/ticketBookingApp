import React, { useRef, useState, useEffect } from 'react';
import axios from 'axios';
import { Link, useNavigate} from 'react-router-dom';
import Textfield from "@atlaskit/textfield";
import Button from "@atlaskit/button";
import Popup from 'reactjs-popup';

export default function UserTicket() {
    const nameRef = useRef<HTMLInputElement>(null);
    const showTimeRef = useRef<HTMLSelectElement>(null);
    const classRef = useRef<HTMLSelectElement>(null);
    const idRef = useRef<HTMLInputElement>(null);
    const [token, setToken] = useState(localStorage.getItem('ACCESS_TOKEN'));
    const [ticketList, setList] = useState<Ticket[]>([]);
    const [isLoading, setLoading] = useState(true);
    const navigate = useNavigate();

    interface Ticket {
        id: number;
        name: string;
        show_time: string;
        class: string;
    }

    useEffect(() => {
        getTickets();
    }, []);

    function getTickets() {
        // Mock API call
        axios.get(`http://localhost:8000/api/users/haveTicket`,
            {
                headers: {
                    Authorization: `Bearer ${token}`
                }
            })
            .then(res => {
                console.log(res);
                // Update the UI by removing the deleted ticket
                setList(res.data.ticket);
                setLoading(false);
            })
            .catch(err => {
                console.error("Request failed:", err);
            });
    }

    function searchTicket(event: React.FormEvent) {
        event.preventDefault();
        const id = idRef.current?.value || 0;
        // Mock search
        setList([{
            id: 1,
            name: "dragon ball y",
            show_time: "night",
            class: "vip"
        }]);
        setLoading(false);
    }

    function deleteTicket(ticketId: number) {
        axios.delete(`http://localhost:8000/api/users/dropTicket/${ticketId}`,
            {
                headers: {
                    Authorization: `Bearer ${token}`
                }
            })
            .then(res => {
                console.log(res);
                // Update the UI by removing the deleted ticket
                setList(prevList => prevList.filter(ticket => ticket.id !== ticketId));
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

    function editTicket(ticketId: number) {
        const updatedTicket = {
            name: nameRef.current?.value || "",
            show_time: showTimeRef.current?.value || "",
            class: classRef.current?.value || ""
        };
        axios.put(`http://localhost:8000/api/users/updateTicket/${ticketId}`, updatedTicket,
            {
                headers: {
                    Authorization: `Bearer ${token}`
                }
            })
            .then(res => {
                console.log(res);
                // Update the UI by mapping over the ticket list and updating the edited ticket
                setList(prevList =>
                    prevList.map(ticket =>
                        ticket.id === ticketId ? { ...ticket, ...updatedTicket } : ticket
                    )
                );
            })
            .catch(err => {
                console.error("Request failed:", err);
            });
    }

    const PromptPopup = ({ ticket }: { ticket: Ticket }) => {
        return (
            <Popup trigger={<Button>Edit</Button>} position="right center">
                <div>
                    <form onSubmit={(e) => { e.preventDefault(); editTicket(ticket.id); }}>
                        <input ref={nameRef} defaultValue={ticket.name} type="text" placeholder="Movie name" /><br /><br />
                        <select ref={showTimeRef} defaultValue={ticket.show_time}>
                            <option value="morning">morning</option>
                            <option value="noon">noon</option>
                            <option value="night">night</option>
                        </select><br /><br />
                        <select ref={classRef} defaultValue={ticket.class}>
                            <option value="normal">normal</option>
                            <option value="vip">vip</option>
                        </select><br /><br />
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
                <Link to='/booking'>Book ticket</Link><br /><br />
            </div>
            <div>
                <form onSubmit={searchTicket}>
                    <input ref={idRef} placeholder='ticket_id' />
                    <Button type="submit">Search</Button>
                </form>
            </div>
            <div>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Movie name</th>
                            <th>Showtime</th>
                            <th>Class</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    {isLoading ? (
                        <tbody>
                            <tr>
                                <td colSpan={5}>Loading...</td>
                            </tr>
                        </tbody>
                    ) : (
                        <tbody>
                            {ticketList.map(ticket => (
                                <tr key={ticket.id}>
                                    <td>{ticket.id}</td>
                                    <td>{ticket.name}</td>
                                    <td>{ticket.show_time}</td>
                                    <td>{ticket.class}</td>
                                    <td>
                                        <PromptPopup ticket={ticket} />
                                        &nbsp;
                                        <Button onClick={() => deleteTicket(ticket.id)}>Delete</Button>
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    )}
                </table>
            </div>
        </>
    );
}
