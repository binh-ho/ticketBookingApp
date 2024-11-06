import React from 'react';
import { Route, Routes } from 'react-router-dom';
import Login from './Components/Login'; // Adjust the import path
import UserTicket from './Components/UserTicket';
import BookingTicket from './Components/BookingTicket'; // Component to navigate to after login

function App() {
  return (
    <Routes>
      <Route path="/" element={<Login />} />
      <Route path="/login" element={<Login />} />
      <Route path="/user" element={<UserTicket />} />
      <Route path="/booking" element={<BookingTicket />} />
    </Routes>
  );
}

export default App;
