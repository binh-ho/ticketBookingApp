import React, { useRef, useState } from 'react';
import axios from 'axios';
import { useNavigate } from "react-router-dom";

export default function Login() {
    const nameRef = useRef<HTMLInputElement>(null);
    const emailRef = useRef<HTMLInputElement>(null);
    const passwordRef = useRef<HTMLInputElement>(null);
    const [token, setToken] = useState(localStorage.getItem('ACCESS_TOKEN'));
    const [status, setStatus] = useState("login...");
    const [isLogin, setIsLogin] = useState(true);
    const navigate = useNavigate();

    function loginSubmit(event: React.FormEvent) {
        event.preventDefault();

        const user = {
            email: emailRef.current?.value || "",
            password: passwordRef.current?.value || "",
        };

        axios.post(`http://localhost:8000/api/login`, user)
            .then(res => {
                const receivedToken = res.data.token;
                setToken(receivedToken);

                if (receivedToken) {
                    localStorage.setItem('ACCESS_TOKEN', receivedToken);
                    setStatus("Logged in!");
                    navigate('/user'); // Redirect to User page on successful login
                } else {
                    localStorage.removeItem('ACCESS_TOKEN');
                    setStatus("Login failed: No token received.");
                }
            })
            .catch(err => {
                console.error("Login failed:", err);
                setStatus("Login failed.");
            });
    }

    function registSubmit(event: React.FormEvent) {
        event.preventDefault();

        const user = {
            name: nameRef.current?.value || "",
            email: emailRef.current?.value || "",
            password: passwordRef.current?.value || "",
        };

        axios.post(`http://localhost:8000/api/register`, user)
            .then(res => {
                if (res.status === 200) {
                    setStatus("Registration successful!");
                    setIsLogin(true);  // Switch to login view
                }
            })
            .catch(err => {
                console.error("Registration failed:", err);
                setStatus("Registration failed.");
            });
    }

    return (
        <>
            {isLogin ? (
                <div>
                    <form onSubmit={loginSubmit}>
                        <input ref={emailRef} type="email" placeholder="Email" /><br />
                        <input ref={passwordRef} type="password" placeholder="Password" /><br />
                        <button type="submit">Log In</button>
                    </form>
                    <button onClick={() => setIsLogin(false)}>Register</button>
                </div>
            ) : (
                <div>
                    <form onSubmit={registSubmit}>
                        <input ref={nameRef} type="text" placeholder="Name" /><br />
                        <input ref={emailRef} type="email" placeholder="Email" /><br />
                        <input ref={passwordRef} type="password" placeholder="Password" /><br />
                        <button type="submit">Submit</button>
                    </form>
                    <button onClick={() => setIsLogin(true)}>Back</button>
                </div>
            )}
            <div>{status}</div>
        </>
    );
}
